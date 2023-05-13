<a class="btn btn-outline-dark " title="点击隐藏站点列表" style="font-size:x-large;position:fixed;right:0px;top:30%;z-index:1000;border:0px;" onclick="switchlist(this)"><i class="bi bi-eye-slash-fill"></i></a>
<main id="stationlist" class="container shadow list" style="position:relative;background-color: #ffffff80">
    <!--侧边栏-->
    <x-offcanvas>
        <form>
            <div class="mb-3 col-12">
                <div class="input-group">
                    <select class="form-select" v-model="params.order" required>
                        <option v-for="(ordertype,index) in ordertypes" :key="index" :label="ordertype" :value="index">@{{ ordertype }}</option>
                    </select>
                    <button type="button" class="btn btn-outline-dark" @click="set('desc')" :title="params.desc==='0'?'正序【从小到大】':'倒序【从大到小】'"><i class="bi" :class="{'bi-sort-up-alt':params.desc==='0','bi-sort-up':params.desc==='1'}" ></i></button>
                    <button type="button" class="btn btn-outline-info" @click="reset">重置 <i class="bi bi-arrow-clockwise"></i></button>
                    <button type="button" class="btn btn-outline-success" data-bs-dismiss="offcanvas"   @click="getData(params)">查询 <i class="bi bi-search"></i></button>
                </div>
            </div>
            <div class="mb-3 col-12 form-floating">
                <select class="form-select" v-model="params.service" id="paramsservice">
                    <option v-for="(item,index) in services" :key="index" :label="item.label" :value="index">@{{ item.label }}</option>
                    <option value="" label="所有服务">所有服务</option>
                </select>
                <label for="paramsservice">服务</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input id="paramsatime" type="date" class="form-control" v-model="params.atime" placeholder="">
                <label for="paramsatime">时间【筛选在该天开放的站点】</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" id="paramssname" class="form-control" v-model="params.sname" placeholder="站点名称">
                <label for="paramssname">站点名称</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" id="paramsaddr" class="form-control" v-model="params.addr" placeholder="站点地址">
                <label for="paramsaddr">站点地址</label>
            </div>

            <div v-if="state_ids!==undefined&&state_ids.length>0" class="mb-3 col-12 form-floating">
                <select class="form-select" v-model="params.state_id" @change="getCities('','',1)" id="state_id" required>
                    <option v-for="state_id in state_ids" :key="state_id.id" :label="state_id.cname" :value="state_id.id">@{{ state_id.cname }}</option>
                    <option label="请选择省市区" value="">请选择省市区</option>
                </select>
                <label for="state_id" class="form-label">省市区</label>
            </div>
            <div v-if="city_ids!==undefined&&city_ids.length>0" class="mb-3 col-12 form-floating">
                <select class="form-select" v-model="params.city_id" @change="getRegions('',1)" id="city_id" required>
                    <option v-for="city_id in city_ids" :key="city_id.id" :label="city_id.cname" :value="city_id.id">@{{ city_id.cname }}</option>
                    <option label="请选择地级市" value="">请选择地级市</option>
                </select>
                <label for="city_id" class="form-label">地级市</label>
            </div>
            <div v-if="region_ids!==undefined&&region_ids.length>0" class="mb-3 col-12 form-floating">
                <select class="form-select" v-model="params.region_id" id="region_id" @change="getStreet(1)"  required>
                    <option v-for="region_id in region_ids" :key="region_id.id" :label="region_id.cname" :value="region_id.id">@{{ region_id.cname }}</option>
                    <option label="请选择区县" value="">请选择区县</option>
                </select>
                <label for="region_id" class="form-label">区县</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" id="paramsdes" class="form-control" v-model="params.des" placeholder="站点描述">
                <label for="paramsdes">站点描述</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" id="paramstime" class="form-control" v-model="params.time" placeholder="开放时间描述">
                <label for="paramstime">开放时间描述</label>
            </div>
        </form>
    </x-offcanvas>
    <p id='cityinfo' style="text-align:center;margin-bottom:0px"></p>
    <!--显示每个站点的信息-->
    <div v-if="stations.length>0">
        <!--站点数大于0，可以双击查看站点信息-->
        <x-modal id='info' title="站点信息">
            <div v-if="station!==null" style="word-wrap:break-word;word-break:break-all;over-flow:hidden;">
                    ID：@{{ station.sid }}<br>
                    名称：@{{ station.sname }}<br>
                    地址：@{{ station.sinfo.addr }}<br>
                    时间：@{{ station.sinfo.time }}<br>
                    预约人数：@{{ station.anum }}<br>
                    状态：@{{ station.sstate==='o'?"开放":"关闭" }}<br><br>
            </div>
        </x-modal>

        <div v-for="(station,index) in stations" :key="index" class="row item list-group-item list-group-item-action"  style="display: flex;background-color: #ffffff80" :class="{'active':check.includes(station.sid),'delete':station.sstate==='d'}" >
           <!--显示站点图片-->
            <div class="col-2" style="align-self: center;max-width: 20%;text-align: right;">
                @if (isset($utype)&&$utype==='a')
                <!--如果是管理员，显示站点图片前还要有个选择框-->
                <input v-if="station.editable===true" type="checkbox" class="form-check-input" :value="station.sid" v-model="check" style="float: left;position: relative;left: 10px;">
                @endif
                <div style="float:right;text-align:center;height:48px;width:48px;">
                    <img :src="station.img" alt="站点图片" height="48" width="48" style="font-size: 46px;border-radius:5px;" >
                </div>
            </div>
            <!--站点显示-->
            <div class="col-10 row d-flex justify-content-between" style="max-width: 75%;">
                <div class="col-8" data-bs-toggle="modal" style="text-align:left;" 


    @if (isset($utype)&&$utype==="a")
            :title="station.editable===true?'双击编辑站点信息':'双击查看站点信息'" @dblclick="openalter(index)" 
    @else
            title="双击查看站点信息" @dblclick="openinfo(index)"
    @endif>
                    <h5 class="mb-0 text-truncate" :title="station.sname"> <span v-if="station.sstate==='c'" class="badge bg-dark"> <i class="bi bi-building-slash"></i> </span>@{{ station.sname }} </h5>
                    <h6 class="mb-0 opacity-75 input-group text-truncate" :title="station.sinfo.des"><a v-if="station.sinfo.p==='1'" class="badge btn" title="筛选服务：核酸检测" :class="{'btn-outline-info text-dark':params.service!=='p'&&params.service!=='','btn-info':params.service==='p'||params.service===''}" @click="set('service','p')"  >核酸</a>
                    <a v-if="station.sinfo.a==='1'" class="badge btn" title="筛选服务：抗原检测" :class="{'btn-outline-warning text-dark':params.service!=='a'&&params.service!=='','btn-warning':params.service==='a'||params.service===''}" @click="set('service','a')">抗原</a>
                    <a v-if="station.sinfo.v==='1'" class="badge btn" title="筛选服务：疫苗接种" :class="{'btn-outline-success text-dark':params.service!=='v'&&params.service!=='','btn-success':params.service==='v'||params.service===''}" @click="set('service','v')">疫苗</a>
                    <a v-if="station.sinfo.r==='1'" class="badge btn" title="筛选服务：报备" :class="{'btn-outline-danger text-dark':params.service!=='r'&&params.service!=='','btn-danger':params.service==='r'||params.service===''}" @click="set('service','r')">报备</a>&nbsp;@{{ getstationdes(station.sinfo.des) }}</h6>
                    <span class="opacity-65 text-nowrap text-truncate mb-0" style="font-size: small;">
                        <i class="bi bi-person-fill text-info"></i>@{{ getNumstr(index) }}
                        <i class="badge bg-primary">@{{ station.sinfo.addr }}</i>&nbsp;
                        <i class="bi bi-calendar-event text-info"></i> @{{ station.sinfo.time }}
                    </span>
                </div>
                <!--用户端是查看，管理端还有删除和恢复-->
                <p class="col-4 opacity-75 text-nowrap">
                    <!--双击查看，跳转到/station/'+station.sid-->
                    <a v-if="station.sstate!=='d'" class="btn rounded-pill btn-outline-primary" :href="'/station/'+station.sid" target="_blank"><i class="bi bi-building"></i> 查看</a>
                    @if(isset($utype)&&$utype==='a')
                    <!--如果是管理员端，则可以调佣del和recover函数-->
                    <a v-if="station.editable===true&&station.sstate!=='d'"  class="btn rounded-pill btn-outline-danger" @click="del(index)"><i class="bi bi-building-x"></i> 删除</a>
                    <a v-if="station.editable===true&&station.sstate==='d'"  class="btn rounded-pill btn-success" @click="recover(index)"><i class="bi bi-building-check"></i> 恢复</a>
                    @endif
                </p>
            </div>
        </div>
        <!--分页-->
        @include('template.paginator')
    </div>
    <p v-if="stations.length===0">抱歉，查询不到任何站点！</p>
</main>
    <!--显示地图-->
    <div id="container" style="width:100%;height:100%;position: fixed;top: 0px;left: 0px;z-index:-1"></div>

    <script>
        function switchlist(app){
            if($("#stationlist").css('display')==='none'){
                $("#stationlist").show();
                app.innerHTML='<i class="bi bi-eye-slash-fill"></i>';
                app.title="点击隐藏站点列表";
            }else{
                $("#stationlist").hide();
                app.innerHTML='<i class="bi bi-eye-fill"></i>';
                app.title="点击显示站点列表";
            }
        }
        const stationlistapp=Vue.createApp({
            data(){
                return{
                    mapObj:null,
                    district:null,
                    polygons:[],
                    markers:[],
                    dataname:"stations",
                    @if (isset($utype)&&$utype==="a")
                    url:"{{ config('var.asl') }}",
                    @else
                    url:"{{ config('var.sl') }}",
                    @endif
                    paginator:{},
                    stations:[],
                    pagenum:{{ $config_station['pagenum'] }},
                    station:null,
                    check:[],
                    
                    params:{
                        utype:"a",
                        page:"1",
                        service:"",
                        state_id:"",
                        city_id:"",
                        region_id:"",
                        sname:"",
                        des:"",
                        time:"",
                        addr:"",
                        order:"num",
                        desc:"1",
                        lng:111,
                        lat:33,
                        atime:toDate(new Date().getTime()+86400000),
                    },
                    paramsinit:{
                        utype:"a",
                        page:"1",
                        service:"",
                        state_id:"",
                        city_id:"",
                        region_id:"",
                        sname:"",
                        des:"",
                        time:"",
                        addr:"",
                        order:"num",
                        desc:"1",
                        lng:111,
                        lat:33,
                        atime:toDate(new Date().getTime()+86400000),
                    },
                    paramspre:{},
                    ordertypes:{
                        num:"按可预约人数",
                        anum:"按预约人数",
                        len:"按距离",
                    },
                    services:{!! json_encode($config_station['type'],JSON_UNESCAPED_UNICODE) !!},
                    state_ids:[],
                    city_ids:[],
                    region_ids:[],
                }
            },
            mounted(){
                //初始化地图
                this.initMap();
                //初始化分页
                initpaginator(this);
                setTimeout(()=>{
                    //初始化地址
                    initaddress(this,"{!! config('var.sla') !!}",this.params);
                    this.getGeoAddr();
                    this.getData();
                },500);
            },
            methods:{
                //设置地址
                setADDR(ip,cityinfoid,mapObj){
                    const that = this;
                    const citysearch = new AMap.CitySearch();
                    citysearch.getCityByIp(ip,function(status, result) {
                        //上边显示的一栏
                    if (status === 'complete' && result.info === 'OK') {
                        if (result && result.city && result.bounds) {
                            console.log(result);
                            const cityinfo = result.city;
                            document.getElementById(cityinfoid).innerHTML = '<i class="bi bi-geo-alt-fill"></i> 您当前所在城市：'+cityinfo;
                            //alert(JSON.stringify(result));
                            if(mapObj){
                                const citybounds = result.bounds;
                                mapObj.setBounds(citybounds);
                                mapObj.setZoom(13);
                            }
                            getData("{!! config('var.sga') !!}"+"?type=c&code="+result.adcode,function(json){
                                that.params.city_id=json.cid;
                                that.params.state_id=json.sid;
                                that.getCities(json.cid,'',1);
                                that.getData();
                            });
                        }
                    } else {
                        document.getElementById(cityinfoid).innerHTML = '<i class="bi bi-geo-alt"></i> 获取不到您所在城市！';
                    }
                });},
                getGeoAddr(){
                    const that = this;
                    this.mapObj = new AMap.Map('container', {
                        resizeEnable: true,
                        center:[125.28553076474091,43.82885381798033],
                        zoom: 13
                    });
                    this.mapObj.plugin('AMap.Geolocation', function() {
                        var geolocation = new AMap.Geolocation({
                            enableHighAccuracy: true,
                            timeout: 10000,
                            offset: [10, 20],
                            zoomToAccuracy: true,     
                            position: 'RB'
                        });
                        that.mapObj.addControl(geolocation);
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
                            that.mapObj.addControl(new AMap.Scale());
                            //alert(JSON.stringify(data));
                            that.setADDR("{{$ip}}","cityinfo",null);
                        }
                        function onError (data) {
                            that.mapObj.addControl(new AMap.Scale());
                            //alert(JSON.stringify(data));
                            that.setADDR("{{$ip}}","cityinfo",that.mapObj);
                        }
                    });
                },

                //初始化地图
                initMap(){
                    const that = this;
                    AMapLoader.load({
                        "key": "e9740f0d7d50ec4897813769d4551f76",              // 申请好的Web端开发者Key，首次调用 load 时必填
                        "version": "2.0",   // 指定要加载的 JSAPI 的版本，缺省时默认为 1.4.15
                        "plugins": ['AMap.Geolocation','AMap.CitySearch','AMap.DistrictSearch','AMap.Scale'],           // 需要使用的的插件列表，如比例尺'AMap.Scale'等
                        "AMapUI": {             // 是否加载 AMapUI，缺省不加载
                            "version": '1.1',   // AMapUI 版本
                            "plugins":['overlay/SimpleMarker'],       // 需要加载的 AMapUI ui插件
                        },
                        "Loca":{                // 是否加载 Loca， 缺省不加载
                            "version": '2.0'  // Loca 版本
                        },
                    }).then((AMap)=>{
                        var opts = {
                            subdistrict: 1,   //返回下一级行政区
                            showbiz:false  //最后一级返回街道信息
                        };
                        this.district = new AMap.DistrictSearch(opts);
                        this.district.search('中国', function(status, result) {
                            if(status=='complete'){
                                getPolygon(that,result.districtList[0]);
                            }
                        });

                    }).catch((e)=>{
                        console.error(e);  //加载错误提示
                    });
                },
                getNumstr(index){
                    let num=(this.params.service==='r'?this.stations[index].sinfo.rnum:(this.params.service==='v'?this.stations[index].sinfo.vnum:this.stations[index].sinfo.pnum));
                    return this.stations[index].anum + " / " +(num===-1?"无限制":num);
                },

                //打开公告详情拟态框
                openinfo(index){
                    $('#info').modal("show");
                    this.station = Object.assign({},this.stations[index]);
                },
                openalter(index){
                    if(this.stations[index].editable===true){
                        alterapp.index=index;
                        alterapp.sid=this.stations[index].sid;
                        $('#alter').modal("show");
                    }else{
                        this.openinfo(index);
                    }
                },

                //全选
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

                //重置
                reset(){
                    for(let i in this.paramsinit){
                        if(typeof this.paramsinit[i] === 'string'){
                            this.params[i]=this.paramspre[i]=this.paramsinit[i];
                        }else{
                            this.params[i]=Object.assign({},this.paramsinit[i]);
                            this.paramspre[i]=Object.assign({},this.paramsinit[i]);
                        }
                    }
                },

                getstationdes(des){
                    const tmp = document.createElement("div");
                    tmp.innerHTML=des;
                    return tmp.innerText;
                },

                //删除站点
                del(index){
                    let station=this.stations[index];
                    let that=this;
                    getData('{!! config('var.asd') !!}'+station.sid,function(json){
                        if(json.status===1){
                            that.stations[index].sstate="d";
                        }
                    },"#msg");
                },

                //恢复站点
                recover(index){
                    let station=this.stations[index];
                    let that=this;
                    getData("{!! config('var.asr') !!}"+station.sid,function(json){
                        if(json.status===1){
                            that.stations[index].sstate="c";
                        }
                    },"#msg");
                },
            }
        }).mount('#stationlist');
    </script>