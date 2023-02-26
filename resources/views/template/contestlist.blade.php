
<main id="contestlist" class="container list shadow">
    <x-modal id="pwdmodal" title="确认比赛密码">
        <div class="text-center p-4 pb-4">
            <div class="form-floating">
                <input class="form-control" id="pwd" type="password" v-model="pwd">
                <label for="pwd">请输入比赛密码【已参赛者无需输入】</label>
            </div>
        </div>
        <x-slot name="footer">
            <a class="btn btn-outline-dark" @click="joinpwd()">确定</a>
        </x-slot>
    </x-modal>
    <x-offcanvas>
        <form>
            @if (isset($utype)&&$utype==="a")
            <div class="mb-3 col-12"><a style="width: 100%" class="btn btn-outline-success btn-fill" @click="checklike">对选定项目查重</a></div>
            <div class="mb-3 col-12"><a style="width: 100%" class="btn btn-outline-success btn-fill" @click="rejudge">重判选定项</a></div>
            @endif
            <div class="mb-3 col-12">
                <div class="input-group">
                    <select class="form-select" v-model="params.order">
                        <option v-for="(ordertype,index) in ordertypes" :key="index" :label="ordertype" :value="index">@{{ ordertype }}</option>
                        <option value="0" label="未选择排序方式" disabled="disabled">未选择排序方式</option>
                    </select>
                    <button type="button" class="btn btn-outline-info" @click="reset">重置 <i class="bi bi-arrow-clockwise"></i></button>
                    <button type="button" class="btn btn-outline-success" data-bs-dismiss="offcanvas"   @click="getData(params)">查询 <i class="bi bi-search"></i></button>
                </div>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" class="form-control" v-model="params.ctitle" id="paramsctitle" placeholder="比赛标题">
                <label for="paramsctitle" >比赛标题</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" id="paramscdes" class="form-control" v-model="params.cdes" placeholder="比赛描述">
                <label for="paramscdes">比赛描述</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input id="paramscstart" type="datetime-local" class="form-control" v-model="params.cstart" placeholder="">
                <label for="paramscstart">开始时间范围</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="datetime-local" class="form-control" v-model="params.cend" id="paramscend" placeholder="结束时间范围">
                <label for="paramscend">结束时间范围</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <select class="form-select" v-model="params.pwd" id="paramspwd">
                    <option value="true" label="加密">加密</option>
                    <option value="false" label="不加密">不加密</option>
                    <option value="0" label="未选择是否加密">未选择是否加密</option>
                </select>
                <label for="paramspwd">比赛加密</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <select class="form-select" v-model="params.rule" id="paramsrule">
                    <option value="a" label="ACM">ACM</option>
                    <option value="i" label="IOI">IOI</option>
                    <option value="0" label="未选择赛制">未选择赛制</option>
                </select>
                <label for="paramsrule">比赛规则</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <select class="form-select" v-model="params.rtrank" id="paramsrtrank">
                    <option value="true" label="实时排名">实时排名</option>
                    <option value="false" label="不实时排名">不显示实时排名</option>
                    <option value="0" label="未选择是否实时排名">未选择是否实时排名</option>
                </select>
                <label for="paramsrtrank">实时排名</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <select class="form-select" v-model="params.numlimit" id="paramsnumlimit">
                    <option value="true" >有提交次数限制</option>
                    <option value="false" >无提交次数限制</option>
                    <option value="0" label="未选择是否提交次数限制">未选择是否提交次数限制</option>
                </select>
                <label for="paramsnumlimit">提交次数限制</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <select class="form-select" v-model="params.punish" id="paramspunish">
                    <option value="true" >有罚时</option>
                    <option value="false" >无罚时</option>
                    <option value="0" label="未选择是否罚时">未选择是否罚时</option>
                </select>
                <label for="paramspunish">罚时</label>
            </div>
        </form>
    </x-offcanvas>
@if (isset($utype)&&in_array($utype,['a','u']))
    @if ($utype==='u'&&$u==='i')
<a @click="checkall" style="float: left;position:relatve;" class="btn btn-outline-dark"><i class="bi bi-check-lg"></i></a>
    @endif
<div class="">
    <button v-for="(dis,index) in dises" style="margin-left:10px;" @click="settype(index)" class="btn" :class="[params.type===index?'btn-'+dis.btn:'btn-outline-'+dis.btn]" :disabled="data.num[index]===0">
        @{{ dis.label }} <span v-if="data.num[index]>0" :class="dis.num">@{{ data.num[index] }}</span>
    </button>
</div>
@endif
    <div v-if="contests.length>0">
        <x-modal id='info' title="比赛信息">
            <div v-if="contest!==null" class="modal-body" style="word-wrap:break-word;word-break:break-all;over-flow:hidden;">
                <div class='modal-body' style='text-align:left;'>
                    ID：@{{ contest.cid }}<br>
                    标题：@{{ contest.ctitle }}<br>
                    描述：@{{ contest.cdes }}<br>
                    开始时间：@{{ contest.cstart }}<br>
                    结束时间：@{{ contest.cend }}<br>
                    比赛时长：@{{ contest.length }}<br>
                </div>
            </div>
        </x-modal>
        <div v-for="(contest,index) in contests" :key="index" class="row item list-group-item list-group-item-action" :class="{'active':check.includes(contest.cid)}" style="display: flex;">
            <div class="col-2" style="align-self: center;max-width: 20%;text-align: right;">
                @if (isset($utype)&&($utype==='a'||($utype==='u'&&isset($u)&&$u==='i')))
                <input type="checkbox" :value="contest.cid" v-model="check" style="float: left;position: relative;left: 10px;">
                @endif
                <div style="float:right;text-align:center;height:48px;width:48px;">
                    <img  :src="contest.img" alt="比赛图片" height="48" width="48" style="font-size: 46px;border-radius:5px;" >
                </div>
            </div>
            <div class="col-10 row d-flex justify-content-between" style="max-width: 75%;">
                <div class="col-8" data-bs-toggle="modal" style="text-align:left;" 
    @if (isset($utype)&&($utype==="a"||($utype==='u'&&isset($u)&&$u==='i')))
            title="点击编辑比赛信息" data-bs-target="#alter" :data-bs-cid="contest.cid"
    @else
            title="点击查看比赛信息" data-bs-target="#info" @click="openinfo(index)"
    @endif>
                    <h5 class="mb-0 text-truncate" :title="contest.ctitle"><i v-if="contest.coption.pwd!==false" class="bi bi-shield-lock-fill text-pink"></i> @{{ contest.ctitle }} </h5>
                    <h6 class="mb-0 opacity-75 text-truncate" :title="contest.cdes"><span v-if="typekey.b.includes(contest.ctype)" class="badge bg-warning" style="font-size:x-small;">测试</span><span v-if="typekey.u.includes(contest.ctype)" class="badge bg-secondary" style="font-size:x-small;">测试</span> @{{ contest.cdes }}</h6>
                    <span class="opacity-65 text-nowrap text-truncate mb-0" style="font-size: small;">
                        <i @click="setrule(contest.coption.rule)" class="badge bg-dark">@{{ contest.coption.rule==='a'?'ACM':'IOI' }}</i>&nbsp;
                        <i class="bi bi-calendar-event text-info"></i> @{{ contest.cstart }}
                        <i class="bi bi-hourglass-split text-info"></i>@{{ getTimeLength(index) }}&nbsp;
                        <i class="bi bi-person-fill text-info"></i>@{{ contest.cnum }}
                    </span>
                </div>
                <p class="col-4 opacity-75 text-nowrap ">
                    <a v-if="typekey.a.includes(contest.ctype)&&starttimes[index]<0" aria-hidden="true" class="btn rounded-pill btn-outline-secondary" @click="join(index)" >未开始</a>
                    <a v-else-if="typekey.a.includes(contest.ctype)&&endtimes[index]>0" aria-hidden="true" class="btn rounded-pill btn-outline-danger" @click="join(index)">已结束</a>
                    <a v-else-if="typekey.a.includes(contest.ctype)" class="btn rounded-pill btn-outline-success"  @click="join(index)">进行中 >> </a>
                    <a v-if="typekey.a.includes(contest.ctype)===false"  class="btn rounded-pill btn-outline-primary" :href="'/contest/'+contest.cid">查看</a>
                    @if(isset($utype)&&$utype==='a')
                    <a v-if="typekey.b.includes(contest.ctype)"  class="btn rounded-pill btn-outline-success" @click="approve(index)">同意申请</a>
                    <a v-if="typekey.b.includes(contest.ctype)"  class="btn rounded-pill btn-outline-warning" @click="refuse(index)">拒绝申请</a>
                    <a v-if="typekey.a.includes(contest.ctype)"  class="btn rounded-pill btn-outline-warning" @click="refuse(index)">下线</a>
                    <a v-if="typekey.a.includes(contest.ctype)"  class="btn rounded-pill btn-outline-danger" @click="del(index)">删除</a>
                    <a v-if="typekey.d.includes(contest.ctype)"  class="btn rounded-pill btn-outline-success" @click="recover(index)">恢复</a>
                    @elseif(isset($utype)&&$utype==='u'&&isset($u)&&$u==='i')
                    <a v-if="typekey.u.includes(contest.ctype)"  class="btn rounded-pill btn-outline-success" @click="apply(index)">申请</a>
                    <a v-if="typekey.b.includes(contest.ctype)"  class="btn rounded-pill btn-outline-warning" @click="cancel(index)">撤销申请</a>
                    <a v-if="typekey.a.includes(contest.ctype)"  class="btn rounded-pill btn-outline-warning" @click="cancel(index)">下线</a>
                    @endif
                </p>
            </div>
        </div>
        @include('template.paginator')
    </div>
        <p v-if="contests.length==0">抱歉，查询不到任何比赛！</p>

</main>

<script>
    const contestlist=Vue.createApp({
        data(){
            return{
                timer:null,
                starttimes:[],
                endtimes:[],

                pwd:"",
                cid:"",
                url:"",
                dataname:"contests",
                paginator:{},
                pagenum:{{ $config_contest['pagenum'] }},
                contests:[],
                contest:null,
                typekey:{!! json_encode($config_contest['typekey']) !!},

                check:[],

                data:{num:{o:0,s:0,c:0,e:0,a:0,b:0,d:0,sum:0}},

                u:"",
                paramspre:{
                    u:"",
                    page:"1",
                    type:"sum",
                    cdes:"",
                    ctitle:"",
                    rule:"0",
                    pwd:"0",
                    rtrank:"0",
                    punish:"0",
                    numlimit:"0",
                    order:"0",
                    cstart:"2021-01-01T00:00:00",
                    cend:"2023-12-31T00:00:00",
                },
                params:{
                    u:"",
                    page:"1",
                    type:"sum",
                    cdes:"",
                    ctitle:"",
                    rule:"0",
                    pwd:"0",
                    rtrank:"0",
                    punish:"0",
                    numlimit:"0",
                    order:"0",
                    cstart:"2021-01-01T00:00:00",
                    cend:"2023-12-31T00:00:00",
                },
                ordertypes:{
                    cstart:"按开始时间排序",
                    cnum:"比赛参与人数",
                },
            }
        },
        mounted(){
        @if (isset($utype)&&$utype==="a")
            this.url="{{ config('var.acl') }}";
            this.u=this.params.u='';
            this.dises=isJSON({!! json_encode($config_contest['adis'],JSON_UNESCAPED_UNICODE) !!});
        @elseif(isset($utype)&&$utype==="u")
            this.url="{{ config('var.cl') }}";
            @if (isset($u)&&in_array($u,['i','j']))
            this.u=this.params.u='{!! $u !!}';
            this.dises=isJSON({!! json_encode($config_contest['u'.$u.'dis'],JSON_UNESCAPED_UNICODE) !!});
            @else
            this.url="{{ config('var.cl') }}";
            this.u="";
            @endif
        @else
            this.url="{{ config('var.cl') }}";
            this.u="";
        @endif
            initpaginator(this);
            this.getData();
        },
        computed:{
        },
        methods:{
            initStatus(){
                for(i in this.contests){
                    this.starttimes[i]=((new Date()).getTime()-getDate(this.contests[i].cstart).getTime())/1000;
                    this.endtimes[i]=((new Date()).getTime()-getDate(this.contests[i].cend).getTime())/1000;
                }
                clearInterval(this.timer);
                this.timer=setInterval(() => {
                    this.refreshStatus()
                }, 1000);
            },
            refreshStatus(){
                for(i in this.contests){
                    this.starttimes[i]++;
                    this.endtimes[i]++;
                }
            },
            openinfo(index){
                this.contest = Object.assign({},this.contests[index]);
                this.contest.length=this.getTimeLength(index);
            },
            getTimeLength(index){
                return getSubTime(getDate(this.contests[index].cstart).getTime(),getDate(this.contests[index].cend).getTime());
            },
            setrule(rule){
                this.params.rule=rule;
                this.getData();
            },
            addcheck(cid){
                if(!this.check.includes(cid))
                    this.check.push(cid);
                else
                    this.check.splice(this.check.indexOf(cid),1);
            },
            checkall(){
                let flag=true;
                for(contest of this.contests){
                    if(!this.check.includes(contest.cid)){
                        this.check.push(contest.cid);
                        flag=false;
                    }
                }
                if(flag===true){
                    this.check.length=0;
                }
            },
            checklike(){
                let data={
                    cids:JSON.stringify(this.check),
                    _token:"{{ csrf_token() }}"
                };
                getData("{{ config('var.jc') }}",null,"#msg",data);
            },
            reset(){
                let that=this;
                this.params=this.paramspre={
                    u:that.u,
                    page:"1",
                    type:"sum",
                    cdes:"",
                    ctitle:"",
                    rule:"0",
                    pwd:"0",
                    rtrank:"0",
                    punish:"0",
                    numlimit:"0",
                    order:"0",
                    cstart:"2021-01-01T00:00:00",
                    cend:"2023-12-31T00:00:00",
                };
            },
            approve(index){
                let contest=this.contests[index];
                let that=this;
                getData('{!! config('var.acar') !!}'+contest.cid,function(json){
                    if(json.status===1){
                        that.contests[index].ctype=that.typekey.o.includes(contest.ctype)?"o":"s";
                    }
                },"#msg");
            },
            refuse(index){
                let contest=this.contests[index];
                let that=this;
                getData('{!! config('var.acrf') !!}'+contest.cid,function(json){
                    if(json.status===1){
                        that.contests[index].ctype=that.typekey.o.includes(contest.ctype)?"c":"e";
                    }
                },"#msg");
            },
            del(index){
                let contest=this.contests[index];
                let that=this;
                getData('{!! config('var.acd') !!}'+contest.cid,function(json){
                    if(json.status===1){
                        that.contests[index].ctype="d";
                    }
                },"#msg");
            },
            recover(index){
                let contest=this.contests[index];
                let that=this;
                getData('{!! config('var.acr') !!}'+contest.cid,function(json){
                    if(json.status===1){
                        that.contests[index].ctype="c";
                    }
                },"#msg");
            },
            apply(index){
                let contest=this.contests[index];
                let that=this;
                getData('{!! config('var.cal') !!}'+contest.cid,function(json){
                    if(json.status===1){
                        that.contests[index].ctype=that.typekey.o.includes(contest.ctype)?"a":"b";
                    }
                },"#msg");
            },
            cancel(index){
                let contest=this.contests[index];
                let that=this;
                getData('{!! config('var.ccl') !!}'+contest.cid,function(json){
                    if(json.status===1){
                        that.contests[index].ctype=that.typekey.o.includes(contest.ctype)?"c":"e";
                    }
                },"#msg");
            },
            checklike(){
            },
            rejudge(){
                let data={
                    cids:JSON.stringify(this.check),
                    _token:"{{ csrf_token() }}"
                };
                getData("{{ config('var.jr') }}"+'1',null,"#msg",data);
            },
            join(index){
                let contest=this.contests[index];
                this.cid=contest.cid;
                this.pwd="";
                @if (!isset($ladmin)||$ladmin===null)
                if(contest.coption.pwd!==false){
                    const pwdmodal = new bootstrap.Modal(document.getElementById('pwdmodal'));
                    pwdmodal.show();
                }else{
                    this.joinpwd();
                }
                @else
                    this.joinpwd();
                @endif
            },
            joinpwd(){
                getData('{!! config('var.cj') !!}'+this.cid+(pwd===""?"":"?pwd="+this.pwd),null,"#msg");
            }
        }
    }).mount('#contestlist');
    //current_page,first_page_url,last_page,last_page_url,next_page_url,path,per_page,prev_page_url,from,to,total
</script>