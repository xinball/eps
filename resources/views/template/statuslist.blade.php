<main id="statuslist" class="container list shadow">
@if (isset($utype))
<x-card header="状态" icon="bi bi-heart-pulse">
    <div class="row" >
        <div class="col-lg-4 col-12" id="sta" style="height: 300px;"></div>
        <div class="col-lg-4 col-12" id="usta" style="height: 300px;"></div>
        <div class="col-lg-4 col-12" id="asta" style="height: 300px;"></div>
    </div>
</x-card>
    
@endif
    <x-offcanvas>
        <form>
            @if (isset($utype))
            <div class="mb-3 col-12"><a style="width: 100%" class="btn btn-outline-success btn-fill" @click="rejudge">重判选定项</a></div>
            @endif
            <div class="mb-3 col-12">
                <div class="input-group">
                    <select class="form-select" v-model="params.order">
                        <option v-for="(ordertype,index) in ordertypes" :key="index" :label="ordertype" :value="index">@{{ ordertype }}</option>
                        <option value="0" label="未选择排序方式">未选择排序方式</option>
                    </select>
                    <button type="button" class="btn btn-outline-info" @click="reset">重置 <i class="bi bi-arrow-clockwise"></i></button>
                    <button type="button" class="btn btn-outline-success" data-bs-dismiss="offcanvas"   @click="getData(params)">查询 <i class="bi bi-search"></i></button>
                </div>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" class="form-control" v-model="params.suid" id="paramssuid" placeholder="公告标题">
                <label for="paramssuid">用户名/邮箱/UID</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" class="form-control" v-model="params.spid"  id="paramsspid" placeholder="问题ID">
                <label for="paramsspid">问题ID</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" class="form-control" v-model="params.scid"  id="paramsscid" placeholder="比赛ID">
                <label for="paramsscid">比赛ID</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="datetime-local" class="form-control" v-model="params.sstart" id="paramssstart" placeholder="开始时间范围">
                <label for="paramssstart">开始时间范围</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="datetime-local" class="form-control" v-model="params.send" id="paramssend" placeholder="结束时间范围">
                <label for="paramssend">结束时间范围</label>
            </div>
            @if (isset($utype)&&$utype==="a")
            @else
            @endif
            <div class="mb-3 col-12 form-floating">
                <select class="form-select" v-model="params.slang" id="paramsslang">
                    <option v-for="(slang,index) in slangs" :key="index" :label="slang" :value="index">@{{ slang }}</option>
                    <option value="0" label="未选择编程语言" disabled="disabled">未选择编程语言</option>
                </select>
                <label for="paramsslang">编程语言</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <select class="form-select" v-model="params.type" id="paramstype">
                    <option v-for="(sresult,index) in sresults" :key="index" :label="sresult" :value="index">@{{ sresult }}</option>
                    <option value="0" label="未选择状态结果类型">未选择状态结果类型</option>
                </select>
                <label for="paramstype">状态结果类型</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <select class="form-select" v-model="params.stype" id="paramsstype">
                    <option v-for="(stype,index) in stypes" :key="index" :label="stype" :value="index">@{{ stype }}</option>
                    <option value="0" label="未选择状态类型">未选择状态类型</option>
                </select>
                <label for="paramsstype">状态类型</label>
            </div>
        </form>    
    </x-offcanvas>
    @if (isset($utype)&&$utype==='a')
    <div class="">
        <button v-for="(dis,index) in adis" style="margin-left:10px;" @click="settype(index)" class="btn" :class="[params.type===index?'btn-'+dis.btn:'btn-outline-'+dis.btn]" :disabled="data.num[index]===0">
            @{{ dis.label }} <span v-if="data.num[index]>0" :class="dis.num">@{{ data.num[index] }}</span>
        </button>
    </div>
    @endif
    <div v-if="statuses.length>0">
        <x-modal id='info' title="状态信息">
            <div v-if="status!==null" class="modal-body" style="word-wrap:break-word;word-break:break-all;over-flow:hidden;">
                {{-- <div class='modal-body' style='text-align:left;'>
                    标题：@{{ notice.ntitle }}<br>
                    描述：@{{ notice.ndes }}<br>
                    发布时间：@{{ notice.ntime }}<br>
                    @{{ notice.nupdate===notice.ntime?"":"更新时间："+notice.nupdate }}<br>
                </div> --}}
            </div>
        </x-modal>
        <div class="item thead-dark thead">
            <div class="row">
                @if (isset($utype)&&($utype==='a'||$utype==='u'))
                <div class="col-1" @click="checkall"><a class="btn btn-outline-dark"><i class="bi bi-check-lg"></i></a></div>
                <div class="col-11 text-center row">
                @else
                <div class="col-12 text-center row">
                @endif
                <div class="col-4 col-md-3"># 结果</div>
                <div class="col-4 col-sm-3 col-lg-2">问题</div>
                <div class="col-4 col-sm-3 col-md-2">作者</div>
                <div class="d-none d-sm-block col-2 col-lg-1">运行时间</div>
                <div class="d-none d-lg-block col-1">运行空间</div>
                <div class="d-none d-lg-block col-1">语言</div>
                <div class="d-none d-lg-block col-1">代码长度</div>
                <div class="d-none d-md-block col-2 col-lg-1">时间</div>
            </div>
        </div>
        <div class="item text-center list-group-item-action" v-for="(status,index) in statuses" style="display: flex;" :key="index" :class="{'active':check.includes(status.sid)}" >
            @if (isset($utype)&&$utype==='a')
                <div class="col-1" >
                    <input type="checkbox" :value="status.sid" v-model="check" >
                </div>
                <div class="row col-11">
            @elseif (isset($utype)&&$utype==='u'&&isset($luser)&&$luser!==null)
                <div class="col-1">
                    <input v-if="status.suid==={{ $luser->uid }}" type="checkbox" :value="status.sid" v-model="check" >
                </div>
                <div class="row col-11">
            @else
                <div class="row col-12">
            @endif
                {{-- <div class="d-none d-sm-block col-sm-1 thead" ><a class="btn btn-light text-truncate" :href="'/status/'+status.spid" >@{{ status.sid }}</a></div> --}}
                <div class="col-4 col-md-3 thead" style="vertical-align: middle;align-self:center;" @click="openinfo(index)">
                    <select v-model="status.sresult" style="font-size: small" class="btn noexpand" :class="['btn-'+adis[status.sresult]['btn']]" >
                        <option v-for="(sresult,index) in sresults" :key="index" :label="'#'+status.sid+' '+sresult+'('+status.score+')'" :value="index">@{{ '#'+status.sid+' '+sresult+'('+status.score+')' }}</option>
                    </select>
                </div>
                <div class="col-4 col-md-3 col-lg-2"><a class="btn btn-light text-truncate" style="width:95%;" :title="status.ptitle" :href="'/problem/'+status.spid" >@{{ '#'+status.spid+' '+status.ptitle }}</a></div>
                <div class="col-4 col-sm-3 col-md-2"><a class="btn btn-light text-truncate" style="width:95%;" :title="status.uname" :href="'/user/'+status.suid" >@{{ status.uname }}</a></div>
                <div class="d-none d-sm-block col-2 col-lg-1" style="vertical-align: middle;align-self:center;" @click="setorder('stime')">@{{ status.stime }}ms</div>
                <div class="d-none d-lg-block col-1" style="vertical-align: middle;align-self:center;" @click="setorder('sspace')">@{{ Math.ceil(status.sspace/1024) }}KB</div>
                <div class="d-none d-lg-block col-1" style="vertical-align: middle;align-self:center;" @click="setslang(status.slang)">@{{ slangs[status.slang] }}</div>
                <div class="d-none d-lg-block col-1" style="vertical-align: middle;align-self:center;" @click="setorder('slen')">@{{ status.slen }}</div>
                <div class="d-none d-md-block col-2 col-lg-1" style="vertical-align: middle;align-self:center;font-size:x-small;" @click="setorder('screate')">@{{ time.create[index].status+(time.create[index].length!==null?"前":"") }}</div>

            </div>
        </div>
        
        @include('template.paginator')
    </div>
    <p v-if="statuses.length===0">抱歉，查询不到任何状态！</p>

</main>

<script>
    const statuslist=Vue.createApp({
        data(){
            return{
                timer:null,
                time:{
                    before:7*86400000,
                    length:1000,
                    create:[]
                },
                check:[],

                @if (isset($utype)&&$utype==="a")
                url:"{!! config('var.asl') !!}",
                typekey:{!! json_encode($config_status['resultkey']['total']) !!},
                @else
                url:"{{ config('var.sl') }}",
                typekey:{!! json_encode($config_status['resultkey']['all']) !!},
                @endif
                dataname:"statuses",
                paginator:{},
                pagenum:{{ $config_status['pagenum'] }},
                data:{num:{sum:0}},
                statuses:[],
                status:null,
                sresults:isJSON({!! json_encode($config_status['results'],JSON_UNESCAPED_UNICODE) !!}),
                stypes:isJSON({!! json_encode($config_status['stypes'],JSON_UNESCAPED_UNICODE) !!}),
                slangs:isJSON({!! json_encode($config_status['langs'],JSON_UNESCAPED_UNICODE) !!}),
                adis:isJSON({!! json_encode($config_status['adis'],JSON_UNESCAPED_UNICODE) !!}),
                paramspre:{
                    page:"1",
                    spid:"",
                    suid:"",
                    scid:"",
                    slang:"0",
                    type:"0",
                    stype:"0",
                    sstart:"2021-01-01T00:00:00",
                    send:"2023-12-31T00:00:00",
                    order:"0",
                },
                params:{
                    page:"1",
                    spid:"",
                    scid:"",
                    suid:"",
                    slang:"0",
                    type:"0",
                    stype:"0",
                    sstart:"2021-01-01T00:00:00",
                    send:"2023-12-31T00:00:00",
                    order:"0",
                },
                ordertypes:{
                    score:"按得分排序",
                    stime:"按运行时间排序",
                    sspace:"按内存空间排序",
                    slen:"按代码长度排序",
                    screate:"按提交时间排序",
                },
            }
        },
        mounted(){
            filterTypes(this.sresults,this.typekey);
            initpaginator(this);
            let that=this;
            this.getData();
        },
        computed:{
        },
        methods:{
            initStatus(){
                for(i in this.statuses){
                    let status=this.statuses[i];
                    this.time.create[i]={
                        length:(new Date()).getTime()-getDate(status.screate).getTime(),
                        status:""
                    };
                    this.getStatus(status.screate,this.time.create[i]);
                }
                clearInterval(this.timer);
                this.timer=setInterval(() => {
                    this.refreshStatus()
                }, 1000);
            },
            getStatus(date,send){
                if(send.length!==null){
                    if(send.length>this.time.before){
                        send.length=null;
                        send.status=date;
                    }else if(send.length>=0){
                        send.status=getSubTime(0,send.length);
                    }
                }
            },
            refreshStatus(){
                for(i in this.statuses){
                    let status=this.statuses[i];
                    if(this.time.create[i].length!==null){
                        this.time.create[i].length+=this.time.length;
                        this.getStatus(status.screate,this.time.create[i])
                    }
                }
            },
            openinfo(index){
                this.status = Object.assign({},this.statuses[index]);
                // window.location.href="/status/"+this.status.sid;
                window.open("/status/"+this.status.sid)
            },
            setorder(order){
                this.params.order=order;
            },
            setslang(slang){
                this.params.slang=slang;
            },
            rejudge(){
                for(i of this.check){
                getData("{{ config('var.jur') }}"+i,null,"#msg",null,jump=false);
                }
            },
            reset(){
                this.params=this.paramspre={
                    page:"1",
                    spid:"",
                    scid:"",
                    suid:"",
                    slang:"0",
                    type:"0",
                    stype:"0",
                    sstart:"2021-01-01T00:00:00",
                    send:"2023-12-31T00:00:00",
                    order:"0",
                };
            },
            checkall(){
                let flag=true;
                for(i in this.statuses){
                    const status=this.statuses[i];
                    console.log(status)
                    @if (isset($utype)&&$utype==='u'&&isset($luser)&&$luser!==null)
                    if(!this.check.includes(status.sid)&&status.suid==={{ $luser->uid }}){
                    @else
                    if(!this.check.includes(status.sid)){
                    @endif
                        this.check.push(status.sid);
                        flag=false;
                    }
                }
                if(flag===true){
                    this.check.length=0;
                }
            },
        }
    }).mount('#statuslist');
    //current_page,first_page_url,last_page,last_page_url,next_page_url,path,per_page,prev_page_url,from,to,total
</script>