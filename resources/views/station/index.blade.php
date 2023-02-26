@extends('template.master')

@section('title','站点')

@section('nextCSS')

@endsection

@section('body')
    <main id="station" class="container p-3 pb-3">
        <div class="row">
            <div class="article col-xl-9 col-lg-8 col-md-7 col-12">
                <div class="break">
                    <h1>@{{ station.sname }}</h1>
                    <h4>@{{ station.sinfo.addr }}</h4>    
                </div>
                <hr class="my-4">
                <h4 class="mb-3 text-info">描述</h4>
                <div class="break" style="padding: 20px;" >无</div>
                <hr class="my-4">
            </div>

            <div class="col-xl-3 col-lg-4 col-md-5 col-12" style="padding-left:20px;padding-top:20px;">
                <x-card style="" header="站点信息" icon="bi bi-info-circle">
                    <ul class="list-unstyled">
                        <li v-for="(info,index) in infos" :key="index">
                            <p class="d-inline-block">@{{ index }}</p>
                            <p class="right" v-if="index!='标签'" v-html="info"></p>
                            <p class="right" v-if="index==='标签'">
                                <span v-if="info.length===0">无</span>
                                <a v-for="tid in info" class="link-nounderline link-info" :href="'/station?tids=['+tid+']'">@{{ " "+tags[tid].tname + " "}}</a>
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
    <script>
        let coder=null;
        const station=Vue.createApp({
            data(){
                return {
                    station:{
                        sid:"",
                        sname:"",
                        sstate:"",
                        city_id: "",
                        region_id:"",
                        sinfo:{
                            p:0,
                            r:0,
                            v:0,
                            pnum:0,
                            rnum:0,
                            vnum:0,
                            addr:"",
                            time:"",
                        },
                        stime:[],

                    },
                    infos:[],

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

                    if(json.data!==null){
                        let that=this;
                        let station=json.data.station;
                        document.title+="-"+station.sname;
                        station.sinfo=isJSON(station.sinfo);
                        this.station=station;
                        this.infos={
                            ID:this.station.sid,
                            地址:this.station.sinfo.addr,
                            时间:this.station.sinfo.time,
                            核酸检测:this.station.sinfo.p===0?"开放":"关闭",
                            核酸检测人数:this.station.sinfo.p===0?"无":(this.station.sinfo.pnum===-1?"无限制":this.station.sinfo.pnum),
                            抗原检测:this.station.sinfo.r===0?"开放":"关闭",
                            抗原检测人数:this.station.sinfo.r===0?"无":(this.station.sinfo.rnum===-1?"无限制":this.station.sinfo.rnum),
                            疫苗注射:this.station.sinfo.v===0?"开放":"关闭",
                            疫苗注射人数:this.station.sinfo.v===0?"无":(this.station.sinfo.vnum===-1?"无限制":this.station.sinfo.vnum),
                        };
                    }
                },
                clear(){
                    coder.setValue('');
                },
                refreshStatus(){
                    let that=this;
                },
            }

        }).mount('#station');

        
    </script>
@endsection
