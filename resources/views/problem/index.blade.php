@extends('template.master')

@section('title','问题')

@section('nextCSS')
    <link rel="stylesheet" href="{{ "/codemirror-5.65.2/lib/codemirror.css" }}"/>
    <link rel="stylesheet" href="{{ "/codemirror-5.65.2/theme/dracula.css" }}"/>
    <link rel="stylesheet" href="{{ "/codemirror-5.65.2/theme/material.css" }}"/>
    <link rel="stylesheet" href="{{ "/codemirror-5.65.2/theme/eclipse.css" }}"/>
    <link rel="stylesheet" href="{{ "/codemirror-5.65.2/theme/idea.css" }}"/>

    <link rel="stylesheet" href="{{ '/codemirror-5.65.2/addon/fold/foldgutter.css' }}"/>
    <link rel="stylesheet" href="{{ '/codemirror-5.65.2/addon/hint/show-hint.css' }}"/>

@endsection

@section('body')
    <main id="problem" class="container p-3 pb-3">
        <div class="row">
            <div class="article col-xl-9 col-lg-8 col-md-7 col-12">
                <div class="break">
                    <h1>@{{ problem.ptitle }}</h1>
                    <h4>@{{ problem.pdes }}</h4>    
                </div>
                <hr class="my-4">
                <h4 class="mb-3 text-info">描述</h4>
                <div class="break" style="padding: 20px;" v-html="problem.pinfo"></div>
                <hr class="my-4">
                <h4 class="mb-3 text-info">输入描述</h4>
                <div v-html="problem.poption.in"></div>
                <hr class="my-4">
                <h4 class="mb-3 text-info">输出描述</h4>
                <div v-html="problem.poption.out"></div>
                <hr class="my-4">
                <h4 class="mb-3 text-info">提示</h4>
                <div v-html="problem.poption.tip"></div>
                <hr class="my-4">
                <h4 class="mb-3 text-info">来源</h4>
                <div>@{{ problem.poption.source!==''?problem.poption.source:"无" }}</div>
                <hr class="my-4">
                <div v-for="(casei,index) in problem.poption.cases" class="row">
                    <label class="h5 col-6 text-info" :for="'casein'+index">示例输入 @{{ index+1 }} <i class="bi bi-clipboard-fill"></i></label>
                    <label class="h5 col-6 text-info" :for="'caseout'+index">示例输出 @{{ index+1 }}</label>
                    <div class="col-12 input-group mb-4">
                    <textarea :id="'casein'+index" rows="4" @click="copy($event)" style="resize:none;" class="form-control" readonly>@{{ casei.in }}</textarea>
                    <textarea :id="'caseout'+index" rows="4" style="resize:none;" class="form-control" readonly>@{{ casei.out }}</textarea></div>
                    <textarea id="copy" hidden></textarea>
                </div>
                <div class="row">
                    <div class="col-12 input-group">
                        <select class="form-select" v-model="si.slang" @change="changeMode">
                            <option v-for="(lang,index) in codeconfig.langs" :key="index" :label="lang" :value="index">@{{ lang }}</option>
                        </select>
                        <select class="form-select" v-model="codeconfig.options.theme" @change="changeTheme">
                            <option v-for="theme in codeconfig.themes" :key="theme.value" :label="theme.value" :value="theme.value">@{{ theme.value }}</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <textarea id="code" name="code"></textarea>
                    </div>
                    <div class="modal-footer">
                        <div class="input-group">

                        <button class="btn btn-danger" type="button" @click="clear" ><i class="bi bi-arrow-clockwise"></i> 清空</button>
                        <input type="file" class="form-control" @change="upload($event)" id="insertfilepath" required>
                        <label class="input-group-text" for="insertfilepath">上传源代码文件 <i class="bi bi-file-earmark-code-fill"></i></label>
                        <button type="button" @click="submitsi($event)" :class="siclass" :disabled="sidisabled" v-html="sival"></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-5 col-12" style="padding-left:20px;padding-top:20px;">
                <x-card style="" header="状态" icon="bi bi-heart-pulse">
                    <div v-if="statuses.length===0">尚未提交！</div>
                    <div class="row" v-for="status in statuses" style="border-bottom: 1px solid rgba(0, 0, 0, 0.125);">
                        <a class="col-3 link-info link-nounderline" :href="'/status/'+status.sid" target="_blank">#@{{ status.sid }}</a>
                        <span class="col-5 badge" :class="['bg-'+adis[status.sresult].btn]">@{{ codeconfig.results[status.sresult] }}</span>
                        <span class="col-4">@{{ status.stime }}ms</span>
                    </div>
                    @if(isset($luser)&&$luser!==null)
                    <a class="btn btn-fill btn-outline-dark" :href="'/status?spid='+problem.pid+'&suid='+'{{ $luser->uid }}'">查看本人所有提交列表>></a>
                    <div id="usta" style="height: 300px;"></div>
                    <div id="asta" style="height: 300px;"></div>
                    @endif
                </x-card>
                <x-card header="统计" icon="bi bi-pie-chart">
                    <div id="sta" style="height: 300px;"></div>
                    <a class="btn btn-fill btn-outline-dark" :href="('/status?spid='+problem.pid)+(contest===null?'':'&scid='+contest.cid)">查看问题提交列表>></a>
                </x-card>
                <x-card id="contestcard" header="比赛信息" icon="bi bi-trophy-fill">
                    <ul v-if="cinfos!=null" class="list-unstyled">
                        <li v-for="(info,index) in cinfos" :key="index">
                            <p class="d-inline-block">@{{ index }}</p>
                            <p class="right" v-html="info"></p>
                        </li>
                    </ul>
                    <a v-if="contest!==null" class="btn btn-fill btn-outline-dark" :href="'/contest/'+contest.cid">前往比赛</a>
                </x-card>
                <x-card style="" header="问题信息" icon="bi bi-info-circle">
                    <ul class="list-unstyled">
                        <li v-for="(info,index) in infos" :key="index">
                            <p class="d-inline-block">@{{ index }}</p>
                            <p class="right" v-if="index!='标签'" v-html="info"></p>
                            <p class="right" v-if="index==='标签'">
                                <span v-if="info.length===0">无</span>
                                <a v-for="tid in info" class="link-nounderline link-info" :href="'/problem?tids=['+tid+']'">@{{ " "+tags[tid].tname + " "}}</a>
                            </p>
                        </li>
                    </ul>
                </x-card>
            </div>

        </div>
        </div>

    </main>
@endsection

@section('nextJS')
    <script src="{{ '/codemirror-5.65.2/lib/codemirror.js' }}"></script>

    <script src="{{ '/codemirror-5.65.2/addon/selection/active-line.js' }}"></script>
    <script src="{{ '/codemirror-5.65.2/mode/clike/clike.js' }}"></script>
    <script src="{{ '/codemirror-5.65.2/mode/javascript/javascript.js' }}"></script>
    <script src="{{ '/codemirror-5.65.2/mode/python/python.js' }}"></script>
    <script src="{{ '/codemirror-5.65.2/mode/php/php.js' }}"></script>

    <script src="{{ '/codemirror-5.65.2/addon/fold/foldcode.js' }}"></script>
    <script src="{{ '/codemirror-5.65.2/addon/fold/foldgutter.js' }}"></script>
    <script src="{{ '/codemirror-5.65.2/addon/fold/brace-fold.js' }}"></script>
    <script src="{{ '/codemirror-5.65.2/addon/fold/comment-fold.js' }}"></script>
    <script src="{{ '/codemirror-5.65.2/addon/hint/show-hint.js' }}"></script>
    <script>
        let coder=null;
        const problem=Vue.createApp({
            data(){
                return {
                    contest:null,
                    cinfos:[],
                    problem:{
                        pid:"",
                        ptitle:"",
                        pdes:"",
                        ptype: "",
                        pinfo:"",
                        poption:{
                            cases:[],
                            in:"",
                            out:"",
                            timelimit:"",
                            spacelimit:"",
                            source:"",
                        },
                        pcases:[],
                        tids:"",
                    },
                    tags:tags,
                    infos:[],
                    adis:isJSON({!! json_encode($config_status['adis'],JSON_UNESCAPED_UNICODE) !!}),
                    codeconfig:{
                        options:{
                            extraKeys: {
                            // 触发提示按键
                            Ctrl: 'autocomplete',
                            },
                            hintOptions: {
                            // 自定义提示选项
                            completeSingle: false, // 当匹配只有一项的时候是否自动补全
                            tables: {}, // 代码提示
                            },
                            theme: "dracula",
                            lineNumbers: true,
                            lineWrapping: true,
                            matchBrackets: true,
                            styleActiveLine: true,
                            foldGutter: true,
                            gutters: ["CodeMirror-linenumbers","CodeMirror-foldgutter"],
                        },
                        langs:isJSON({!! json_encode($config_status['langs'],JSON_UNESCAPED_UNICODE) !!}),
                        modes:isJSON({!! json_encode($config_status['modes'],JSON_UNESCAPED_UNICODE) !!}),
                        results:isJSON({!! json_encode($config_status['results'],JSON_UNESCAPED_UNICODE) !!}),
                        themes:[
                            {value:"dracula"},
                            {value:"material"},
                            {value:"eclipse"},
                            {value:"idea"},
                        ]
                    },

                    statuses:[],
                    siurl:"{!! config('var.si') !!}",
                    status:null,
                    submit:false,

                    si:{
                        slang:"c",
                        scode:"",
                        cid:null,
                        pid:"",
                        _token:"{{csrf_token()}}",
                    }

                };
            },
            mounted(){
                this.init();
                this.refreshStatus();
                clearInterval(this.timer);
                this.timer=setInterval(() => {
                    this.refreshStatus()
                }, 2000);
            },
            computed:{
                sival(){
                    if(this.status!==null){
                        return this.codeconfig.results[this.status.sresult];
                    }else if(this.submit===true){
                        return '<span class="spinner-grow spinner-grow-sm" ></span> 评测中...';
                    }else{
                        return "提交评测";
                    }
                },
                siclass(){
                    return ['btn','btn-outline-'+(this.status===null?'dark':this.adis[this.status.sresult].btn)];
                },
                sidisabled(){
                    if(this.submit===true)
                        return "true";
                    if(this.status!==null&&this.status.sresult==='p')
                        return "true";
                    return false;
                }
            },
            methods:{
                init(){
                    let sta = echarts.init(document.getElementById('sta'));
                    sta.showLoading();
                    coder=CodeMirror.fromTextArea(document.getElementById("code"),this.codeconfig.options);
                    this.changeTheme();
                    this.changeMode();

                    if(json.data!==null){
                        let that=this;
                        let problem=json.data.problem;
                        this.si.pid=problem.pid;
                        if('contest' in json.data){
                            let contest=json.data.contest;
                            this.contest=contest;
                            document.title=document.title.substring(0,document.title.length-2);
                            const ordenum=String.fromCharCode(contest.pids.indexOf(problem.pid)+65);
                            document.title+=contest.ctitle+'-'+ordenum;
                            this.cinfos={
                                比赛问题序号:ordenum,
                                ID:contest.cid,
                                赛制:'<i class="badge bg-dark">'+(contest.coption.rule==='a'?'ACM':'IOI')+'</i>',
                                比赛标题:contest.ctitle,
                                结束时间:contest.cend,
                                参赛人数:contest.cnum,
                                总提交数:contest.snums.sum,
                            };
                            this.si.cid=contest.cid;
                        }else{
                            $('#contestcard').hide();
                        }
                        document.title+="-"+problem.ptitle;
                        problem.pcases=isJSON(problem.pcases,true);
                        problem.poption=isJSON(problem.poption);
                        if(!('cases' in problem.poption)){
                            problem.poption.cases=[];
                        }
                        if(!('in' in problem.poption)){
                            problem.poption.in="";
                        }
                        if(!('out' in problem.poption)){
                            problem.poption.out="";
                        }
                        if(!('timelimit' in problem.poption)){
                            problem.poption.timelimit="100";
                        }
                        if(!('spacelimit' in problem.poption)){
                            problem.poption.spacelimit="1000";
                        }
                        if(!('source' in problem.poption)){
                            problem.poption.source="";
                        }
                        if(!('tip' in problem.poption)){
                            problem.poption.tip="";
                        }
                        this.problem=problem;
                        this.infos={
                            ID:this.problem.pid,
                            时间限制:this.problem.poption.timelimit+"ms",
                            内存限制:this.problem.poption.spacelimit+"KB",
                            标签:this.problem.tids,
                            创建者:'<a class="link-nounderline link-info" target="_blank" href="/user/'+this.problem.puid+'">'+this.problem.uname+'</a>',
                            提交总数:this.problem.psubmit,
                        };
                        @if (isset($utype)&&$utype==='a')
                            @if (isset($ladmin)&&$ladmin!==null)
                                that.slurl="{!! config('var.asl') !!}"+"?spid="+problem.pid+"&suid={!! $ladmin->uid !!}";
                            @else
                                that.slurl="";
                            @endif
                        @else
                            @if (isset($luser)&&$luser!==null)
                                that.slurl="{!! config('var.sl') !!}"+"?spid="+problem.pid+"&suid={!! $luser->uid !!}";
                            @else
                                that.slurl="";
                            @endif
                        @endif
                        if(that.slurl!==""){
                            getData(that.slurl,function(json){
                                that.statuses=json.data.statuses.data;
                                if(that.statuses.length>0){
                                    getData("{!! config('var.sg') !!}"+that.statuses[0].sid,function(json){
                                        if(json.data!==null){
                                            let status=json.data.status;
                                            that.si.slang=status.slang;
                                            coder.setValue(status.scode);
                                            that.changeMode();
                                        }
                                    });
                                }
                            });
                        }



                        let stadata=[];
                        let title="";
                        if('snums' in problem){
                            this.infos.提交总数=problem.snums.sum;
                            stadata=[
                                {name:"AC",value:problem.snums.a},
                                {name:"CE",value:problem.snums.c},
                                {name:"WA",value:problem.snums.w},
                                {name:"RE",value:problem.snums.r},
                                {name:"TL",value:problem.snums.t},
                                {name:"ML",value:problem.snums.m},
                                {name:"SE",value:problem.snums.s},
                            ];
                            title="比赛问题提交分布-"+problem.snums.sum;
                        }else{
                            stadata=[
                                {name:"AC",value:problem.pac},
                                {name:"CE",value:problem.pce},
                                {name:"WA",value:problem.pwa},
                                {name:"RE",value:problem.pre},
                                {name:"TL",value:problem.ptl},
                                {name:"ML",value:problem.pml},
                                {name:"SE",value:problem.pse},
                            ];
                            title="问题提交分布-"+problem.psubmit;
                        }
                        setproblemsta(sta,stadata,title);
                        sta.hideLoading();

                        let ustadata=[];
                        if('usnums' in problem){
                            let usta = echarts.init(document.getElementById('usta'));
                            usta.showLoading();
                            this.infos.本用户提交总数=problem.usnums.sum;
                            ustadata=[
                                {name:"AC",value:problem.usnums.a},
                                {name:"CE",value:problem.usnums.c},
                                {name:"WA",value:problem.usnums.w},
                                {name:"RE",value:problem.usnums.r},
                                {name:"TL",value:problem.usnums.t},
                                {name:"ML",value:problem.usnums.m},
                                {name:"SE",value:problem.usnums.s},
                            ];
                            setproblemsta(usta,ustadata,"用户提交分布-"+problem.usnums.sum);
                            usta.hideLoading();
                        }else{
                            $('#usta').hide();
                        }

                        let astadata=[];
                        if('asnums' in problem){
                            let asta = echarts.init(document.getElementById('asta'));
                            asta.showLoading();
                            this.infos.本管理员提交总数=problem.asnums.sum;
                            astadata=[
                                {name:"AC",value:problem.asnums.a},
                                {name:"CE",value:problem.asnums.c},
                                {name:"WA",value:problem.asnums.w},
                                {name:"RE",value:problem.asnums.r},
                                {name:"TL",value:problem.asnums.t},
                                {name:"ML",value:problem.asnums.m},
                                {name:"SE",value:problem.asnums.s},
                            ];
                            setproblemsta(asta,astadata,"管理员提交分布-"+problem.asnums.sum);
                            asta.hideLoading();
                        }else{
                            $('#asta').hide();
                        }
                    }
                },
                copy(event){
                    event.currentTarget.select();
                    document.execCommand('copy');
                    if(event.currentTarget.value!=="")
                        echoMsg('#msg',{status:1,message:"复制示例输入成功！"})
                },
                changeTheme(){
                    coder.setOption('theme',this.codeconfig.options.theme);
                },
                changeMode(){
                    coder.setOption('mode',this.codeconfig.modes[this.codeconfig.langs[this.si.slang]]);
                },
                submitsi(e){
                    let that=this;
                    let event=e;
                    this.si.scode=coder.getValue();
                    this.submit=true;

                    getData(this.siurl,function(json){
                        if(json.data!==null){
                            that.status=json.data.status;
                        }
                        if(json.status!==1){
                            that.submit=false;
                            that.status=null;
                            event.target.innerHTML='重新评测';
                        }
                    },"#msg",this.si);
                },
                clear(){
                    coder.setValue('');
                },
                refreshStatus(){
                    let that=this;
                    if(this.submit===true){
                    @if (isset($ladmin)&&$ladmin!==null)
                    getData("{!! config('var.asg') !!}"+that.status.sid,function(json){
                    @else
                    getData("{!! config('var.sg') !!}"+that.status.sid,function(json){
                    @endif
                        if(json.status===1){
                            that.status=json.data.status;
                            if(that.status.sresult!=='p'){
                                that.statuses.push(that.status);
                                that.submit=false;
                            }
                        }else{
                            this.submit=false;
                        }
                    },"#msg",data=null,jump=false,false);
                    }
                },
                upload(e){
                    let that=this;
                    let file=e.currentTarget.files[0];
                    const total=file.size;
                    if(total>102400){
                        echoMsg("#msg",{status:4,message:"文件大小不得大于100KB！"});
                        return;
                    }
                    try {
                        let reader = new FileReader();
                        reader.readAsText(file);
                        let loaded=0;
                        reader.onload=function () {
                            if(reader.result){
                                coder.setValue(reader.result);
                            }
                        }
                    } catch (error) {
                        echoMsg("#msg",{status:4,message:"源代码文件必须为文本文件！"});
                        return;
                    }
                }
            }

        }).mount('#problem');

        
    </script>
@endsection
