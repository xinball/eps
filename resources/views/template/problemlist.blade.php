<main id="problemlist" class="container list shadow">
    <x-offcanvas>
        <form>
            @if (isset($utype)&&$utype==="a")
            <div class="mb-3 col-12"><a style="width: 100%" class="btn btn-outline-success btn-fill" @click="checklike">对选定项目查重</a></div>
            <div class="mb-3 col-12"><a style="width: 100%" class="btn btn-outline-success btn-fill" @click="rejudge">重判选定项</a></div>
            @endif
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
            <div class="mb-3 col-12">
                <div class="input-group">
                    <button v-for="(tid,index) in tids" type="button" class="btn btn-outline-dark" :key="index" @click="deltid(index)">@{{ tags[tid].tname }} <i class="bi bi-x-lg"></i></button>
                    <select class="form-select" v-model="tid" id="paramstids">
                        <option v-for="tag in tags0" :key="tag.tid" :label="tag.tname" :value="tag.tid">@{{ tag.tname }}</option>
                        <option label="未选择标签" value="0" disabled="disabled">未选择标签</option>
                    </select>
                    <button type="button" class="btn btn-outline-success" @click="inserttid()">添加 <i class="bi bi-plus-lg"></i></button>
                </div>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" id="paramsptitle" class="form-control" v-model="params.ptitle" placeholder="问题标题">
                <label for="paramsptitle">问题标题</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" id="paramspdes" class="form-control" v-model="params.pdes" placeholder="问题描述">
                <label for="paramspdes">问题描述</label>
            </div>
        </form>
    </x-offcanvas>

    <div v-if="problems.length>0">
        <x-modal id='info' title="题目信息">
            <div v-if="problem!==null" class="modal-body" style="word-wrap:break-word;word-break:break-all;over-flow:hidden;">
                <div class="modal-body" style="text-align:left;">
                    编号：@{{ problem.pid }}<br>
                    标题：@{{ problem.ptitle }}<br>
                    描述：@{{ problem.pdes }}<br>
                    提交：@{{ problem.psubmit }}<br>
                    AC数量：@{{ problem.pac }}<br>
                    <div style="text-align:center;">
                        <button type="button" v-for="tid in problem.tids"  class="btn btn-outline-dark" style="margin:5px;" @click="inserttid(tid)" data-bs-toggle="popover" title="标签信息" :data-bs-content="tags[tid].tdes">@{{ tags[tid].tname }}</button>
                    </div>
                </div>
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
                    <div class="d-none d-md-block col-md-1">#</div>
                    <div class="col-5">标题</div>
                    <div class="col-3">描述</div>
                    <div class="d-none d-sm-block col-sm-1">提交</div>
                    <div class="col-4 col-sm-3 col-md-2">AC率(%)</div>
                </div>
            </div>
        </div>
        <div class="row item list-group-item list-group-item-action " v-for="(problem,index) in problems" style="display: flex;" :class="{'active':check.includes(problem.pid)}"  :key="index" >
        @if (isset($utype)&&($utype==='a'||$utype==='u'))
            <div class="col-1" >
                <input type="checkbox" :value="problem.pid" v-model="check" >
                <a v-if="problem.ptype!=='d'" class="btn btn-danger" @click="del(index)" style="margin-left:20px; font-size:xx-small;"><i class="bi bi-trash3-fill"></i></a>
                <a v-else class="btn btn-success" @click="recover(index)" style="margin-left:20px; font-size:xx-small;" ><i class="bi bi-arrow-repeat"></i></a>
            </div>
        @endif
            <div data-bs-toggle="modal" :data-bs-index="index" 
        @if (isset($utype)&&($utype==="a"||$utype==='u'))
                class="row text-center col-11" title="点击编辑问题信息" data-bs-target="#alter" :data-bs-pid="problem.pid" 
        @else
                class="row text-center col-12"
                title="点击查看题目信息" data-bs-target="#info" @click="openinfo($event)"
        @endif >
                <div class="d-none d-md-block col-md-1 thead" >@{{ problem.pid }}</div>
                <div class="col-5">
                    <a class="btn btn-light text-truncate" style="width: 95%;" :title="problem.ptitle" :href="'/problem/'+problem.pid" >@{{ problem.ptitle }}</a>
                </div>
                <div class="col-3 text-truncate" style="vertical-align: middle;align-self:center;" :title="problem.pdes">@{{ problem.pdes }}</div>
                <div class="d-none d-sm-block col-sm-1" style="vertical-align: middle;align-self:center;">@{{ problem.psubmit }}</div>
                <div class="col-4 col-sm-3 col-md-2" style="vertical-align: middle;align-self:center;">@{{ (problem.pacrate*100).toFixed(3) }}</div>

            </div>
        </div>

        @include('template.paginator')
    </div>
    <p v-if="problems.length===0">抱歉，查询不到任何题目！</p>
</main>
    <script>
        const problemlist=Vue.createApp({
            data(){
                return{
                    dataname:"problems",
                    @if (isset($utype)&&$utype==="a")
                    url:"{{ config('var.apl') }}",
                    utype:'a',
                    @elseif (isset($utype)&&$utype==="u")
                    utype:'u',
                    url:"{{ config('var.pil') }}",
                    @else
                    utype:'u',
                    url:"{{ config('var.pl') }}",
                    @endif
                    paginator:{},
                    problems:[],
                    pagenum:{{ $config_problem['pagenum'] }},
                    paramspre:{
                        page:"1",
                        pdes:"",
                        ptitle:"",
                        type:"",
                        tids:"[]",
                        order:"0",
                    },
                    problem:null,
                    check:[],
                    
                    params:{
                        page:"1",
                        pdes:"",
                        ptitle:"",
                        type:"",
                        tids:"[]",
                        order:"0",
                    },
                    ordertypes:{
                        psubmit:"按提交数量排序",
                        pac:"按AC数量排序",
                        pacrate:"按AC率排序",
                    },
                    tags:[],
                    tags0:[],
                    tid:"0",
                    tids:[],
                }
            },
            mounted(){
                initpaginator(this);
                this.getData();
            },
            methods:{
                del(index){
                    let problem=this.problems[index];
                    let that=this;
                    getData('{!! config('var.pd') !!}'+problem.pid+'?utype='+this.utype,function(json){
                        if(json.status===1){
                            that.problems[index].ptype="d";
                        }
                    },"#msg");
                },
                recover(index){
                    let problem=this.problems[index];
                    let that=this;
                    getData('{!! config('var.pr') !!}'+problem.pid+'?utype='+this.utype,function(json){
                        if(json.status===1){
                            that.problems[index].ptype="m";
                        }
                    },"#msg");
                },
                openinfo(event){
                    this.problem = Object.assign({},this.problems[event.currentTarget.getAttribute('data-bs-index')]);
                    this.problem.tids=isJSON(this.problem.tids);
                },
                inserttid(tid=""){
                    if(tid!=="")
                        this.tid=tid;
                    if(!this.tids.includes(this.tid)){
                        if(this.tids.length>=6){
                            echoMsg("#msg",{status:4,message:"标签数量不得超过6个"});
                        }else if(this.tid in this.tags){
                            this.tids.push(this.tid);
                            echoMsg("#msg",{status:1,message:"标签添加查询成功！"});
                        }
                        this.params.tids=JSON.stringify(this.tids);
                    }else{
                        echoMsg("#msg",{status:4,message:"该标签已添加"});
                    }
                },
                checkall(){
                    let flag=true;
                    for(problem of this.problems){
                        if(!this.check.includes(problem.pid)){
                            this.check.push(problem.pid);
                            flag=false;
                        }
                    }
                    if(flag===true){
                        this.check.length=0;
                    }
                },
                checklike(){
                    let data={
                        pids:JSON.stringify(this.check),
                        _token:"{{ csrf_token() }}"
                    };
                    getData("{{ config('var.jc') }}",null,"#msg",data);
                },
                rejudge(){
                    let data={
                        pids:JSON.stringify(this.check),
                        _token:"{{ csrf_token() }}"
                    };
                    getData("{{ config('var.jr') }}"+'1',null,"#msg",data);
                },
                deltid(index){
                    this.tids.splice(index,1);
                    this.params.tids=JSON.stringify(this.tids);
                },
                reset(){
                    this.params=this.paramspre={
                        page:"1",
                        pdes:"",
                        ptitle:"",
                        type:"",
                        tids:"[]",
                        order:"0",
                    };
                    this.tids=[];
                },
            }
        }).mount('#problemlist');
    </script>