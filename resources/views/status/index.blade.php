@extends('template.master')

@section('title','状态-')

@section('nextCSS')
    <link rel="stylesheet" href="{{ "/codemirror-5.65.2/lib/codemirror.css" }}"/>
    <link rel="stylesheet" href="{{ "/codemirror-5.65.2/theme/dracula.css" }}"/>
    <link rel="stylesheet" href="{{ "/codemirror-5.65.2/theme/material.css" }}"/>
    <link rel="stylesheet" href="{{ "/codemirror-5.65.2/theme/eclipse.css" }}"/>
    <link rel="stylesheet" href="{{ "/codemirror-5.65.2/theme/idea.css" }}"/>
    <link rel="stylesheet" href="{{ '/codemirror-5.65.2/addon/fold/foldgutter.css' }}"/>
@endsection

@section('body')

<main id="status" v-if="status!==null" class="container p-3 pb-3">
    <div class="row">
        <div class="article col-12">
            <div class="input-group">
                <span v-if="status.scid!==null" class="input-group-text">@{{ String.fromCharCode(65+status.pids.indexOf(status.spid)) }}</span>
                <a class="btn btn-dark">#P @{{ status.spid }}</a>
                <a class="form-control text-truncate btn btn-outline-dark" target="_blank" :href="'/problem/'+status.spid+(status.scid!==null?'/'+status.scid:'')">@{{ status.ptitle+" "+status.pdes }}</a>
                <a v-if="status.scid!==null" class="btn btn-dark">#C @{{ status.scid }}</a>
                <a v-if="status.scid!==null" class="form-control text-truncate btn btn-outline-dark" target="_blank" :href="'/contest/'+status.scid">@{{ status.ctitle+" "+status.cdes }}</a>
            </div>
            <hr class="my-4">
            <div class="break">
                <div class="alert row d-flex align-items-center" :class="'alert-'+adis[status.sresult].btn" role="alert">
                    <div class="col-2 text-center">
                        <i class="h1" :class="adis[status.sresult].icon"></i>
                    </div>
                    <div class="col-10">
                        <h2>@{{ results[status.sresult]+"("+status.score+"分)" }}</h2>
                        <div class="row">
                            <p class="col-md-4">
                                <strong>编程语言：</strong>@{{ codeconfig.langs[status.slang] }}
                            </p>
                            <p class="col-sm-8">
                                <strong>提交时间：</strong>@{{ status.screate }} 
                            </p>
                            <p class="col-md-4">
                                <strong>运行时间：</strong>@{{ status.stime }}ms
                            </p>
                            <p class="col-md-4"><strong>运行内存：</strong>@{{ Math.ceil(status.sspace/1024) }}KB
                            </p>
                            <p class="col-md-4">
                                <strong>代码长度：</strong>@{{ status.slen }}
                            </p>
                            <p v-if="status.sinfo!==null&&(status.sresult==='c'||status.sresult==='s')"><strong>信息：</strong>@{{ status.sinfo }}</p>
                        </div>
                    </div>
                </div> 
            </div>
            <hr class="my-4">
            <div v-if="status.sinfo!==null&&(typeof status.sinfo!=='string')" class="row">
                <div class="col-12" v-for="info in status.sinfo">
                    <div class="input-group">
                        <span class="input-group-text" >#@{{ info.test_case }}</span>
                        <a :class="['form-control btn btn-'+adis[info.result].btn]">@{{ results[info.result] }}</a>
                        <span class="input-group-text" >@{{ info.score }} 分</span>
                        <span class="input-group-text" >@{{ info.time }} ms</span>
                        <span class="input-group-text" >@{{ Math.ceil(info.space/1024) }}KB</span>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="row" id="codediv">
                <div class="col-12">
                    <textarea id="code" name="code"></textarea>
                </div>
                <div class="modal-footer">
                    <div class="mb-3 col-12"><a style="width: 100%" class="btn-fill" :class="siclass" :disabled="sidisabled" v-html="sival" @click="rejudge($event)">重判本代码</a></div>
                </div>
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

<script>
    let coder=null;
    const status=Vue.createApp({
        data(){
            return {
                timer:null,
                status:{
                    sid:"",
                    ptitle:"",
                    spid:"",
                    suid:"",
                    scid:null,
                    stime:"",
                    sspace:"",
                    slen:"",
                    slang:"",
                    sresult:"a",
                    sinfo:null,
                    scode:null,
                },
                submit:false,

                resultcodes:{!! json_encode($config_status['resultcodes']) !!},
                adis:isJSON({!! json_encode($config_status['adis'],JSON_UNESCAPED_UNICODE) !!}),
                results:isJSON({!! json_encode($config_status['results'],JSON_UNESCAPED_UNICODE) !!}),
                codeconfig:{
                        options:{
                            readOnly:true,
                            theme: "dracula",
                            mode:"text/x-csrc",
                            lineNumbers: true,
                            lineWrapping: true,
                            matchBrackets: true,
                            styleActiveLine: true,
                            foldGutter: true,
                            gutters: ["CodeMirror-linenumbers","CodeMirror-foldgutter"],
                        },
                        langs:isJSON({!! json_encode($config_status['langs'],JSON_UNESCAPED_UNICODE) !!}),
                        modes:isJSON({!! json_encode($config_status['modes'],JSON_UNESCAPED_UNICODE) !!}),
                    },
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
        methods:{
            init(){
                coder=CodeMirror.fromTextArea(document.getElementById("code"),this.codeconfig.options);
                coder.setOption('theme',this.codeconfig.options.theme);
                coder.setOption('mode','text/x-csrc');
                coder.setValue('');
                if(json.data!==null){
                    this.status=json.data.status;
                    //alert(JSON.stringify(this.status));
                    document.title+=this.status.uname+(this.status.scid!==null?"-"+this.status.ctitle:"")+"-"+this.status.ptitle;
                    if('scode' in this.status&&this.status.scode!==null){
                        coder.setOption('mode',this.codeconfig.modes[this.codeconfig.langs[this.status.slang]]);
                        coder.setValue(this.status.scode);
                    }else{
                        $('#codediv').hide();
                    }
                    console.log(this.status);
                    if(!'sinfo' in this.status){
                        this.status.sinfo=null;
                    }
                }
            },
            rejudge(e){
                let that=this;
                    let event=e;
                    this.submit=true;
                    getData("{{ config('var.jur') }}"+this.status.sid,function(){
                        if(json.data!==null){
                            that.status=Object.assign({},json.data.status);
                        }
                        if(json.status!==1){
                            that.submit=false;
                            that.status=null;
                            event.target.innerHTML='重新评测';
                        }
                    },"#msg");
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
                            that.status=Object.assign({},json.data.status);
                            if(that.status.sresult!=='p'){
                                that.submit=false;
                            }
                        }else{
                            that.submit=false;
                        }
                    },"#msg",data=null,jump=false,load=false);
                    }
                },
        },
            computed:{
                sival(){
                    if(this.submit===true){
                        return '<span class="spinner-grow spinner-grow-sm" ></span> 重新评测中...';
                    }else if(this.status!==null){
                        return this.results[this.status.sresult]+"【点击可重新评测】";
                    }else{
                        return "提交评测";
                    }
                },
                siclass(){
                    if(this.submit===true){
                        return ['btn','btn-outline-primary'];
                    }
                    return ['btn','btn-outline-'+(this.status===null?'dark':this.adis[this.status.sresult].btn)];
                },
                sidisabled(){
                    if(this.submit===true)
                        return "true";
                    if(this.status!==null&&this.status.sresult==='p'){
                        return "true";
                    }
                    return false;
                }
            },

    }).mount('#status');

    
</script>

@endsection
