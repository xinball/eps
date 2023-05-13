    <x-modal id="alter" class="modal-fullscreen ckeditor" title="编辑预约/报备">    
        <div class="text-center p-4 pb-4">
            <h4 class="mb-3"><i class="bi bi-receipt"></i> 预约/报备信息</h4>
            <div class="row g-3">
                <div class="col-4">
                    <div class="input-group">
                        <div class="form-floating">
                            <input type="number" class="form-control" min="1" id="altersid" v-model="appoint.sid" placeholder="站点编号" @change="getStation" disabled>
                            <label for="altersid" class="form-label">站点编号</label>
                        </div>
                    </div>
                </div>
@if(!isset($type))
                <div class="col-4">
                    <div class="form-floating">
                        <select class="form-select" v-model="appoint.atype" id="alteratype">
                            <option v-for="(item,index) in services" :key="index" :label="item.label" :value="index">@{{ item.label }}</option>
                        </select>
                        <label for="alteratype">服务</label>
                    </div>
                </div>
@else
                <div class="col-4">
                    <div class="form-floating">
                        <select class="form-select" v-model="appoint.atype" id="alteratype" disabled>
                            <option label="报备服务" value="r">报备服务</option>
                            </select>
                        <label for="alteratype">服务</label>
                    </div>
                </div>
@endif
                <div class="col-4">
                    <div class="form-floating">
                        <input type="datetime-local" class="form-control" id="alteratime" v-model="appoint.atime" placeholder="预约/报备时间">
                        <label for="alteratime" class="form-label">预约/报备时间</label>
                    </div>
                </div>
                <div v-if="appoint.atype==='r'" class="col-12 input-group">
                    <span class="input-group-text">起点</span>
                    <div v-show="from.state_ids!==undefined&&from.state_ids.length>0" class="form-floating">
                        <select class="form-select" v-model="from.state_id" @change="from.getCities()" id="fromstate_idalter" required>
                            <option label="请选择省市区" value="">请选择省市区</option>
                            <option v-for="state_id in from.state_ids" :key="state_id.id" :label="state_id.cname" :value="state_id.id">@{{ state_id.cname }}</option>
                        </select>
                        <label for="fromstate_idalter" class="form-label">省市区</label>
                    </div>
                    <div v-show="from.city_ids!==undefined&&from.city_ids.length>0" class="form-floating">
                        <select class="form-select" v-model="from.city_id" @change="from.getRegions()" id="fromcity_idalter" required>
                            <option label="请选择地级市" value="">请选择地级市</option>
                            <option v-for="city_id in from.city_ids" :key="city_id.id" :label="city_id.cname" :value="city_id.id">@{{ city_id.cname }}</option>
                        </select>
                        <label for="fromcity_idalter" class="form-label">地级市</label>
                    </div>
                    <div v-show="from.region_ids!==undefined&&from.region_ids.length>0" class="form-floating">
                        <select class="form-select" v-model="from.region_id" id="fromregion_idalter" required>
                            <option label="请选择区县" value="">请选择区县</option>
                            <option v-for="region_id in from.region_ids" :key="region_id.id" :label="region_id.cname" :value="region_id.id">@{{ region_id.cname }}</option>
                        </select>
                        <label for="fromregion_idalter" class="form-label">区县</label>
                    </div>
                    <div class="form-floating">
                        <input type="text" id="fromaddralter" class="form-control" placeholder="详细地址" v-model="from.addr"/>
                        <label for="fromaddralter" class="form-label">详细地址</label>
                    </div>
                </div>
                <div v-if="appoint.atype==='r'" class="col-12 input-group">
                    <span class="input-group-text">终点</span>
                    <div v-show="to.state_ids!==undefined&&to.state_ids.length>0" class="form-floating">
                        <select class="form-select" v-model="to.state_id" @change="to.getCities()" id="tostate_idalter" required>
                            <option label="请选择省市区" value="">请选择省市区</option>
                            <option v-for="state_id in to.state_ids" :key="state_id.id" :label="state_id.cname" :value="state_id.id">@{{ state_id.cname }}</option>
                        </select>
                        <label for="tostate_idalter" class="form-label">省市区</label>
                    </div>
                    <div v-show="to.city_ids!==undefined&&to.city_ids.length>0" class="form-floating">
                        <select class="form-select" v-model="to.city_id" @change="to.getRegions()" id="tocity_idalter" required>
                            <option label="请选择地级市" value="">请选择地级市</option>
                            <option v-for="city_id in to.city_ids" :key="city_id.id" :label="city_id.cname" :value="city_id.id">@{{ city_id.cname }}</option>
                        </select>
                        <label for="tocity_idalter" class="form-label">地级市</label>
                    </div>
                    <div v-show="to.region_ids!==undefined&&to.region_ids.length>0" class="form-floating">
                        <select class="form-select" v-model="to.region_id" id="toregion_idalter" required>
                            <option label="请选择区县" value="">请选择区县</option>
                            <option v-for="region_id in to.region_ids" :key="region_id.id" :label="region_id.cname" :value="region_id.id">@{{ region_id.cname }}</option>
                        </select>
                        <label for="toregion_idalter" class="form-label">区县</label>
                    </div>
                    <div class="form-floating">
                        <input type="text" id="toaddralter" class="form-control" placeholder="详细地址" v-model="to.addr"/>
                        <label for="toaddralter" class="form-label">详细地址</label>
                    </div>
                </div>
                <div class="form-floating"> 
                    <input type="text" id="msgalter" class="form-control" v-model="msg" placeholder="预约/报备信息">
                    <label for="msgalter" class="form-label">预约/报备信息</label>
                </div>
            </div>
            <hr class="my-4"><br>
            <div v-if="station!==null">
            <h4 class="mb-3"><i class="bi bi-info-square-fill"></i> 站点信息</h4>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <label class="input-group-text">#@{{station.sid}}</label>
                        <div class="form-floating">
                            <input type="text" class="form-control" v-model="station.sname" placeholder="站点名称" disabled>
                            <label class="form-label">名称</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <label class="input-group-text"><i class="bi bi-calendar4-week"></i></label>
                        <div class="form-floating">
                            <input type="text" class="form-control" v-model="station.sinfo.time" placeholder="开放时间描述【不填写则根据时间配置自动生成】" disabled>
                            <label class="form-label">开放时间描述</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch form-check-inline">
                        <label class="form-check-label">预约/报备确认时间限制</label>
                        <input type="checkbox" class="form-check-input"  v-model="station.sinfo.approvetime" disabled>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch form-check-inline">
                        <label class="form-check-label">支持报备</label>
                        <input type="checkbox" class="form-check-input" v-model="station.sinfo.r" disabled>
                    </div>
                    <div v-show="station.sinfo.r===true" class="input-group">
                        <input type="number" class="form-control" min="0" v-model="station.sinfo.rnum" placeholder="报备人数/日【不填写表示无限制】" disabled>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch form-check-inline">
                        <label class="form-check-label">支持核酸检测</label>
                        <input type="checkbox" class="form-check-input" v-model="station.sinfo.p" disabled>
                    </div>
                    <div v-show="station.sinfo.p===true" class="input-group">
                        <input type="number" class="form-control" min="0" v-model="station.sinfo.pnum" placeholder="核酸检测人数/日【不填写表示无限制】" disabled>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch form-check-inline">
                        <label class="form-check-label">支持抗原检测</label>
                        <input type="checkbox" class="form-check-input" v-model="station.sinfo.a" disabled>
                    </div>
                    <div v-show="station.sinfo.a===true" class="input-group">
                        <input type="number" class="form-control" min="0" v-model="station.sinfo.anum" placeholder="抗原检测人数/日【不填写表示无限制】" disabled>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch form-check-inline">
                        <label class="form-check-label">支持疫苗接种</label>
                        <input type="checkbox" class="form-check-input" v-model="station.sinfo.v" disabled>
                    </div>
                    <div v-show="station.sinfo.v===true" class="input-group">
                        <input type="number" class="form-control" min="0" v-model="station.sinfo.vnum" placeholder="疫苗接种人数/日【不填写表示无限制】" disabled>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <label class="input-group-text"><i class="bi bi-globe2"></i></label>
                        <div class="form-floating">
                            <input type="number" class="form-control" step="0.000001" min="3" max="54" v-model="station.slat" placeholder="纬度(°N)" disabled>
                            <label class="form-label">纬度(°N)</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <label class="input-group-text"><i class="bi bi-globe"></i></label>
                        <div class="form-floating">
                            <input type="number" class="form-control" step="0.000001" min="73" max="136" v-model="station.slng" placeholder="经度(°E)" disabled>
                            <label class="form-label">经度(°E)</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <label class="input-group-text"><i class="bi bi-geo-alt"></i></label>
                        <div class="form-floating">
                            <input type="text" class="form-control" v-model="station.sinfo.addr" placeholder="站点地址描述" disabled>
                            <label class="form-label">地址描述</label>
                        </div>
                    </div>
                </div>
                <div v-show="state_ids!==undefined&&state_ids.length>0" class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" v-model="station.state_id" @change="getCities()" disabled>
                            <option label="请选择省市区" value="">请选择省市区</option>
                            <option v-for="state_id in state_ids" :key="state_id.id" :label="state_id.cname" :value="state_id.id">@{{ state_id.cname }}</option>
                        </select>
                        <label class="form-label">省市区</label>
                    </div>
                </div>
                <div v-show="city_ids!==undefined&&city_ids.length>0" class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" v-model="station.city_id" @change="getRegions()" disabled>
                            <option label="请选择地级市" value="">请选择地级市</option>
                            <option v-for="city_id in city_ids" :key="city_id.id" :label="city_id.cname" :value="city_id.id">@{{ city_id.cname }}</option>
                        </select>
                        <label class="form-label">地级市</label>
                    </div>
                </div>
                <div v-show="region_ids!==undefined&&region_ids.length>0" class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" v-model="station.region_id" disabled>
                            <option label="请选择区县" value="">请选择区县</option>
                            <option v-for="region_id in region_ids" :key="region_id.id" :label="region_id.cname" :value="region_id.id">@{{ region_id.cname }}</option>
                        </select>
                        <label class="form-label">区县</label>
                    </div>
                </div>
            </div>
            </div>
        </div>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">关闭</button>
            <button type="button" class="btn btn-outline-success" @click="alter()"><i class="bi bi-clipboard-plus"></i> 编辑预约/报备</button>
        </x-slot>
    </x-modal>
        <script>
            const alterapp=Vue.createApp({
                data() {
                    return{
                        index:0,
                        aid:0,
                        appoint:{
                            aid:"",
                            sid:"",
                            atype: "",
                            atime: "",
                            ainfo: [],
                        },
                        msg:"",
                        from:{
                            getCities(){},
                            getRegions(){},
                            state_id:"",
                            city_id:"",
                            region_id:"",
                            state_ids:[],
                            city_ids:[],
                            region_ids:[],
                        },
                        to:{
                            getCities(){},
                            getRegions(){},
                            state_id:"",
                            city_id:"",
                            region_id:"",
                            state_ids:[],
                            city_ids:[],
                            region_ids:[],
                        },
                        station:null,
                        state_ids:[],
                        city_ids:[],
                        region_ids:[],
@if(!isset($type))
                        services:{!! json_encode($config_station['typep'],JSON_UNESCAPED_UNICODE) !!},
@endif
                    }
                },
                mounted(){
                    this.init();
                },
                methods:{
                    alter(){
                        let ainfo="{}";
                        if(this.appoint.atype==='r'){
                            ainfo = JSON.stringify({
                                msg:this.msg,
                                fstate_id:this.from.state_id,
                                fcity_id:this.from.city_id,
                                fregion_id:this.from.region_id,
                                faddr:this.from.addr,
                                tstate_id:this.to.state_id,
                                tcity_id:this.to.city_id,
                                tregion_id:this.to.region_id,
                                taddr:this.to.addr,
                            });
                        }else{
                            ainfo = JSON.stringify({
                                msg:this.msg,
                            });
                        }
                        let data={
                            ainfo:ainfo,
                            atype:this.appoint.atype,
                            atime:this.appoint.atime,
                            _token:"{{csrf_token()}}"
                        };
                        let that = this;
                        getData("{!! config('var.pal') !!}"+that.appoint.aid,function(json){
                            if(json.status===1){
                                if(typeof appointlistapp !== 'undefined'){
                                    appointlistapp.getData();
                                }
                                $('#alter').modal("hide");
                            }
                        },"#msg",data);
                    },
                    init(){
                        let that = this;
                        document.getElementById('alter').addEventListener('show.bs.modal',function(event){
                            getData("{!! config('var.pg') !!}"+that.aid,
                            function(json){
                                if(json.data!==null){
                                    that.appoint = json.data.appoint;
                                    that.appoint.atime = that.appoint.atime.replace(' ','T');
                                    that.getStation();
                                    if(that.appoint.atype==='r'){
                                        that.from.state_id=that.appoint.ainfo.fstate_id;
                                        that.from.city_id=that.appoint.ainfo.fcity_id;
                                        that.from.region_id=that.appoint.ainfo.fregion_id;
                                        that.from.addr=that.appoint.ainfo.faddr;
                                        that.to.state_id=that.appoint.ainfo.tstate_id;
                                        that.to.city_id=that.appoint.ainfo.tcity_id;
                                        that.to.region_id=that.appoint.ainfo.tregion_id;
                                        that.to.addr=that.appoint.ainfo.taddr;
                                        initaddress(that.from,"{!! config('var.sla') !!}",that.from);
                                        initaddress(that.to,"{!! config('var.sla') !!}",that.to);
                                    }
                                    that.msg=that.appoint.ainfo.msg;
                                }
                            },"#msg",null,false);
                        });
                    },
                    getStation(){
                        let that = this;
                        getData("{!! config('var.sg') !!}"+that.appoint.sid,function(json){
                            if(json.status===1&&json.data!==null){
                                that.station=getStation(json.data.station);
                                initaddress(that,"{!! config('var.sla') !!}",that.station);
                            }else{
                                that.station=null;
                            }
                        },"",null,false);
                    }
                }
            }).mount("#alter");

        </script>