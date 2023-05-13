<!--点击添加按钮的提示-->
<x-modal id="checkinsert" class="modal-sm" title="添加确认">
    <div class="text-center p-4 pb-4">
        您确定要添加该站点吗？
    </div>
    <x-slot name="footer">
        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#insert" onclick="insertapp.insert()">创建</button>
        <button type="button" class="btn btn-outline-info"  data-bs-toggle="modal" data-bs-target="#insert">返回</button>
    </x-slot>

    <!--添加站点的拟态框-->
</x-modal>
    <x-modal id="insert" class="modal-fullscreen ckeditor" title="添加站点@@{{ sname===''?'':'-'+sname }}">    
        <div class="text-center p-4 pb-4">
        <h4 class="mb-3"><i class="bi bi-info-square-fill"></i> 基本信息</h4>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="sname" v-model="sname" placeholder="站点名称" required>
                        <label for="sname" class="form-label">名称</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <label class="input-group-text" for="sstate"><i class="bi" :class="{'bi-door-open':sstate==='o','bi-door-closed':sstate==='c'}"></i></label>
                        <div class="form-floating">
                            <select class="form-select" v-model="sstate" id="sstate" required>
                                <option v-for="(sstate,index) in sstates" :key="index" :label="sstate" :value="index">@{{ sstate }}</option>
                            </select>
                            <label for="sstate" class="form-label">站点状态</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <label class="input-group-text" for="time"><i class="bi bi-calendar4-week"></i></label>
                        <div class="form-floating">
                            <input type="text" class="form-control" id="time" v-model="time" placeholder="开放时间描述【不填写则根据时间配置自动生成】" required>
                            <label for="time" class="form-label">开放时间描述</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch form-check-inline">
                        <label for="approvetime" class="form-check-label">预约确认时间限制</label>
                        <input type="checkbox" class="form-check-input" id="approvetime" v-model="approvetime" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch form-check-inline">
                        <label for="r" class="form-check-label">支持报备</label>
                        <input type="checkbox" class="form-check-input" id="r" v-model="r" required>
                    </div>
                    <div v-show="r===true" class="input-group">
                        <input type="number" class="form-control" id="rnum" min="0" v-model="rnum" placeholder="报备人数/日【不填写表示无限制】" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch form-check-inline">
                        <label for="p" class="form-check-label">支持核酸检测</label>
                        <input type="checkbox" class="form-check-input" id="p" v-model="p" required>
                    </div>
                    <div v-show="p===true" class="input-group">
                        <input type="number" class="form-control" id="pnum" min="0" v-model="pnum" placeholder="核酸检测人数/日【不填写表示无限制】" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch form-check-inline">
                        <label for="a" class="form-check-label">支持抗原检测</label>
                        <input type="checkbox" class="form-check-input" id="a" v-model="a" required>
                    </div>
                    <div v-show="a===true" class="input-group">
                        <input type="number" class="form-control" id="anum" min="0" v-model="anum" placeholder="抗原检测人数/日【不填写表示无限制】" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch form-check-inline">
                        <label for="v" class="form-check-label">支持疫苗接种</label>
                        <input type="checkbox" class="form-check-input" id="v" v-model="v" required>
                    </div>
                    <div v-show="v===true" class="input-group">
                        <input type="number" class="form-control" id="vnum" min="0" v-model="vnum" placeholder="疫苗接种人数/日【不填写表示无限制】" required>
                    </div>
                </div>
            </div>
            <hr class="my-4"><br><br>
            <h4 class="mb-3"><i class="bi bi-calendar2-week-fill"></i> 时间配置</h4>
            <div class="row g-3">
                <div class="input-group justify-content-center">
                    <a class="btn btn-outline-secondary" v-for="(stimeconfig,index) in stimeconfigs" :class="{'active':stimeconfig.status}" @click="changestimeconfig(index)" role="button">配置@{{index+1}}</a>
                </div>
                <div v-for="(stimeconfig,index) in stimeconfigs" v-show="stimeconfig.status" :id="'stimeconfig'+index">
                    <div class="card card-body">配置@{{index+1}}：@{{stimeconfig.text}}<a class="btn btn-outline-dark" @click="setstimeconfig(index)">应用</a></div>
                </div>
                <div class="accordion" id="stimeaccordion">
                    <div class="accordion-item" v-for="(sday,i) in stime">
                        <h2 class="accordion-header input-group" :id="'stimeh'+i">
                            <button class="accordion-button collapsed" style="width:90%" type="button" data-bs-toggle="collapse" :data-bs-target="'#stime'+i" aria-expanded="true" aria-controls="'stime'+i">
                            @{{getDateType(i)}}
                            </button>
                            <button class="btn btn-outline-dark" @click="addstime(i)" style="width:10%"><i class="bi bi-plus-lg"></i></button>
                        </h2>
                        <div :id="'stime'+i" class="accordion-collapse collapse" data-bs-parent="#stimeaccordion" :aria-labelledby="'stimeh'+i">
                            <div v-for="(item,j) in sday" class="input-group p-1">
                                <button class="btn btn-primary">#@{{j+1}}</button>
                                <input :id="'stime'+i+j+'s'" type="time" class="form-control" v-model="item.start" placeholder="">
                                <label class="input-group-text">~</label>
                                <input :id="'stime'+i+j+'e'" type="time" class="form-control" v-model="item.end" placeholder="">
                                <button class="btn btn-outline-dark" @click="upstime(i,j)" :disabled="j===0"><i class="bi bi-arrow-up"></i></button>
                                <button class="btn btn-outline-dark" @click="downstime(i,j)" :disabled="j===stime[i].length-1"><i class="bi bi-arrow-down"></i></button>
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
                        <label class="input-group-text" for="slat"><i class="bi bi-globe2"></i></label>
                        <div class="form-floating">
                            <input type="number" class="form-control" id="slat" step="0.000001" min="3" max="54" v-model="slat" placeholder="纬度(°N)" required>
                            <label for="slat" class="form-label">纬度(°N)</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <label class="input-group-text" for="slng"><i class="bi bi-globe"></i></label>
                        <div class="form-floating">
                            <input type="number" class="form-control" id="slng" step="0.000001" min="73" max="136" v-model="slng" placeholder="经度(°E)" required>
                            <label for="slng" class="form-label">经度(°E)</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <label class="input-group-text" for="addr"><i class="bi bi-geo-alt"></i></label>
                        <div class="form-floating">
                            <input type="text" class="form-control" id="addr" v-model="addr" placeholder="站点地址描述" required>
                            <label for="addr" class="form-label">地址描述</label>
                        </div>
                    </div>
                </div>
                <div v-if="state_ids!==undefined&&state_ids.length>0" class="col-md-4">
                    <label for="istate_id" class="form-label">省市区</label>
                    <select class="form-select" v-model="state_id" @change="getCities()" id="istate_id" required>
                        <option label="请选择省市区" value="">请选择省市区</option>
                        <option v-for="state_id in state_ids" :key="state_id.id" :label="state_id.cname" :value="state_id.id">@{{ state_id.cname }}</option>
                    </select>
                </div>
                <div v-if="city_ids!==undefined&&city_ids.length>0" class="col-md-4">
                    <label for="icity_id" class="form-label">地级市</label>
                    <select class="form-select" v-model="city_id" @change="getRegions()" id="icity_id" required>
                        <option label="请选择地级市" value="">请选择地级市</option>
                        <option v-for="city_id in city_ids" :key="city_id.id" :label="city_id.cname" :value="city_id.id">@{{ city_id.cname }}</option>
                    </select>
                </div>
                <div v-if="region_ids!==undefined&&region_ids.length>0" class="col-md-4">
                    <label for="region_id" class="form-label">区县</label>
                    <select class="form-select" v-model="region_id" id="region_id" required>
                        <option label="请选择区县" value="">请选择区县</option>
                        <option v-for="region_id in region_ids" :key="region_id.id" :label="region_id.cname" :value="region_id.id">@{{ region_id.cname }}</option>
                    </select>
                </div>
            </div>
            <hr class="my-4"><br><br>
            <h4 class="mb-3"><i class="bi bi-body-text"></i> 站点描述</h4>
            <textarea id="deseditor" class="ckeditor"></textarea>
        </div>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">关闭</button>
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#checkinsert"><i class="bi bi-building-fill-add"></i> 创建</button>
        </x-slot>
    </x-modal>
        <script>

            let editor={des:null};
            const insertapp=Vue.createApp({
                data() {
                    return{
                        sname: "",
                        sstate: "c",
                        state_id:"",
                        city_id:"",
                        region_id:"",
                        slat:43,
                        slng:125,

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

                        stime:[
                            [{start:"",end:""}],[{start:"",end:""}],[{start:"",end:""}],[{start:"",end:""}],[{start:"",end:""}],[{start:"",end:""}],[{start:"",end:""}]
                        ],
                        stimeconfigs:isJSON({!! json_encode($config_station['stimeconfigs']) !!}),
                        sstates:{
                            o:"开放",
                            c:"关闭"
                        },
                        state_ids:[],
                        city_ids:[],
                        region_ids:[],
                    }
                },
                mounted(){
                    CKSource.Editor.create( document.querySelector( '#deseditor' ),editorconfig)
                        .then( newEditor => {editor.des=newEditor;} )
                        .catch( error => {console.error( error );} );

                        //初始化地址
                    initaddress(this,"{!! config('var.sla') !!}",this);
                },
                methods:{

                    //创建新站点
                    insert(){
                        let data={
                            sname:this.sname,
                            sstate:this.sstate,
                            state_id:this.state_id,
                            city_id:this.city_id,
                            region_id:this.region_id,
                            slat:this.slat,
                            slng:this.slng,
                            approvetime:this.approvetime?"1":"0",
                            p:this.p?"1":"0",
                            a:this.a?"1":"0",
                            v:this.v?"1":"0",
                            r:this.r?"1":"0",
                            pnum:this.pnum,
                            anum:this.anum,
                            vnum:this.vnum,
                            rnum:this.rnum,
                            addr:this.addr,
                            time:this.time,
                            des:editor.des.getData(),
                            stime:JSON.stringify(this.stime),
                            _token:"{{csrf_token()}}"
                        };
                        console.log(data);
                        getData("{!! config('var.asi') !!}",null,"#insert-msg",data);
                    },

                    //得到日期的类型
                    getDateType(i){
                        return DAYTYPE[i];
                    },

                    //添加时间
                    addstime(i){
                        this.stime[i].push({start:"",end:""});
                    },

                    //删除时间
                    delstime(i,j){
                        this.stime[i].splice(j,1);
                    },

                    //上移时间
                    upstime(i,j){
                        if(j>0){
                            let tem=this.stime[i][j];
                            this.stime[i][j]=this.stime[i][j-1];
                            this.stime[i][j-1]=tem;
                        }
                    },

                    //下移时间
                    downstime(i,j){
                        if(j<this.stime[i].length-1){
                            let tem=this.stime[i][j];
                            this.stime[i][j]=this.stime[i][j+1];
                            this.stime[i][j+1]=tem;
                        }
                    },

                    //应用时间配置
                    setstimeconfig(index){
                        this.stime=this.stimeconfigs[index].stime;
                    },

                    //改变时间配置
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
                    }
                }
            }).mount("#insert");

        </script>