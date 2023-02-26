<main id="stationlist" class="container shadow">
    <x-offcanvas>
        <form>
            <div class="mb-3 col-12">
                <div class="input-group">
                    <select class="form-select" v-model="params.order" required>
                        <option v-for="(ordertype,index) in ordertypes" :key="index" :label="ordertype" :value="index">@{{ ordertype }}</option>
                        <option value="0" label="未选择排序方式" disabled="disabled">未选择排序方式</option>
                    </select>
                    <button type="button" class="btn btn-outline-info" @click="reset">重置 <i class="bi bi-arrow-clockwise"></i></button>
                    <button type="button" class="btn btn-outline-success" data-bs-dismiss="offcanvas"   @click="getData(params)">查询 <i class="bi bi-search"></i></button>
                </div>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" id="paramsname" class="form-control" v-model="params.name" placeholder="站点名称">
                <label for="paramsname">站点名称</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" id="paramspdes" class="form-control" v-model="params.pdes" placeholder="站点服务">
                <label for="paramspdes">站点服务</label>
            </div>
        </form>
    </x-offcanvas>
    <x-modal id='info' title="站点信息">
        <div v-if="station!==null" class="modal-body" style="word-wrap:break-word;word-break:break-all;over-flow:hidden;">
            <div class="modal-body" style="text-align:left;">
                编号：@{{ station.sid }}<br>
                状态：@{{ station.sstate==='o'?"开放":"关闭" }}<br>
                名称：@{{ station.sname }}<br>
            </div>
        </div>
    </x-modal>
    <div id="container" style="width:100%;height:600px"></div>

    <div v-if="stations.length>0" style="width: 300px;float: right;position: fixed;top: 4rem;right: 1rem;">
        <div class="item thead-dark thead">
            <p id='cityinfo' style="text-align:center;margin-bottom:0px"></p>
            <!-- <div class="row">
                @if (isset($utype)&&$utype==='a')
                <div class="col-1" @click="checkall"><a class="btn btn-outline-dark"><i class="bi bi-check-lg"></i></a></div>
                <div class="col-11 text-center row">
                @else
                <div class="col-12 text-center row">
                @endif
                    <div class="d-none d-md-block col-md-1">#</div>
                    <div class="col-5">名称</div>
                    <div class="col-3">地址</div>
                    <div class="d-none d-sm-block col-sm-1">服务</div>
                </div>
            </div> -->
        </div>
        <div class="row item list-group-item list-group-item-action " v-for="(station,index) in stations" style="display: flex;" :class="{'active':check.includes(station.sid)}" data-bs-toggle="modal" :key="index" :data-bs-index="index" 
        @if (isset($utype)&&$utype==="a")
                title="点击编辑该站点" data-bs-target="#alter" :data-bs-sid="station.sid" 
        @else
                title="点击查看站点信息" data-bs-target="#info" @click="openinfo($event)"
        @endif 
        >
        @if (isset($utype)&&$utype==='a')
            <div class="col-1" >
                <input type="checkbox" :value="station.sid" v-model="check" >
                <a v-if="station.ptype!=='d'" class="btn btn-danger" @click="del(index)" style="margin-left:20px; font-size:xx-small;"><i class="bi bi-trash3-fill"></i></a>
                <a v-else class="btn btn-success" @click="recover(index)" style="margin-left:20px; font-size:xx-small;" ><i class="bi bi-arrow-repeat"></i></a>
            </div>
        @endif
            <div @if (isset($utype)&&$utype==="a") class="text-center col-11" @else class="text-center col-12" @endif >
                <div class="row">
                    <div class="col-2 thead" >@{{ station.sid }}</div>
                    <div class="col-10">
                        <a class="btn btn-light text-truncate" :title="station.sname" :href="'/station/'+station.sid" style="width: 95%;"> @{{ station.sname }} </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8 text-truncate" style="font-size:12px;color:grey;align-self: self-end;text-align: left;" :title="station.sinfo.addr">@{{ station.sinfo.addr }}</div>
                    <div class="col-4" style="align-self:center;text-align: right;" v-html="station.service"></div>
                </div>
            </div>
        </div>

        @include('template.paginator')
    </div>
    <p v-if="stations.length===0">抱歉，查询不到任何站点！</p>
</main>


    <script>
        function setADDR(mapObj=null){
            var citysearch = new AMap.CitySearch();
            citysearch.getCityByIp("{{ $ip }}",function(status, result) {
            if (status === 'complete' && result.info === 'OK') {
                if (result && result.city && result.bounds) {
                    console.log(result);
                    var cityinfo = result.city;
                    document.getElementById('cityinfo').innerHTML = '<i class="bi bi-geo-alt-fill"></i> 您当前所在城市：'+cityinfo;
                    if(mapObj){
                        var citybounds = result.bounds;
                        mapObj.setBounds(citybounds);
                    }
                }
            } else {
                document.getElementById('cityinfo').innerHTML = '<i class="bi bi-geo-alt"></i> 获取不到您所在城市！';
            }
        });}
        AMapLoader.load({
            "key": "e9740f0d7d50ec4897813769d4551f76",              // 申请好的Web端开发者Key，首次调用 load 时必填
            "version": "2.0",   // 指定要加载的 JSAPI 的版本，缺省时默认为 1.4.15
            "plugins": ['AMap.Geolocation','AMap.CitySearch','AMap.Scale'],           // 需要使用的的插件列表，如比例尺'AMap.Scale'等
            "AMapUI": {             // 是否加载 AMapUI，缺省不加载
                "version": '1.1',   // AMapUI 版本
                "plugins":['overlay/SimpleMarker'],       // 需要加载的 AMapUI ui插件
            },
            "Loca":{                // 是否加载 Loca， 缺省不加载
                "version": '2.0'  // Loca 版本
            },
        }).then((AMap)=>{
            var mapObj = new AMap.Map('container', {
                resizeEnable: true,
                center:[125.28553076474091,43.82885381798033],
                zoom: 13
            });
            mapObj.plugin('AMap.Geolocation', function() {
                var geolocation = new AMap.Geolocation({
                    enableHighAccuracy: true,
                    timeout: 10000,
                    offset: [10, 20],
                    zoomToAccuracy: true,     
                    position: 'RB'
                })
                mapObj.addControl(geolocation);
                geolocation.getCurrentPosition();
                AMap.Event.addListener(geolocation, 'complete', onComplete);//返回定位信息
                AMap.Event.addListener(geolocation, 'error', onError);      //返回定位出错信息

                // geolocation.getCityInfo(function(status,result){
                //         if(status=='complete'){
                //             onComplete(result)
                //         }else{
                //             onError(result)
                //         }
                //         alert(JSON.stringify(result));
                // });

                function onComplete (data) {
                    mapObj.addControl(new AMap.Scale());
                    setADDR();
                }

                function onError (data) {
                    mapObj.addControl(new AMap.Scale());
                    setADDR(mapObj);
                }
            })
        }).catch((e)=>{
            console.error(e);  //加载错误提示
        });
        
        const stationlist=Vue.createApp({
            data(){
                return{
                    dataname:"stations",
                    @if (isset($utype)&&$utype==="a")
                    url:"{{ config('var.asl') }}",
                    utype:'a',
                    @else
                    utype:'u',
                    url:"{{ config('var.sl') }}",
                    @endif
                    paginator:{},
                    stations:[],
                    pagenum:{{ $config_station['pagenum'] }},
                    station:null,
                    check:[],
                    
                    paramspre:{
                        page:"1",
                        city:"",
                        region:"",
                        order:"0",
                    },
                    params:{
                        page:"1",
                        city:"",
                        region:"",
                        order:"0",
                    },
                    ordertypes:{
                        num:"按剩余人数排序",
                        len:"按距离排序",

                    },
                }
            },
            mounted(){
                initpaginator(this);
                this.getData();
            },
            methods:{
                del(index){
                    let station=this.stations[index];
                    let that=this;
                    getData('{!! config('var.sd') !!}'+station.sid+'?utype='+this.utype,function(json){
                        if(json.status===1){
                            that.stations[index].ptype="d";
                        }
                    },"#msg");
                },
                recover(index){
                    let station=this.stations[index];
                    let that=this;
                    getData('{!! config('var.pr') !!}'+station.sid+'?utype='+this.utype,function(json){
                        if(json.status===1){
                            that.stations[index].ptype="m";
                        }
                    },"#msg");
                },
                openinfo(event){
                    this.station = Object.assign({},this.stations[event.currentTarget.getAttribute('data-bs-index')]);
                },
                checkall(){
                    let flag=true;
                    for(station of this.stations){
                        if(!this.check.includes(station.sid)){
                            this.check.push(station.sid);
                            flag=false;
                        }
                    }
                    if(flag===true){
                        this.check.length=0;
                    }
                },
                reset(){
                    this.params=this.paramspre={
                        page:"1",
                        city:"",
                        region:"",
                        order:"0",
                    };
                },
            }
        }).mount('#stationlist');
    </script>