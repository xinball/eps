@extends('template.master')

@section('title','比赛-')

@section('nextCSS')
@endsection

@section('body')

<main id="contest" class="container p-3 pb-3">
    <div class="row">
        <div class="article col-xl-9 col-lg-8 col-md-7 col-12">
            <div class="break">
                <h1><i v-if="contest.coption.pwd!==false" class="bi bi-shield-lock-fill text-pink"></i> @{{ contest.ctitle }}</h1>
                <h4><span v-if="typekey.b.includes(contest.ctype)" class="badge bg-warning" >测试</span><span v-if="typekey.u.includes(contest.ctype)" class="badge bg-secondary" >测试</span> @{{ contest.cdes }}</h4>
                <p style="text-align: left;">
                    <a v-if="typekey.a.includes(contest.ctype)&&starttime<0" aria-hidden="true" class="btn rounded-pill btn-outline-secondary">未开始</a>
                    <a v-else-if="typekey.a.includes(contest.ctype)&&endtime>0" aria-hidden="true" class="btn rounded-pill btn-outline-danger">已结束</a>
                    <a v-else-if="typekey.a.includes(contest.ctype)" class="btn rounded-pill btn-outline-success" >进行中 >> </a>
                    @if(isset($ladmin)&&$ladmin!==null)
                    <a v-if="typekey.b.includes(contest.ctype)"  class="btn rounded-pill btn-outline-success" @click="approve()">同意申请</a>
                    <a v-if="typekey.b.includes(contest.ctype)"  class="btn rounded-pill btn-outline-warning" @click="refuse()">拒绝申请</a>
                    <a v-if="typekey.a.includes(contest.ctype)"  class="btn rounded-pill btn-outline-warning" @click="refuse()">下线</a>
                    <a v-if="typekey.a.includes(contest.ctype)"  class="btn rounded-pill btn-outline-danger" @click="del()">删除</a>
                    <a v-if="typekey.d.includes(contest.ctype)"  class="btn rounded-pill btn-outline-success" @click="recover()">恢复</a>
                    @elseif(isset($luser)&&$luser!==null)
                    <a v-if="contest.self&&typekey.u.includes(contest.ctype)"  class="btn rounded-pill btn-outline-success" @click="apply()">申请</a>
                    <a v-if="contest.self&&typekey.b.includes(contest.ctype)"  class="btn rounded-pill btn-outline-warning" @click="cancel()">撤销申请</a>
                    <a v-if="contest.self&&typekey.a.includes(contest.ctype)"  class="btn rounded-pill btn-outline-warning" @click="cancel()">下线</a>
                    @endif
                </p>
            </div>
            <hr class="my-4">
            <h4 class="mb-3 text-info">描述</h4>
            <div class="break" style="padding: 20px;" v-html="contest.cinfo"></div>
            <hr class="my-4">
            <h4 class="mb-3 text-info">题目</h4>
            <div class="row g-3">
                <div class="col-md-12 input-group" style="font-size:32px;" v-for="(problem,index) in contest.problems" :key="index">
                    <span class="input-group-text" disabled>@{{ String.fromCharCode(65+index) }}</span>
                    <a class="btn btn-dark">@{{ contest.pids[index] }}</a>
                    <a class="form-control text-truncate btn btn-outline-dark" target="_blank" :href="'/problem/'+contest.pids[index]+'/'+contest.cid">@{{ problem.ptitle+" "+problem.pdes }}</a>
                    <a v-if="'solve' in problem" class="btn btn-success"><i class="bi bi-check-lg"></i> @{{ problem.status.stime }}ms</a>
                    <a v-else class="btn btn-secondary"><i class="bi bi-x-lg"></i> </a>
                </div>
            </div>
            <hr class="my-4">
            <h4 class="mb-3 text-info">实时榜单</h4>
            <div v-if="rtrank&&ranktable!==null" class="row g-3">
                <div class="table-responsive-xxl text-center">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>#uid</th>
                                <th>用户名</th>
                                <th>AC</th>
                                <th>提交总数</th>
                                <th v-if="contest.coption.rule==='a'">总时间(min)</th>
                                <th v-else>分数</th>
                                <th v-for="(pid,index) in contest.pids">@{{ String.fromCharCode(index+65)+'(#'+pid+')' }}</th>
                            </tr>
                        </thead>
                        <tbody class="align-middle">
                            <tr v-for="(rank,index) in ranktable">
                                <th scope="row">@{{ rank.uid }}</th>
                                <td v-html="getuserurl(rank)"></td>
                                <td>@{{ rank.ac }}</td>
                                <td>@{{ rank.submitbefore+'/'+rank.submit }}</td>
                                <td v-if="contest.coption.rule==='a'">@{{ rank.punish.toFixed(0) }}</td>
                                <td v-else>@{{ rank.score }}</td>
                                <td v-for="(pid,index) in contest.pids" v-html="getproblemstatus(rank,pid)" :class="getstatusclass(rank,pid)"></td>
                            </tr>
                        </tbody>
                    </table>
                        <p v-if="ranktable.length===0">暂无提交！</p>
                </div>
            </div>
            <div v-if="rtrank!==true">@{{ nortrank }}</div>
            <hr class="my-4">
            <h4 class="mb-3 text-info">比赛管理员</h4>
            <div class="input-group">
                <a class="btn btn-outline-dark" target="_blank" v-for="(auser,index) in contest.ausers" :href="'/user/'+contest.auids[index]">@{{ auser.uname }}</a>
            </div>
        </div>

        <div class="col-xl-3 col-lg-4 col-md-5 col-12" style="padding-left:20px;padding-top:20px;">
            <x-card style="" header="统计" icon="bi bi-pie-chart">
                <div id="sta" style="height: 300px;"></div>
                <a class="btn btn-fill btn-outline-dark" :href="'/status?scid='+contest.cid">查看比赛提交列表>></a>
            </x-card>
            <x-card style="" header="信息" icon="bi bi-info-circle">
                <div class="w-100" style="text-align:center;height:96px;width:96px;">
                    <img  :src="contest.img" alt="比赛图片" height="90" width="90" style="font-size: 84px;border-radius:8px;" >
                </div>
                <ul class="list-unstyled">
                    <li v-for="(info,index) in infos" :key="index">
                        <p class="d-inline-block">@{{ index }}</p>
                        <p class="right" v-html="info"></p>
                    </li>
                </ul>
            </x-card>
            
        </div>

    </div>
    </div>

</main>
@endsection

@section('nextJS')
<script>
    function compareByScore(v1,v2){
        if(v1.score<v2.score)
            return 1;
        if(v1.score>v2.score)
            return -1;
        if(v1.ac<v2.ac)
            return 1;
        if(v1.ac>v2.ac)
            return -1;
        if(v1.submitbefore<v2.submitbefore)
            return -1;
        if(v1.submitbefore>v2.submitbefore)
            return 1;
        if(v1.time<v2.time)
            return -1;
        if(v1.time>v2.time)
            return 1;
        if(v1.space<v2.space)
            return -1;
        if(v1.space>v2.space)
            return 1;
        return 0;
    }
    function compareByPunish(v1,v2){
        if(v1.ac<v2.ac)
            return 1;
        if(v1.ac>v2.ac)
            return -1;
        if(v1.punish<v2.punish)
            return -1;
        if(v1.punish>v2.punish)
            return 1;
        if(v1.submitbefore<v2.submitbefore)
            return -1;
        if(v1.submitbefore>v2.submitbefore)
            return 1;
        if(v1.time<v2.time)
            return -1;
        if(v1.time>v2.time)
            return 1;
        if(v1.space<v2.space)
            return -1;
        if(v1.space>v2.space)
            return 1;
        return 0;
    }
    const contest=Vue.createApp({
        data(){
            return {
                timer:null,
                ranktimer:null,
                statuses:[],
                ranktable:null,
                max:null,
                contest:{
                    cid:"",
                    ctitle:"",
                    cdes:"",
                    ctype: "",
                    cinfo:"",
                    coption:{
                    },
                },
                typekey:{!! json_encode($config_contest['typekey']) !!},
                infos:[],
                rtrank:true,
                nortrank:"",

            };
        },
        mounted(){
            this.init();
        },
        methods:{
            getTimeLength(){
                return getSubTime(getDate(this.contest.cstart).getTime(),getDate(this.contest.cend).getTime());
            },
            init(){
                if(json.data!==null){
                    let sta = echarts.init(document.getElementById('sta'));
                    sta.showLoading();
                    let that=this;
                    let contest=json.data.contest;
                    document.title+="-"+contest.ctitle;
                    this.starttime=((new Date()).getTime()-getDate(contest.cstart).getTime())/1000;
                    this.endtime=((new Date()).getTime()-getDate(contest.cend).getTime())/1000;
                    if(!('pwd' in contest.coption)||contest.coption.pwd===null){
                        contest.coption.pwd=false;
                    }
                    if(!('punish' in contest.coption)||contest.coption.punish===null){
                        contest.coption.punish="0";
                    }
                    if(!('rtrank' in contest.coption)){
                        contest.coption.rtrank=true;
                    }
                    if(!('numlimit' in contest.coption)||contest.coption.numlimit===null){
                        contest.coption.numlimit=0;
                    }
                    this.contest=contest;
                    this.infos={
                        ID:contest.cid,
                        赛制:'<i class="badge bg-dark">'+(contest.coption.rule==='a'?'ACM':'IOI')+'</i>',
                        开始时间:contest.cstart,
                        结束时间:contest.cend,
                        比赛时长:this.getTimeLength(),
                        参赛人数:contest.cnum,
                        题目数量:contest.pids.length,
                        提交总数:contest.snums.sum,
                        罚时:contest.coption.rule==='a'&&contest.coption.punish!==0?contest.coption.punish+'min':"无",
                        实时榜单:contest.coption.rtrank?"支持":"不支持",
                        次数限制:contest.coption.numlimit!==0?contest.coption.numlimit:"无",
                        密码:contest.coption.pwd?"有":"无",
                    };
                    let stadata=[
                        {name:"AC",value:contest.snums.a},
                        {name:"CE",value:contest.snums.c},
                        {name:"WA",value:contest.snums.w},
                        {name:"RE",value:contest.snums.r},
                        {name:"TL",value:contest.snums.t},
                        {name:"ML",value:contest.snums.m},
                        {name:"SE",value:contest.snums.s},
                    ];
                    sta.hideLoading();
                    setproblemsta(sta,stadata,"比赛提交分布-"+contest.snums.sum);

                    @if (isset($ladmin)&&$ladmin!==null)
                    this.rtrank=true;
                    @elseif (isset($luser)&&$luser!==null)
                    if(this.contest.coption.rtrank===false){
                        this.nortrank="该比赛不支持实时榜单！";
                        this.rtrank=false;
                    }else if(this.starttime<0||this.endtime>0){
                        this.nortrank="该比赛不在进行中，无法查看榜单！";
                        this.rtrank=false;
                    }
                    @else
                    this.rtrank=false;
                        this.nortrank="用户未登录，无法查看榜单！";
                    @endif

                    clearInterval(this.timer);
                    this.timer=setInterval(() => {
                        this.refreshStatus()
                    }, 1000);
                    this.refreshRank();
                    clearInterval(this.ranktimer);
                    this.ranktimer=setInterval(() => {
                        this.refreshRank()
                    }, 10000);
                    // @if (isset($utype)&&$utype==='a')
                    //     @if (isset($ladmin)&&$ladmin!==null)
                    //         that.slurl="{!! config('var.asl') !!}"+"?spid="+problem.pid+"&suid={!! $ladmin->uid !!}";
                    //     @else
                    //         that.slurl="";
                    //     @endif
                    // @else
                    //     @if (isset($luser)&&$luser!==null)
                    //         that.slurl="{!! config('var.sl') !!}"+"?spid="+problem.pid+"&suid={!! $luser->uid !!}";
                    //     @else
                    //         that.slurl="";
                    //     @endif
                    // @endif
                    // if(that.slurl!==""){
                    //     getData(that.slurl,function(json){
                    //         that.statuses=json.data.statuses.data;
                    //         if(that.statuses.length>0){
                    //             getData("{!! config('var.sg') !!}"+that.statuses[0].sid,function(json){
                    //                 if(json.data!==null){
                    //                     let status=json.data.status;
                    //                     that.si.slang=status.slang;
                    //                     coder.setValue(status.scode);
                    //                     this.changeMode();
                    //                 }
                    //             });
                    //         }
                    //     });
                    // }



                    //
                }
            },
            getuserurl(rank){
                return '<a class="text-info" href="/user/'+rank.uid+'">'+rank.uname+'</a>';
            },
            getstatusclass(rank,pid){
                let statusclass='';
                if(pid in rank){
                    if(rank[pid].result!=='a'){
                        statusclass='bg-danger';
                    }else{
                        if(this.max[pid].uid===rank.uid)
                            statusclass='bg-success';
                        else
                            statusclass='bg-ac';
                    }
                }
                return statusclass;
            },
            getproblemstatus(rank,pid){
                let html="";
                if(pid in rank){
                    // if(rank[pid].result!=='a'){
                    //     html+='';
                    // }else{
                    //     html+='';
                    // }
                    if(this.contest.coption.rule==='a'){
                        html+=rank[pid].punish.toFixed(0)+'<br>'+rank[pid].submitbefore+'/'+rank[pid].submit+'<br>'+rank[pid].screate;
                    }else{
                        html+=rank[pid].score+'<br>'+rank[pid].submitbefore+'/'+rank[pid].submit+'<br>'+rank[pid].screate;
                    }
                }
                return html;
            },
            refreshStatus(){
                this.starttime++;
                this.endtime++;
            },
            refreshRank(){
                let that=this;
                let ranktable=[];
                if(this.rtrank===true){
                    @if (isset($ladmin)&&$ladmin!==null)
                    getData('{{ config('var.asl') }}'+'?order=screate&stype=u&scid='+this.contest.cid,function(json){
                    @else
                    getData('{{ config('var.sl') }}'+'?order=screate&scid='+this.contest.cid,function(json){
                    @endif
                    if(json.data===null){
                        return;
                    }
                        let statuses=json.data.statuses.data;
                        let j=0;
                        let user={};
                        let max={};
                        for(pid of that.contest.pids){
                            max[pid]={
                                uid:0,
                                score:-1,
                                punish:5*365*24*60,
                            };
                        }
                        for(i in statuses){
                            const status=statuses[i];
                            if(status.scid===that.contest.cid&&status.stype==='u'&&that.contest.pids.includes(status.spid)){
                                console.log(status);
                                if(user[status.suid] in ranktable){
                                    ranktable[user[status.suid]].submit++;
                                    if(status.sresult==='a'){
                                        ranktable[user[status.suid]].acall++;
                                    }else{

                                    }
                                }else{
                                    user[status.suid]=j;
                                    ranktable[j]={
                                        uid:status.suid,
                                        uname:status.uname,
                                        score:0,
                                        submit:1,
                                        submitbefore:1,
                                        acall:(status.sresult==='a'?1:0),
                                        ac:0,
                                        punish:0,
                                        time:0,
                                        space:0,
                                        len:0,
                                    };
                                    j++;

                                }
                                let rank=ranktable[user[status.suid]];
                                if(status.spid in rank){
                                    rank[status.spid].submit++;
                                    if(rank[status.spid].acall===0){
                                        rank[status.spid].submitbefore++;
                                        rank.submitbefore++;
                                        rank.time+=(status.stime-rank[status.spid].time);
                                        rank.space+=(status.sspace-rank[status.spid].space);
                                        rank.len+=(status.slen-rank[status.spid].len);
                                        rank[status.spid].time=status.stime;
                                        rank[status.spid].space=status.sspace;
                                        rank[status.spid].len=status.slen;
                                        rank[status.spid].screate=status.screate;
                                        if(status.sresult==='a'){
                                            rank.ac++;
                                            rank[status.spid].result='a';
                                            rank[status.spid].acall++;
                                        }

                                        if(that.contest.coption.rule==='a'){
                                            if(status.sresult==='a'){
                                                const punish=(getDate(status.screate).getTime()-getDate(that.contest.cstart).getTime())/(1000*60);
                                                rank[status.spid].punish+=punish;
                                                rank.punish+=punish;
                                                if(rank.punish<max[status.spid].punish){
                                                    max[status.spid]={
                                                        uid:status.suid,
                                                        score:0,
                                                        punish:rank[status.spid].punish,
                                                    }
                                                }
                                            }else{
                                                rank.punish+=that.contest.coption.punish;
                                                rank[status.spid].punish+=that.contest.coption.punish;
                                            }
                                        }else if(status.score>rank[status.spid].score){
                                            rank.score+=(status.score-rank[status.spid].score);
                                            rank[status.spid].score=status.score;
                                            if(status.score>max[status.spid].score){
                                                max[status.spid]={
                                                    uid:status.suid,
                                                    score:status.score,
                                                    punish:0,
                                                }
                                            }
                                        }
                                    }
                                }else{
                                    rank[status.spid]={
                                        submit:1,
                                        submitbefore:1,
                                        score:status.score,
                                        acall:(status.sresult==='a'?1:0),
                                        len:status.slen,
                                        result:status.sresult,
                                        time:status.stime,
                                        space:status.sspace,
                                        screate:status.screate,
                                        punish:0,
                                    };
                                    if(status.sresult==='a'){
                                        rank.ac++;
                                    }
                                    if(that.contest.coption.rule==='a'){
                                        if(status.sresult!=='a'){
                                            rank.punish+=that.contest.coption.punish;
                                            rank[status.spid].punish+=that.contest.coption.punish;
                                        }else{
                                            const punish=(getDate(status.screate).getTime()-getDate(that.contest.cstart).getTime())/(1000*60);
                                            rank[status.spid].punish+=punish;
                                            rank.punish+=punish;
                                            if(rank.punish<max[status.spid].punish){
                                                max[status.spid]={
                                                    uid:status.suid,
                                                    score:0,
                                                    punish:rank[status.spid].punish,
                                                }
                                            }
                                        }
                                    }else{
                                        rank.score+=rank[status.spid].score;
                                        if(status.score>max[status.spid].score){
                                            max[status.spid]={
                                                uid:status.suid,
                                                score:status.score,
                                                punish:0,
                                            }
                                        }
                                    }
                                    rank.time+=status.stime;
                                    rank.space+=status.sspace;
                                    rank.len+=status.slen;
                                }
                                    // console.log(rank)
                            }
                        }
                        if(that.contest.coption.rule=='a'){
                            ranktable.sort(compareByPunish);
                        }else{
                            ranktable.sort(compareByScore);
                        }
                        console.log(ranktable)
                        that.ranktable=ranktable;
                        that.max=max;
                    });
                }
            },
            approve(){
                let that=this;
                getData('{!! config('var.acar') !!}'+this.contest.cid,function(json){
                    if(json.status===1){
                        that.contest.ctype=that.typekey.o.includes(that.contest.ctype)?"o":"s";
                    }
                },"#msg");
            },
            refuse(index){
                let that=this;
                getData('{!! config('var.acrf') !!}'+this.contest.cid,function(json){
                    if(json.status===1){
                        that.contest.ctype=that.typekey.o.includes(that.contest.ctype)?"c":"e";
                    }
                },"#msg");
            },
            del(index){
                let that=this;
                getData('{!! config('var.acd') !!}'+this.contest.cid,function(json){
                    if(json.status===1){
                        that.contest.ctype="d";
                    }
                },"#msg");
            },
            recover(index){
                let that=this;
                getData('{!! config('var.acr') !!}'+this.contest.cid,function(json){
                    if(json.status===1){
                        that.contest.ctype="c";
                    }
                },"#msg");
            },
            apply(index){
                let that=this;
                getData('{!! config('var.cal') !!}'+this.contest.cid,function(json){
                    if(json.status===1){
                        that.contest.ctype=that.typekey.o.includes(that.contest.ctype)?"a":"b";
                    }
                },"#msg");
            },
            cancel(index){
                let that=this;
                getData('{!! config('var.ccl') !!}'+this.contest.cid,function(json){
                    if(json.status===1){
                        that.contest.ctype=that.typekey.o.includes(that.contest.ctype)?"c":"e";
                    }
                },"#msg");
            },
        }

    }).mount('#contest');

    
</script>

@endsection

@section('nextJS')
@endsection
