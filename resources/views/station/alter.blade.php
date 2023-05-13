    <x-modal id="alter" class="modal-fullscreen ckeditor" title="编辑站点@@{{ station.sname===''?'':'-'+station.sname }}">
        <div class="text-center p-4 pb-4">
            <div class="crop-station" id="crop-station">
                <div class="station-view btn btn-outline-dark" style="cursor: pointer;" title="更换站点图片">
                    <img  :src="station.img" alt="" height="72" width="72" style="font-size: 68px;border-radius: 8px;" >
                </div>
            </div>
            <h4 class="mb-3"><i class="bi bi-info-square-fill"></i> 基本信息</h4>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <label class="input-group-text" for="snamealter">#@{{station.sid}}</label>
                        <div class="form-floating">
                            <input type="text" class="form-control" id="snamealter" v-model="station.sname" placeholder="站点名称" required>
                            <label for="snamealter" class="form-label">名称</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <label class="input-group-text" for="sstatealter"><i class="bi" :class="{'bi-door-open':station.sstate==='o','bi-door-closed':station.sstate==='c'}"></i></label>
                        <div class="form-floating">
                            <select class="form-select" v-model="station.sstate" id="sstatealter" required>
                                <option v-for="(sstate,index) in sstates" :key="index" :label="sstate" :value="index">@{{ station.sstate }}</option>
                            </select>
                            <label for="sstatealter" class="form-label">站点状态</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <label class="input-group-text" for="timealter"><i class="bi bi-calendar4-week"></i></label>
                        <div class="form-floating">
                            <input type="text" class="form-control" id="timealter" v-model="station.sinfo.time" placeholder="开放时间描述【不填写则根据时间配置自动生成】" required>
                            <label for="timealter" class="form-label">开放时间描述</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch form-check-inline">
                        <label for="approvetimealter" class="form-check-label">预约确认时间限制</label>
                        <input type="checkbox" class="form-check-input" id="approvetimealter" v-model="station.sinfo.approvetime" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch form-check-inline">
                        <label for="ralter" class="form-check-label">支持报备</label>
                        <input type="checkbox" class="form-check-input" id="ralter" v-model="station.sinfo.r" required>
                    </div>
                    <div v-show="station.sinfo.r===true" class="input-group">
                        <input type="number" class="form-control" id="rnumalter" min="0" v-model="station.sinfo.rnum" placeholder="报备人数/日【不填写表示无限制】" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch form-check-inline">
                        <label for="palter" class="form-check-label">支持核酸检测</label>
                        <input type="checkbox" class="form-check-input" id="palter" v-model="station.sinfo.p" required>
                    </div>
                    <div v-show="station.sinfo.p===true" class="input-group">
                        <input type="number" class="form-control" id="pnumalter" min="0" v-model="station.sinfo.pnum" placeholder="核酸检测人数/日【不填写表示无限制】" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch form-check-inline">
                        <label for="aalter" class="form-check-label">支持抗原检测</label>
                        <input type="checkbox" class="form-check-input" id="aalter" v-model="station.sinfo.a" required>
                    </div>
                    <div v-show="station.sinfo.a===true" class="input-group">
                        <input type="number" class="form-control" id="anumalter" min="0" v-model="station.sinfo.anum" placeholder="抗原检测人数/日【不填写表示无限制】" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch form-check-inline">
                        <label for="valter" class="form-check-label">支持疫苗接种</label>
                        <input type="checkbox" class="form-check-input" id="valter" v-model="station.sinfo.v" required>
                    </div>
                    <div v-show="station.sinfo.v===true" class="input-group">
                        <input type="number" class="form-control" id="vnumalter" min="0" v-model="station.sinfo.vnum" placeholder="疫苗接种人数/日【不填写表示无限制】" required>
                    </div>
                </div>
            </div>
            <hr class="my-4"><br><br>
            <h4 class="mb-3"><i class="bi bi-calendar2-week-fill"></i> 时间配置</h4>
            <div class="row g-3">
                <!--配置方案-->
                <div class="input-group justify-content-center">
                    <a class="btn btn-outline-secondary" v-for="(stimeconfig,index) in stimeconfigs" :class="{'active':stimeconfig.status}" @click="changestimeconfig(index)" role="button">配置@{{index+1}}</a>
                </div>
                <!--配置方案应用-->
                <div v-for="(stimeconfig,index) in stimeconfigs" v-show="stimeconfig.status">
                    <div class="card card-body">配置@{{index+1}}：@{{stimeconfig.text}}<a class="btn btn-outline-dark" @click="setstimeconfig(index)">应用</a></div>
                </div>
                <!--一周七天的配置-->
                <div class="accordion" id="stimeaccordionalter">
                    <div class="accordion-item" v-for="(sday,i) in station.stime">
                        <h2 class="accordion-header input-group">
                            <button class="accordion-button collapsed" style="width:90%" type="button" data-bs-toggle="collapse" :data-bs-target="'#stimealter'+i" aria-expanded="true" aria-controls="'stimealter'+i">
                            @{{getDateType(i)+'-'+sday.length+'个配置'}}
                            <div class="btn-fill">
                                <div class="progress-stacked" v-for="(item,j) in sday">
                                    <div class="progress" role="progressbar" :style="{width: getWidth('00:00',item.start)}">
                                        <div class="progress-bar bg-light"></div>
                                    </div>
                                    <div class="progress" role="progressbar" :style="{width: getWidth(item.start,item.end)}">
                                        <div class="progress-bar bg-success" v-text="item.start+'~'+item.end"></div>
                                    </div>
                                    <div class="progress" role="progressbar" :style="{width: getWidth(item.end,'24:00')}">
                                        <div class="progress-bar bg-light"></div>
                                    </div>
                                </div>
                            </div>
                            </button>
                            <button class="btn btn-outline-dark" @click="addstime(i)" style="width:10%"><i class="bi bi-plus-lg"></i></button>
                        </h2>
                        <div :id="'stimealter'+i" class="accordion-collapse collapse" data-bs-parent="#stimeaccordionalter" :aria-labelledby="'stimehalter'+i">
                            <div v-for="(item,j) in sday" class="input-group p-1">
                                <button class="btn btn-primary">#@{{j+1}}</button>
                                <input type="time" class="form-control" v-model="item.start" placeholder="">
                                <label class="input-group-text">~</label>
                                <input type="time" class="form-control" v-model="item.end" placeholder="">
                                <button class="btn btn-outline-dark" @click="upstime(i,j)" :disabled="j===0"><i class="bi bi-arrow-up"></i></button>
                                <button class="btn btn-outline-dark" @click="downstime(i,j)" :disabled="j===station.stime[i].length-1"><i class="bi bi-arrow-down"></i></button>
                                <button class="btn btn-outline-danger" @click="delstime(i,j)"><i class="bi bi-dash-lg"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="my-4"><br><br>
            <h4 class="mb-3"><i class="bi bi-pin-map-fill"></i> 地址配置</h4>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <label class="input-group-text" for="slatalter"><i class="bi bi-globe2"></i></label>
                        <div class="form-floating">
                            <input type="number" class="form-control" id="slatalter" step="0.000001" min="3" max="54" v-model="station.slat" placeholder="纬度(°N)" required>
                            <label for="slatalter" class="form-label">纬度(°N)</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <label class="input-group-text" for="slngalter"><i class="bi bi-globe"></i></label>
                        <div class="form-floating">
                            <input type="number" class="form-control" id="slngalter" step="0.000001" min="73" max="136" v-model="station.slng" placeholder="经度(°E)" required>
                            <label for="slngalter" class="form-label">经度(°E)</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <label class="input-group-text" for="addralter"><i class="bi bi-geo-alt"></i></label>
                        <div class="form-floating">
                            <input type="text" class="form-control" id="addralter" v-model="station.sinfo.addr" placeholder="站点地址描述" required>
                            <label for="addralter" class="form-label">地址描述</label>
                        </div>
                    </div>
                </div>
                <div v-show="state_ids!==undefined&&state_ids.length>0" class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" v-model="station.state_id" @change="getCities()" id="state_idalter" required>
                            <option label="请选择省市区" value="">请选择省市区</option>
                            <option v-for="state_id in state_ids" :key="state_id.id" :label="state_id.cname" :value="state_id.id">@{{ state_id.cname }}</option>
                        </select>
                        <label for="state_idalter" class="form-label">省市区</label>
                    </div>
                </div>
                <div v-show="city_ids!==undefined&&city_ids.length>0" class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" v-model="station.city_id" @change="getRegions()" id="city_idalter" required>
                            <option label="请选择地级市" value="">请选择地级市</option>
                            <option v-for="city_id in city_ids" :key="city_id.id" :label="city_id.cname" :value="city_id.id">@{{ city_id.cname }}</option>
                        </select>
                        <label for="city_idalter" class="form-label">地级市</label>
                    </div>
                </div>
                <div v-show="region_ids!==undefined&&region_ids.length>0" class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" v-model="station.region_id" id="region_idalter" required>
                            <option label="请选择区县" value="">请选择区县</option>
                            <option v-for="region_id in region_ids" :key="region_id.id" :label="region_id.cname" :value="region_id.id">@{{ region_id.cname }}</option>
                        </select>
                        <label for="region_idalter" class="form-label">区县</label>
                    </div>
                </div>
            </div>
            <hr class="my-4"><br><br>
            <h4 class="mb-3"><i class="bi bi-person-lines-fill"></i> 站点管理员【序号小者优先级高】</h4>
            <div class="input-group"><input class="form-control" @keyup.enter="insertuid" type="text" v-model="uid" placeholder="请输入身份证明/邮箱/UID"> <button type="button" class="btn btn-outline-success" @click="insertuid"><i class="bi bi-person-plus-fill"></i> 添加管理员</button></div>
            <div class="row g-3">
                <div class="col-md-12 input-group" v-for="(admin,index) in station.sadmin" :key="index">
                    <button type="button" class="badge bg-dark" disabled>@{{ index+1 }}</button>
                    <a class="form-control text-truncate btn btn-outline-secondary" target="_blank" title="进入该用户主页" :href="'/user/'+admin.uid">@{{ "#"+admin.uid+" "+admin.uname }}</a>
                    <a class="form-control text-truncate btn btn-outline-secondary" target="_blank" title="发邮件给该用户" :href="'mailto:'+admin.uemail">@{{ admin.uemail }}</a>
                    <button type="button" class="btn btn-outline-danger" @click="deluid(index)"><i class="bi bi-x-lg"></i></button>
                    <button type="button" class="btn btn-outline-info" @click="upuid(index)" :disabled="index===0"><i class="bi bi-arrow-up"></i></button>
                    <button type="button" class="btn btn-outline-secondary" @click="downuid(index)" :disabled="index===station.sadmin.length-1"><i class="bi bi-arrow-down"></i></button>
                </div>
            </div>
            <hr class="my-4"><br><br>
            <h4 class="mb-3"><i class="bi bi-person-workspace"></i> 区域管理员【可管理区域内站点】</h4>
            <div class="row g-3">
                <div class="col-md-12 input-group" v-for="(admin,index) in station.aadmin" :key="index">
                    <button type="button" class="badge bg-dark" disabled>@{{ index+1 }}</button>
                    <a class="form-control text-truncate btn btn-outline-secondary" target="_blank" title="进入该用户主页" :href="'/user/'+admin.uid">@{{ "#"+admin.uid+" "+admin.uname }}</a>
                    <a class="form-control text-truncate btn btn-outline-secondary" target="_blank" title="发邮件给该用户" :href="'mailto:'+admin.uemail">@{{ admin.uemail }}</a>
                    <button type="button" class="text-truncate badge bg-dark" disabled>@{{ getAAdminTypes(admin.types) }}</button>
                </div>
            </div>
            <hr class="my-4"><br><br>
            <h4 class="mb-3"><i class="bi bi-body-text"></i> 站点描述</h4>
            <textarea id="altereditor" class="ckeditor"></textarea>
        </div>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">关闭</button>
            <button type="button" class="btn btn-outline-success"  @click="alter"><i class="bi bi-building-fill-gear"></i> 修改</button>
        </x-slot>

    <x-modal id="station-modal" title="更换站点图片" class="modal-fullscreen">
        <form id="uploadstation" class="avatar-form" :action="'{!! config('var.asu') !!}'+station.sid" enctype="multipart/form-data" method="post">
            {{ csrf_field() }}
            <div class="avatar-body">
                <!-- Upload image and data -->
                <div class="avatar-upload">
                    <input class="avatar-src" name="avatar_src" type="hidden">
                    <input class="avatar-src" name="sid" :value="station.sid" type="hidden">
                    <input class="avatar-data" name="avatar_data" type="hidden">
                    <label for="avatarInput">本地上传</label>
                    <input class="avatar-input" id="avatarInput" name="avatar_file" type="file">
                </div>

                <!-- Crop and preview -->
                <div class="row">
                    <div class="col-md-9">
                        <div class="avatar-wrapper"></div>
                    </div>
                    <div class="col-md-3">
                        <div class="avatar-preview preview-avatar-lg"></div>
                        <div class="avatar-preview preview-avatar-md"></div>
                        <div class="avatar-preview preview-avatar-sm"></div>
                    </div>
                </div>
                <x-slot name="footer">
                    <div class="row">
                        <div class="input-group">
                            <input class="avatar-btns" type="range" min="-180" max="180" data-method="rotate" data-option="45" value="0" class="form-range" class="" id="station-btns">
                            <button data-method="rotate" value="0" class="btn btn-outline-info avatar-btns" type="submit">重置</button>
                            <button form="uploadstation" class="btn btn-outline-success btn-block avatar-save" type="submit">保存</button>
                        </div>
                    </div>
                </x-slot>
            </div>
        </form>
    </x-modal>
    </x-modal>
        <script>

            let altereditor=null;
            const alterapp=Vue.createApp({
                data() {
                    return{
                        index:0,
                        sid:0,
                        uid:"",
                        sadmin:[],
                        station:{
                            sname: "",
                            sstate: "c",
                            state_id:"",
                            city_id:"",
                            region_id:"",
                            slat:43,
                            slng:125,
                            aadmin:[],
                            sadmin:[],
                            sinfo:{
                                approvetime:false,
                                p:false,
                                a:false,
                                r:false,
                                v:false,
                                pnum:null,
                                anum:null,
                                rnum:null,
                                vnum:null,
                                addr:"",
                                time:"",
                                des: "",
                            },
                            stime:[
                                [{start:"",end:""}],[{start:"",end:""}],[{start:"",end:""}],[{start:"",end:""}],[{start:"",end:""}],[{start:"",end:""}],[{start:"",end:""}]
                            ],
                        },

                        sstates:{
                            o:"开放",
                            c:"关闭"
                        },
                        state_ids:[],
                        city_ids:[],
                        region_ids:[],
                        stimeconfigs:{!! json_encode($config_station['stimeconfigs']) !!},
                    }
                },
                mounted(){
                    this.init();
                },
                methods:{

                    //修改
                    alter(){
                        //this.station.sadmin.forEach(v=>this.sadmin.push(v.uid));
                        let data={
                            sname:this.station.sname,
                            sstate:this.station.sstate,
                            state_id:this.station.state_id,
                            city_id:this.station.city_id,
                            region_id:this.station.region_id,
                            slat:this.station.slat,
                            slng:this.station.slng,
                            approvetime:this.station.sinfo.approvetime?"1":"0",
                            p:this.station.sinfo.p?"1":"0",
                            a:this.station.sinfo.a?"1":"0",
                            v:this.station.sinfo.v?"1":"0",
                            r:this.station.sinfo.r?"1":"0",
                            pnum:this.station.sinfo.pnum,
                            anum:this.station.sinfo.anum,
                            vnum:this.station.sinfo.vnum,
                            rnum:this.station.sinfo.rnum,
                            addr:this.station.sinfo.addr,
                            time:this.station.sinfo.time,
                            des:altereditor.getData(),
                            stime:JSON.stringify(this.station.stime),
                            sadmin:JSON.stringify(this.sadmin),
                            _token:"{{csrf_token()}}"
                        };
                        console.log(data);
                        let that = this;
                        getData("{!! config('var.asa') !!}"+that.station.sid,function(json){
                            if(json.status===1){
                                if(typeof stationlistapp !== 'undefined'){
                                    stationlistapp.getData();
                                }else if(typeof stationapp !== 'undefined'){
                                    getData("{!! config('var.asg') !!}"+that.station.sid,function(tmp){
                                        stationapp.station=tmp.data.station;
                                        stationapp.init();
                                    },null);
                                }
                            }
                        },"#alter-msg",data);
                    },
                    //初始化载入原来数据
                    init(){
                        CKSource.Editor.create( document.querySelector( '#altereditor' ),editorconfig)
                            .then( newEditor => {altereditor=newEditor;} )
                            .catch( error => {console.error( error );} );
                        let that = this;
                        document.getElementById('alter').addEventListener('show.bs.modal',function(event){
                            getData("{!! config('var.asg') !!}"+that.sid,
                            function(json){
                                if(json.data!==null){
                                    let station=getStation(json.data.station);
                                    document.title+="-"+station.sname;
                                    if(station.stime===[]||station.stime.length!==7){
                                        station.stime=[[],[],[],[],[],[],[]];
                                    }
                                    station.sadmin.forEach(v=>that.sadmin.push(v.uid));
                                    that.station=station;
                                    ///service/system/getaddrlist，把这个地方找出来
                                    initaddress(that,"{!! config('var.sla') !!}",that.station);
                                    altereditor.setData(that.station.sinfo.des);
                                    console.log(that.station);
                                }
                            },"#msg");
                        });
                    },
                    getAAdminTypes(str){
                        const tmp = isJSON(str);
                        let retval="";
                        if(tmp.indexOf("s")>=0) retval+="省市区";
                        if(tmp.indexOf("c")>=0) retval+=(retval===""?"":"/")+"地级市";
                        if(tmp.indexOf("r")>=0) retval+=(retval===""?"":"/")+"区县";
                        return retval;
                    },

                    //得到日期的类型
                    getDateType(i){
                        return DAYTYPE[i];
                    },

                    //增加时间段
                    addstime(i){
                        this.station.stime[i].push({start:"",end:""});
                    },

                    //删除时间段
                    delstime(i,j){
                        this.station.stime[i].splice(j,1);
                    },

                    //上移时间
                    upstime(i,j){
                        if(j>0){
                            let tem=this.station.stime[i][j];
                            this.station.stime[i][j]=this.station.stime[i][j-1];
                            this.station.stime[i][j-1]=tem;
                        }
                    },

                    //下移时间
                    downstime(i,j){
                        if(j<this.station.stime[i].length-1){
                            let tem=this.station.stime[i][j];
                            this.station.stime[i][j]=this.station.stime[i][j+1];
                            this.station.stime[i][j+1]=tem;
                        }
                    },

                    //应用配置方案
                    setstimeconfig(index){
                        this.station.stime=this.stimeconfigs[index].stime;
                    },
                    getWidth(starttime,endtime){
                        const end=getDayTime(endtime);
                        const start=getDayTime(starttime);
                        if(end>start)
                            return(end-start)/864000+'%';
                        else
                            return 0;
                    },

                    //改变时间配置方案
                    changestimeconfig(i){
                        this.stimeconfigs[i].status=!this.stimeconfigs[i].status;
                        if(this.stimeconfigs[i].status){
                            for(j in this.stimeconfigs){
                                if(i===j-'0'){
                                    continue;
                                }
                                this.stimeconfigs[j].status=false;
                            }
                        }
                    },
                    insertuid(){
                        if(!this.sadmin.includes(parseInt(this.uid))){
                            let that=this;
                            getData("{!! config('var.aug') !!}"+this.uid,
                            function(json){
                                if(json.data!==null&&'user' in json.data){
                                    that.sadmin.push(that.uid);
                                    that.station.sadmin.push(json.data.user);
                                }
                            },"#alter-msg",data=null,jump=false);
                        }else{
                            echoMsg("#alter-msg",{status:4,message:"该用户已添加"});
                        }
                    },
                    deluid(index){
                        this.sadmin.splice(index,1);
                        this.station.sadmin.splice(index,1);
                    },

                    upuid(index){
                        if(index>0){
                            let tem=this.sadmin[index];
                            this.sadmin[index]=this.sadmin[index-1];
                            this.sadmin[index-1]=tem;
                            tem=this.station.sadmin[index];
                            this.station.sadmin[index]=this.station.sadmin[index-1];
                            this.station.sadmin[index-1]=tem;
                        }
                    },
                    downuid(index){
                        if(index<this.sadmin.length-1){
                            let tem=this.sadmin[index];
                            this.sadmin[index]=this.sadmin[index+1];
                            this.sadmin[index+1]=tem;
                            tem=this.station.sadmin[index];
                            this.station.sadmin[index]=this.station.sadmin[index+1];
                            this.station.sadmin[index+1]=tem;
                        }
                    },
                }
            }).mount("#alter");

        </script>