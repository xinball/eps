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
                <select class="form-select" v-model="params.service" id="paramsservice">
                    <option v-for="(item,index) in services" :key="index" :label="item" :value="index">@{{ item }}</option>
                    <option value="0" disabled="disabled" label="未选择服务">未选择服务</option>
                </select>
                <label for="paramsservice">服务</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input id="paramsatime" type="datetime-local" class="form-control" v-model="params.atime" placeholder="">
                <label for="paramsatime">时间【筛选在该时间开放的站点】</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" id="paramsname" class="form-control" v-model="params.name" placeholder="站点名称">
                <label for="paramsname">站点名称</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" id="paramsaddr" class="form-control" v-model="params.addr" placeholder="站点服务">
                <label for="paramsaddr">站点地址</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" id="paramsdes" class="form-control" v-model="params.des" placeholder="站点服务">
                <label for="paramsdes">站点描述</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" id="paramstime" class="form-control" v-model="params.time" placeholder="开放时间描述">
                <label for="paramstime">开放时间描述</label>
            </div>
        </form>
    </x-offcanvas>

    <p id='cityinfo' style="text-align:center;margin-bottom:0px"></p>
    <div id="container" style="width:100%;height:600px"></div>
    <!--显示每个站点的信息-->
    <div v-if="stations.length>0">
        <!--站点数大于0，可以点击查看站点信息-->
        <x-modal id='info' title="站点信息">
            <div v-if="station!==null" class="modal-body" style="word-wrap:break-word;word-break:break-all;over-flow:hidden;">
                <div class="modal-body" style="text-align:left;">
                    ID：@{{ station.sid }}<br>
                    名称：@{{ station.sname }}<br>
                    地址：@{{ station.sinfo.addr }}<br>
                    时间：@{{ station.sinfo.time }}<br>
                    预约人数：@{{ station.num }}<br>
                    状态：@{{ station.sstate==='o'?"开放":"关闭" }}<br>
                </div>
            </div>
        </x-modal>

        <div v-for="(station,index) in stations" :key="index" class="row item list-group-item list-group-item-action" :class="{'active':check.includes(station.sid)}" style="display: flex;">
            <div class="col-2" style="align-self: center;max-width: 20%;text-align: right;">
                @if (isset($utype)&&$utype==='a')
                <input type="checkbox" :value="station.sid" v-model="check" style="float: left;position: relative;left: 10px;">
                @endif
                <div style="float:right;text-align:center;height:48px;width:48px;">
                    <img :src="station.img" alt="站点图片" height="48" width="48" style="font-size: 46px;border-radius:5px;" >
                </div>
            </div>
            <div class="col-10 row d-flex justify-content-between" style="max-width: 75%;">
                <div class="col-8" data-bs-toggle="modal" style="text-align:left;" 


    @if (isset($utype)&&$utype==="a")
            title="点击编辑站点信息" data-bs-target="#alter" :data-bs-cid="station.sid"
    @else
            title="点击查看站点信息" data-bs-target="#info" @click="openinfo(index)"
    @endif>
                    

    <!--站点显示-->
                    <h5 class="mb-0 text-truncate" :title="station.sname"> @{{ station.sname }} <span v-if="station.sinfo.p===true" class="badge bg-info">核酸</span><span v-if="station.sinfo.r===true" class="badge bg-warning">抗原</span><span v-if="station.sinfo.v===true" class="badge bg-success">疫苗</span></h5>
                    <h6 class="mb-0 opacity-75 text-truncate" :title="station.sinfo.des"><span v-if="station.sstate==='c'" class="badge bg-warning" style="font-size:x-small;">测试</span> @{{ station.sinfo.des }}</h6>
                    <span class="opacity-65 text-nowrap text-truncate mb-0" style="font-size: small;">
                        <i class="bi bi-person-fill text-info"></i>@{{ station.num + " / " +(params.service==='r'?station.sinfo.rnum:(params.service==='v'?station.sinfo.vnum:station.sinfo.pnum)) }}
                        <i class="badge bg-dark">@{{ station.sinfo.addr }}</i>&nbsp;
                        <i class="bi bi-calendar-event text-info"></i> @{{ station.sinfo.time }}
                    </span>
                </div>
                <!--用户端是查看，管理端还有删除和恢复-->
                <p class="col-4 opacity-75 text-nowrap ">
                    <a class="btn rounded-pill btn-outline-primary" :href="'/station/'+station.sid">查看</a>
                    @if(isset($utype)&&$utype==='a')
                    <a v-if="station.sstate!=='d'"  class="btn rounded-pill btn-outline-danger" @click="del(index)">删除</a>
                    <a v-if="station.sstate==='d'"  class="btn rounded-pill btn-outline-success" @click="recover(index)">恢复</a>
                    
                    @endif
                </p>
            </div>
        </div>
        <!--分页-->
        @include('template.paginator')
    </div>
    <p v-if="stations.length===0">抱歉，查询不到任何站点！</p>
</main>


    <script>

        //地图
        function setADDR(mapObj=null){
            var citysearch = new AMap.CitySearch();
            citysearch.getCityByIp("{{ $ip }}",function(status, result) {
                //上边显示的一栏
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
        //加载地图
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
                    
                    params:{
                        page:"1",
                        service:"p",
                        city:"",
                        region:"",
                        order:"num",
                        lng:111,
                        lat:33,
                        atime:"2023-03-06T00:00:00",
                        time:"",
                        addr:"",
                    },
                    paramspre:{},
                    ordertypes:{
                        num:"按剩余人数排序",
                        len:"按距离排序",

                    },
                    services:{
                        p:"核酸检测服务",
                        r:"抗原检测服务",
                        v:"疫苗接种服务",
                    }
                }
            },
            mounted(){
                initpaginator(this);
                this.getData();
            },
            methods:{
                openinfo(index){
                    this.station = Object.assign({},this.stations[index]);
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
                del(index){
                    let station=this.stations[index];
                    let that=this;
                    getData('{!! config('var.asd') !!}'+station.sid,function(json){
                        if(json.status===1){
                            that.stations[index].sstate="d";
                        }
                    },"#msg");
                },
                recover(index){
                    let station=this.stations[index];
                    let that=this;
                    getData('{!! config('var.asr') !!}'+station.sid,function(json){
                        if(json.status===1){
                            that.stations[index].sstate="d";
                        }
                    },"#msg");
                },

/*
                apply(index){
                    let appoint=this.appoints[index];
                    let that=this;
                    getData('{!! config('var.pa') !!}'+appoint.aid,function(json){
                        if(json.status===1){
                            that.appoints[index].astate="s";
                        }
                    },"#msg");
                },
                cancel(index){
                    let appoint=this.appoints[index];
                    let that=this;
                    getData('{!! config('var.pc') !!}'+appoint.aid,function(json){
                        if(json.status===1){
                            that.appoints[index].astate="n";
                        }
                    },"#msg");
                },
                approve(index){
                    let appoint=this.appoints[index];
                    let that=this;
                    getData('{!! config('var.apa') !!}'+appoint.aid,function(json){
                        if(json.status===1){
                            that.appoints[index].astate="f";
                        }
                    },"#msg");
                },
                refuse(index){
                    let appoint=this.appoints[index];
                    let that=this;
                    getData('{!! config('var.acrf') !!}'+appoint.aid,function(json){
                        if(json.status===1){
                            that.appoints[index].astate="r";
                        }
                    },"#msg");
                },
                */
            }
        }).mount('#stationlist');
    </script>