<!--点击添加按钮的提示-->
<x-modal id="checkinsert" class="modal-sm" title="添加确认">
    <div class="text-center p-4 pb-4">
        您确定要添加该预约/报备吗？
    </div>
    <x-slot name="footer">
        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#insert" onclick="insertapp.insert()">创建</button>
        <button type="button" class="btn btn-outline-info"  data-bs-toggle="modal" data-bs-target="#insert">返回</button>
    </x-slot>
</x-modal>
    <x-modal id="insert" class="modal-fullscreen ckeditor" title="添加预约/报备">    
        <div class="text-center p-4 pb-4">
            <h4 class="mb-3"><i class="bi bi-receipt"></i> 预约/报备信息</h4>
            <div class="row g-3">
                <div class="col-4">
                    <div class="input-group">
                        <div class="form-floating">
                            <input type="number" class="form-control" min="1" id="sid" v-model="sid" placeholder="站点编号" @change="getStation" required>
                            <label for="sid" class="form-label">站点编号</label>
                        </div>
                    </div>
                </div>
@if(!isset($type))
                <div class="col-4">
                    <div class="form-floating">
                        <select class="form-select" v-model="atype" id="atype">
                            <option v-for="(item,index) in services" :key="index" :label="item.label" :value="index">@{{ item.label }}</option>
                            </select>
                        <label for="atype">预约/报备服务</label>
                    </div>
                </div>
@else
                <div class="col-4">
                    <div class="form-floating">
                        <select class="form-select" v-model="atype" id="atype" disabled>
                            <option label="报备服务" value="r">报备服务</option>
                        </select>
                        <label for="atype">服务</label>
                    </div>
                </div>
@endif
                <div class="col-4">
                    <div class="form-floating">
                        <input type="datetime-local" class="form-control" id="atime" v-model="atime" placeholder="预约/报备时间">
                        <label for="atime" class="form-label">预约/报备时间</label>
                    </div>
                </div>
                <div v-if="atype==='r'" class="col-12 input-group">
                    <span class="input-group-text">起点</span>
                    <div v-show="from.state_ids!==undefined&&from.state_ids.length>0" class="form-floating">
                        <select class="form-select" v-model="from.state_id" @change="from.getCities()" id="fromstate_id" required>
                            <option label="请选择省市区" value="">请选择省市区</option>
                            <option v-for="state_id in from.state_ids" :key="state_id.id" :label="state_id.cname" :value="state_id.id">@{{ state_id.cname }}</option>
                        </select>
                        <label for="fromstate_id" class="form-label">省市区</label>
                    </div>
                    <div v-show="from.city_ids!==undefined&&from.city_ids.length>0" class="form-floating">
                        <select class="form-select" v-model="from.city_id" @change="from.getRegions()" id="fromcity_id" required>
                            <option label="请选择地级市" value="">请选择地级市</option>
                            <option v-for="city_id in from.city_ids" :key="city_id.id" :label="city_id.cname" :value="city_id.id">@{{ city_id.cname }}</option>
                        </select>
                        <label for="fromcity_id" class="form-label">地级市</label>
                    </div>
                    <div v-show="from.region_ids!==undefined&&from.region_ids.length>0" class="form-floating">
                        <select class="form-select" v-model="from.region_id" id="fromregion_id" required>
                            <option label="请选择区县" value="">请选择区县</option>
                            <option v-for="region_id in from.region_ids" :key="region_id.id" :label="region_id.cname" :value="region_id.id">@{{ region_id.cname }}</option>
                        </select>
                        <label for="fromregion_id" class="form-label">区县</label>
                    </div>
                    <div class="form-floating">
                        <input type="text" id="fromaddr" class="form-control" placeholder="详细地址" v-model="from.addr"/>
                        <label for="fromaddr" class="form-label">详细地址</label>
                    </div>
                </div>
                <div v-if="atype==='r'" class="col-12 input-group">
                    <span class="input-group-text">终点</span>
                    <div v-show="to.state_ids!==undefined&&to.state_ids.length>0" class="form-floating">
                        <select class="form-select" v-model="to.state_id" @change="to.getCities()" id="tostate_id" required>
                            <option label="请选择省市区" value="">请选择省市区</option>
                            <option v-for="state_id in to.state_ids" :key="state_id.id" :label="state_id.cname" :value="state_id.id">@{{ state_id.cname }}</option>
                        </select>
                        <label for="tostate_id" class="form-label">省市区</label>
                    </div>
                    <div v-show="to.city_ids!==undefined&&to.city_ids.length>0" class="form-floating">
                        <select class="form-select" v-model="to.city_id" @change="to.getRegions()" id="tocity_id" required>
                            <option label="请选择地级市" value="">请选择地级市</option>
                            <option v-for="city_id in to.city_ids" :key="city_id.id" :label="city_id.cname" :value="city_id.id">@{{ city_id.cname }}</option>
                        </select>
                        <label for="tocity_id" class="form-label">地级市</label>
                    </div>
                    <div v-show="to.region_ids!==undefined&&to.region_ids.length>0" class="form-floating">
                        <select class="form-select" v-model="to.region_id" id="toregion_id" required>
                            <option label="请选择区县" value="">请选择区县</option>
                            <option v-for="region_id in to.region_ids" :key="region_id.id" :label="region_id.cname" :value="region_id.id">@{{ region_id.cname }}</option>
                        </select>
                        <label for="toregion_id" class="form-label">区县</label>
                    </div>
                    <div class="form-floating">
                        <input type="text" id="toaddr" class="form-control" placeholder="详细地址" v-model="to.addr"/>
                        <label for="toaddr" class="form-label">详细地址</label>
                    </div>
                </div>
                <div class="form-floating"> 
                    <input type="text" id="msg" class="form-control" v-model="msg" placeholder="预约/报备信息">
                    <label for="msg" class="form-label">预约/报备信息</label>
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
                        <select class="form-select" v-model="station.state_id" @change="getCities()" id="state_id" disabled>
                            <option label="请选择省市区" value="">请选择省市区</option>
                            <option v-for="state_id in state_ids" :key="state_id.id" :label="state_id.cname" :value="state_id.id">@{{ state_id.cname }}</option>
                        </select>
                        <label for="state_id" class="form-label">省市区</label>
                    </div>
                </div>
                <div v-show="city_ids!==undefined&&city_ids.length>0" class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" v-model="station.city_id" @change="getRegions()" id="city_id" disabled>
                            <option label="请选择地级市" value="">请选择地级市</option>
                            <option v-for="city_id in city_ids" :key="city_id.id" :label="city_id.cname" :value="city_id.id">@{{ city_id.cname }}</option>
                        </select>
                        <label for="city_id" class="form-label">地级市</label>
                    </div>
                </div>
                <div v-show="region_ids!==undefined&&region_ids.length>0" class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" v-model="station.region_id" id="region_id" disabled>
                            <option label="请选择区县" value="">请选择区县</option>
                            <option v-for="region_id in region_ids" :key="region_id.id" :label="region_id.cname" :value="region_id.id">@{{ region_id.cname }}</option>
                        </select>
                        <label for="region_id" class="form-label">区县</label>
                    </div>
                </div>
            </div>
            </div>
        </div>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">关闭</button>
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#checkinsert"><i class="bi bi-clipboard-plus"></i> 创建</button>
        </x-slot>
    </x-modal>
        <script>
            const insertapp=Vue.createApp({
                data() {
                    return{
                        station:null,
                        sid:"",
                        atype:"",
                        atime:"",
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
                    insert(){
                        let ainfo="{}";
                        if(this.atype==='r'){
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
                            sid:this.sid,
                            atype:this.atype,
                            atime:this.atime,
                            ainfo:ainfo,
                            _token:"{{csrf_token()}}"
                        };
                        console.log(data);
                        getData("{!! config('var.pi') !!}",function(json){
                            if(json.status===1){
                                if(typeof appointlistapp !== 'undefined'){
                                    appointlistapp.reset();
                                    appointlistapp.getData();
                                }
                                $('#insert').modal("hide");
                            }
                        },"#msg",data);
                    },
                    init(){
                        let that = this;
                        initaddress(that.from,"{!! config('var.sla') !!}",that.from);
                        initaddress(that.to,"{!! config('var.sla') !!}",that.to);
                        document.getElementById('insert').addEventListener('show.bs.modal',function(event){
                            const sid = event.relatedTarget.getAttribute('data-bs-sid');
                            if(sid!==undefined&&sid!==null){
                                that.sid = sid;
                                that.atype = event.relatedTarget.getAttribute('data-bs-atype');
                                that.atime = event.relatedTarget.getAttribute('data-bs-atime');
                                that.getStation();
                            }
                        });
                    },
                    getStation(){
                        let that = this;
                        getData("{!! config('var.sg') !!}"+this.sid,function(json){
                            if(json.status===1&&json.data!==null){
                                that.station=getStation(json.data.station);
                                initaddress(that,"{!! config('var.sla') !!}",that.station);
                            }else{
                                that.station=null;
                            }
                        },"",null,false);
                    }
                }
            }).mount("#insert");

        </script>