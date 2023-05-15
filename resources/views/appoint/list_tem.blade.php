<main id="appointlist" class="container shadow list">
    <!--侧边栏-->
    <x-offcanvas>
        <form>
            <div class="mb-3 col-12">
                <div class="input-group">
                    <select class="form-select" v-model="params.order" required>
                        <option v-for="(ordertype,index) in ordertypes" :key="index" :label="ordertype" :value="index">@{{ ordertype }}</option>
                    </select>
                    <button type="button" class="btn btn-outline-info" @click="reset">重置 <i class="bi bi-arrow-clockwise"></i></button>
                    <button type="button" class="btn btn-outline-success" data-bs-dismiss="offcanvas"   @click="getData(params)">查询 <i class="bi bi-search"></i></button>
                </div>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="number" class="form-control" v-model="params.aid" id="paramsaid" placeholder="预约/报备编号">
                <label for="paramsaid">预约/报备编号</label>
            </div>
@if (isset($utype)&&$utype==='a')
            <div class="mb-3 col-12 form-floating">
                <input type="number" class="form-control" v-model="params.uid" id="paramsuid" placeholder="用户编号">
                <label for="paramsuid">用户编号</label>
            </div>
@endif
@if(!isset($type))
            <div class="mb-3 col-12 form-floating">
                <div class="form-check form-check-inline" v-for="(item,index) in services" >
                    <input class="form-check-input" type="checkbox" :id="'type'+index" v-model="params.type" :value="index">
                    <label :for="'type'+index" class="form-check-label" >@{{ item.label }}</label>
                </div>
            </div>
@endif
            <div class="mb-3 col-12 form-floating">
                <select class="form-select" v-model="params.state" id="paramsstate">
                    <option v-for="(item,index) in states" :key="index" :value="index" :label="item.label">@{{ item.label }}</option>
                    <option value="" label="所有状态类型">所有状态类型</option>
                </select>
                <label for="paramsstate">状态类型</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="datetime-local" class="form-control" v-model="params.start" id="paramsstart" placeholder="开始时间范围">
                <label for="paramsstart">开始时间范围</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="datetime-local" class="form-control" v-model="params.end" id="paramsend" placeholder="结束时间范围">
                <label for="paramsend">结束时间范围</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" id="paramssid" class="form-control" v-model="params.sid" placeholder="站点编号">
                <label for="paramssid">站点编号</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" id="paramsmsg" class="form-control" v-model="params.msg" placeholder="信息">
                <label for="paramsmsg">信息</label>
            </div>
        </form>
    </x-offcanvas>
        <x-modal id='info' class="modal-lg" title="@@{{applyflag===1?'提交申请':(cancelflag===1?'撤销申请':(refuseflag===1?'拒绝申请':(approveflag===1?'完成预约':'预约/报备信息')))}}">
            <div v-if="appoint!==null" style="word-wrap:break-word;word-break:break-all;over-flow:hidden;">
                <div class="row">
                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text">#@{{ appoint.aid }}</span>
                            <a class="form-control btn btn-outline-dark" title="打开用户主页" :href="'/user/'+appoint.uid" target="_blank">@{{ "#"+appoint.uid+" "+appoint.uname}}</a>
                            <a class="form-control btn btn-outline-dark" title="打开站点页面" :href="'/station/'+appoint.sid" target="_blank">@{{ "#"+appoint.sid+" "+appoint.sname}}</a>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text">服务</span>
                            <input class="form-control" :value="services[appoint.atype].label" disabled/>
                            <span class="input-group-text">状态</span>
                            <input class="form-control" :value="states[appoint.astate].label" disabled/>
                            <span class="input-group-text">时间</span>
                            <input class="form-control" :value="appoint.atime" disabled/>
                        </div>
                    </div>
                    <div v-if="appoint.atype!=='r'" class="col-12">
                        <div class="input-group">
                            <span class="input-group-text">信息</span>
                            <input class="form-control" :value="appoint.ainfo.msg" disabled/>
                        </div>
                    </div>
                    <div v-if="appoint.atype==='r'" class="accordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#ainfocollapse" aria-expanded="true" aria-controls="ainfocollapse">
                                    信息 - @{{ Object.keys(appoint.ainfo).length }} 项
                                </button>
                            </h2>
                            <div id="ainfocollapse" class="accordion-collapse collapse">
                                <div v-for="(item,index) in appoint.ainfo" class="input-group">
                                    <span class="input-group-text" v-text="index"></span>
                                    <input disabled class="form-control" :value="item"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#aprocess" aria-expanded="false" aria-controls="aprocess">
                                    历史处理 - @{{ appoint.aprocesses.length }} 项
                                </button>
                            </h2>
                            <div id="aprocess" class="accordion-collapse collapse">
                                <div v-for="(item,index) in appoint.aprocesses">
                                    <div class="input-group">
                                        <span class="input-group-text">@{{"#"+item.apid}}</span>
                                        <span class="input-group-text" :class="['bg-'+processtype[item.apinfo.type].btn]">@{{processtype[item.apinfo.type].label}}</span>
                                        <a class="form-control btn btn-outline-dark text-truncate" title="打开用户主页" :href="'/user/'+item.uid" target="_blank">@{{ "#"+item.uid+" "+item.uname}}</a>
                                        <span class="input-group-text" style="font-size:x-small">@{{item.aptime}}</span>
                                        <a class="btn btn-outline-dark text-truncate" 
                                        data-bs-toggle="collapse" :href="'#collapse'+index" ><i class="bi bi-three-dots-vertical"></i></a>
                                    </div>
                                    <div v-if="typeof item.apinfo.msg === 'string'" class="collapse" :id="'collapse'+index">
                                        信息：@{{item.apinfo.msg===''?'无':item.apinfo.msg}}
                                    </div>
                                    <div v-if="typeof item.apinfo.msg === 'object'" class="collapse" :id="'collapse'+index">
                                        <div class="card card-body">
                                            <div v-for="(msg,index) in item.apinfo.msg" class="input-group">
                                                <span class="input-group-text" v-text="index"></span>
                                                <input disabled class="form-control" :value="msg"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <x-slot name="footer">
                        <div class="input-group justify-content-end">
                            <input v-if="approveflag===1&&appoint.astate==='s'" v-model="msg" placeholder="同意原因说明信息" class="form-control"/>
                            <a v-if="approveflag===1&&appoint.astate==='s'" @click="approve()" class="btn btn-outline-warning">完成</a>

                            <input v-if="refuseflag===1&&appoint.astate==='s'" v-model="msg" placeholder="拒绝原因说明信息" class="form-control"/>
                            <a v-if="refuseflag===1&&appoint.astate==='s'" @click="refuse()" class="btn btn-outline-warning">拒绝</a>

                            <input v-if="cancelflag===1&&appoint.astate==='s'" v-model="msg" placeholder="撤销原因说明信息" class="form-control"/>
                            <a v-if="cancelflag===1&&appoint.astate==='s'" @click="cancel()" class="btn btn-outline-warning">撤销</a>

                            <a v-if="applyflag===1&&(appoint.astate==='n'||appoint.astate==='r')" @click="apply()" class="btn btn-outline-success">提交申请</a>

                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">关闭</button>
                        </div>
                    </x-slot>
                </div>
            </div>
        </x-modal>
    <div v-if="appoints.length>0">
        <div class="item thead-dark thead">
            <div class="row">
                <div class="col-12 text-center row align-items-center">
                    <div class="col-md-1 d-md-block d-none"><a class="btn btn-light btn-fill" @click="set('order','aid')" :title="params.desc==='0'?'正序【从小到大】':'倒序【从大到小】'"># <i v-if="params.order==='aid'" class="bi" :class="{'bi-sort-up-alt':params.desc==='0','bi-sort-up':params.desc==='1'}" ></i></a></div>
                    <div class="col-md-1 col-2 text-truncate"><a title="所有用户" class="btn btn-fill btn-light" @click="set('uid','')">用户</a></div>
                    <div class="col-1 text-truncate"><a title="所有站点" class="btn btn-fill btn-light" @click="set('sid','')">站点</a></div>
                    <div class="col-2 text-truncate"><a title="所有服务类型" class="btn btn-fill btn-light" @click='set("type",["p","a","v"])'>服务</a></div>
                    <div class="col-2 text-truncate"><a title="所有状态类型" class="btn btn-fill btn-light" @click="set('astate','')">状态</a></div>
                    <div class="col-2"><a :title="params.desc==='0'?'正序【从小到大】':'倒序【从大到小】'" class="btn btn-fill btn-light" @click="set('order','atime')"><i class="bi bi-calendar2-check"></i> <i v-if="params.order==='atime'" class="bi" :class="{'bi-sort-up-alt':params.desc==='0','bi-sort-up':params.desc==='1'}" ></i></a></div>
                    <div class="col-3">操作</div>
                </div>
            </div>
        </div>
        <div class="item" v-for="(appoint,index) in appoints" style="display: flex;" :key="index" >
            <div class="row text-center col-12 align-items-center" title="双击查看预约/报备详细信息" @dblclick="openinfo(index)">
                <div class="col-md-1 d-md-block d-none thead">@{{ appoint.aid }}</div>
                <div class="col-md-1 col-2 text-truncate"><a :title="'筛选用户：#'+appoint.uid+' '+appoint.uname" @click="set('uid',appoint.uid)" class="btn btn-fill btn-light">@{{appoint.uname}}</a></div>
                <div class="col-1 text-truncate"><a :title="'筛选站点：'+appoint.sname" @click="set('sid',appoint.sid)" class="btn btn-fill btn-light">@{{ appoint.sname }}</a></div>
                <div class="col-2 text-truncate"><a :title="'筛选服务类型：'+services[appoint.atype].label" @click="set('type',[appoint.atype])" class="btn btn-fill" :class="['btn-'+services[appoint.atype].btn]">@{{services[appoint.atype].label}}</a></div>
                <div class="col-2 text-truncate"><a :title="'筛选状态类型：'+states[appoint.astate].label" @click="set('astate',appoint.astate)" class="btn btn-fill" :class="['btn-'+states[appoint.astate].btn]">@{{states[appoint.astate].label}}</a></div>
                <div class="col-2" style="font-size:x-small;">@{{ appoint.atime }}</div>
                <div class="col-3 text-truncate">
                    <div class="input-group justify-content-center">
@if (isset($utype)&&$utype==='a')
                        <a v-if="appoint.astate==='s'" @click="openapprove(index)" class="btn btn-outline-success">同意</a>
                        <a v-if="appoint.astate==='s'" @click="openrefuse(index)" class="btn btn-outline-danger">拒绝</a>
                        <span v-if="appoint.astate!=='s'">无操作</span>
@else
                        <a v-if="appoint.astate==='n'||appoint.astate==='r'" @click="openapply(index)" class="btn btn-outline-success">提交</a>
                        <a v-if="appoint.astate==='n'||appoint.astate==='r'" @click="openalter(index)" class="btn btn-outline-info">修改</a>
                        <a v-if="appoint.astate==='s'" @click="opencancel(index)" class="btn btn-outline-warning">撤销</a>
                        <a v-if="appoint.astate!=='d'&&appoint.astate!=='s'" @click="del(index)" class="btn btn-outline-danger">删除</a>
                        <a v-if="appoint.astate==='d'" @click="recover(index)" class="btn btn-outline-success">恢复</a>
@endif
                    </div>
                </div>
            </div>
        </div>
        <!--分页-->
        @include('template.paginator')
    </div>
    <p v-if="appoints.length===0">抱歉，查询不到任何预约/报备！</p>
</main>


    <script>        
        const appointlistapp=Vue.createApp({
            data(){
                return{
                    dataname:"appoints",
                    paginator:{},
                    appoints:[],
                    pagenum:{{ $config_appoint['pagenum'] }},
                    appoint:null,

                    index:0,
                    aid:0,
                    applyflag:0,
                    cancelflag:0,
                    approveflag:0,
                    refuseflag:0,
                    msg:"",
                    apinfo:{
                        state_id:0,
                        city_id:0,
                        region_id:0,
                        addr:"",
                        result:0,
                        msg:"",
                    },
                    check:[],

@if (isset($utype)&&$utype==="a")
                    url:"{{ config('var.apl') }}",
                    gurl:"{{ config('var.apg') }}",
@else
                    url:"{{ config('var.pl') }}",
                    gurl:"{{ config('var.pg') }}",
@endif
                    
                    params:{
                        page:"1",
@if (isset($utype)&&$utype==='a')
                        uid:"",
@else
                        uid:{{isset($luser)?$luser->uid:''}},
@endif
                        sid:"",
                        aid:"",
@if(!isset($type))
                        type:["p","a","v"],
@else
                        type:["r"],
@endif
                        msg:"",
                        state:"",
                        order:"atime",
                        desc:"1",
                        start:"2023-01-01T00:00:00",
                        end:"2023-12-31T00:00:00",
                    },
                    paramspre:{},
                    ordertypes:{
                        aid:"按预约/报备编号",
                        atime:"按预约/报备时间",
                    },
@if(!isset($type))
                    services:{!! json_encode($config_station['typep'],JSON_UNESCAPED_UNICODE) !!},
@else
                    services:{!! json_encode($config_station['typer'],JSON_UNESCAPED_UNICODE) !!},
@endif
                    states:{!! json_encode($config_appoint['state'],JSON_UNESCAPED_UNICODE) !!},
                    processtype:{!! json_encode($config_appoint['processtype'],JSON_UNESCAPED_UNICODE) !!},
                }
            },
            mounted(){
                initpaginator(this);
                this.getData();
                this.init();
            },
            methods:{
                init(){
                    const that = this;
                    document.getElementById('info').addEventListener('show.bs.modal',function(event){
                        getData(that.gurl+that.aid,function(json){
                            if(json.data!==null){
                                that.appoint = json.data.appoint;
                                that.appoint.atime = that.appoint.atime.replace(' ','T');
                            }else{
                                $('#info').modal("hide");
                            }
                        },"#msg",null,false);
                    });
                },
                checkall(){
                    let flag=true;
                    for(appoint of this.appoints){
                        if(!this.check.includes(appoint.aid)){
                            this.check.push(appoint.aid);
                            flag=false;
                        }
                    }
                    if(flag===true){
                        this.check.length=0;
                    }
                },

                //重置
                reset(){
                    this.params=this.paramspre={
                        page:"1",
@if (isset($utype)&&$utype==='a')
                        uid:"",
@else
                        uid:{{isset($luser)?$luser->uid:''}},
@endif
                        sid:"",
                        aid:"",
@if(!isset($type))
                        type:["p","a","v"],
@else
                        type:["r"],
@endif
                        msg:"",
                        state:"",
                        order:"atime",
                        desc:"1",
                        start:"2023-01-01T00:00:00",
                        end:"2023-12-31T00:00:00",
                    };
                },
                openinfo(index){
                    this.index=index;
                    this.aid=this.appoints[index].aid;
                    this.applyflag=0;
                    this.cancelflag=0;
                    this.approveflag=0,
                    this.refuseflag=0;
                    $('#info').modal("show");
                },
@if (isset($utype)&&$utype==="a")
                openapprove(index){
                    this.index=index;
                    this.aid=this.appoints[index].aid;
                    this.approveflag=1;
                    this.applyflag=0;
                    this.cancelflag=0;
                    this.refuseflag=0;
                    $('#info').modal("show");
                },
                approve(index){
                    let appoint=this.appoints[index];
                    let that=this;
                    getData("{!! config('var.apa') !!}"+appoint.aid,function(json){
                        if(json.status===1){
                            that.appoints[index].astate="f";
                        }
                    },"#msg");
                },
                openrefuse(index){
                    this.index=index;
                    this.aid=this.appoints[index].aid;
                    this.refuseflag=1;
                    this.applyflag=0;
                    this.cancelflag=0;
                    this.approveflag=0,
                    $('#info').modal("show");
                },
                refuse(){
                    const that=this;
                    const data={
                        msg:that.msg,
                        _token:"{{csrf_token()}}"
                    }
                    getData("{!! config('var.apr') !!}"+that.aid,function(json){
                        if(json.status===1){
                            that.appoints[that.index].astate="r";
                            that.msg="";
                            $('#info').modal("hide");
                        }
                    },"#msg",data);
                },
@else
                openalter(index){
                    alterapp.index=index;
                    alterapp.aid=this.appoints[index].aid;
                    $('#alter').modal("show");
                },
                opencancel(index){
                    this.index=index;
                    this.aid=this.appoints[index].aid;
                    this.cancelflag=1;
                    this.applyflag=0;
                    this.approveflag=0,
                    this.refuseflag=0;
                    $('#info').modal("show");
                },
                openapply(index){
                    this.index=index;
                    this.aid=this.appoints[index].aid;
                    this.applyflag=1;
                    this.cancelflag=0;
                    this.approveflag=0,
                    this.refuseflag=0;
                    $('#info').modal("show");
                },
                del(index){
                    const that=this;
                    const appoint=that.appoints[index];
                    getData("{!! config('var.pd') !!}"+appoint.aid,function(json){
                        if(json.status===1){
                            that.appoints[index].astate="d";
                        }
                    },"#msg");
                },
                recover(index){
                    const that=this;
                    const appoint=that.appoints[index];
                    getData("{!! config('var.pr') !!}"+appoint.aid,function(json){
                        if(json.status===1){
                            that.getData();
                        }
                    },"#msg");
                },
                apply(){
                    const that=this;
                    getData("{!! config('var.pa') !!}"+that.aid,function(json){
                        if(json.status===1){
                            that.appoints[that.index].astate="s";
                            $('#info').modal("hide");
                        }
                    },"#msg");
                },
                cancel(){
                    const that=this;
                    const data={
                        msg:that.msg,
                        _token:"{{csrf_token()}}"
                    }
                    getData("{!! config('var.pc') !!}"+that.aid,function(json){
                        if(json.status===1){
                            that.appoints[that.index].astate="n";
                            that.msg="";
                            $('#info').modal("hide");
                        }
                    },"#msg",data);
                },
@endif
            }
        }).mount('#appointlist');
    </script>