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
                    <h5><a :href="'/station?state_id='+station.state_id+'&city_id='+station.city_id+'&region_id='+station.region_id" class="btn text-dark btn-outline-primary badge">@{{ state+" "+city+" "+region }}</a> <span class="badge bg-dark">@{{station.slat+"°N "+station.slng+"°E"}}</span></h5>
                    <p>@{{ station.sinfo.addr }}</p>
                </div>
                <hr class="my-4">
                <h4 class="mb-3 text-info">描述</h4>
                <div class="break" style="padding: 20px;"  v-html='station.sinfo.des===""?"无":station.sinfo.des'></div>
                <hr class="my-4">
            </div>

            <div class="col-xl-3 col-lg-4 col-md-5 col-12" style="padding-left:20px;padding-top:20px;">

                <!--x-card继承了card-->
                <x-card style="" header="站点信息" icon="bi bi-info-circle">
                    <div class="w-100" style="text-align:center;height:96px;width:96px;">
                        <img  :src="station.img" alt="站点图片" height="90" width="90" style="font-size: 84px;border-radius:8px;" >
                    </div>
                    <ul class="list-unstyled">
                        <li v-for="(info,index) in infos" :key="index">
                            <p v-if="info!==null" class="d-inline-block">@{{ index }}</p>
                            <p v-if="info!==null" class="right" v-html="info"></p>
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
        const stationapp=Vue.createApp({
            data(){
                return {
                    station:{
                        sid:"",
                        sname:"",
                        sstate:"",
                        city_id: "",
                        region_id:"",
                        sinfo:{
                            p:false,
                            a:false,
                            r:false,
                            v:false,
                            rnum:0,
                            pnum:0,
                            anum:0,
                            vnum:0,
                            addr:"",
                            time:"",
                        },
                        stime:[],

                    },
                    infos:[],
                    state:"",
                    city:"",
                    region:"",
                    

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
                        let station=json.data.station;
                        document.title+="-"+station.sname;
                        station.sinfo=isJSON(station.sinfo);
                        this.station=station;
                        this.infos={
                            ID:this.station.sid,
                            地址:this.station.sinfo.addr??"无",
                            时间:this.station.sinfo.time??"无",
                            报备:this.station.sinfo.r?"<span style='color:green;'>开放</span>":"<span style='color:red;'>关闭</span>",
                            报备人数:this.station.sinfo.r?(this.station.sinfo.rnum===-1?"无限制":this.station.sinfo.rnum):null,
                            核酸检测:this.station.sinfo.p?"<span style='color:green;'>开放</span>":"<span style='color:red;'>关闭</span>",
                            核酸检测人数:this.station.sinfo.p?(this.station.sinfo.pnum===-1?"无限制":this.station.sinfo.pnum):null,
                            抗原检测:this.station.sinfo.a?"<span style='color:green;'>开放":"<span style='color:red;'>关闭</span>",
                            抗原检测人数:this.station.sinfo.a?(this.station.sinfo.anum===-1?"无限制":this.station.sinfo.anum):null,
                            疫苗注射:this.station.sinfo.v?"<span style='color:green;'>开放</span>":"<span style='color:red;'>关闭</span>",
                            疫苗注射人数:this.station.sinfo.v?(this.station.sinfo.vnum===-1?"无限制":this.station.sinfo.vnum):null,
                        };
                        let that=this;
                        if(that.station.region_id!==null){
                            getData("{!! config('var.sga') !!}"+"?type=r&id="+that.station.region_id,function(json){
                                that.region=json.rcname;
                                that.city=json.ccname;
                                that.state=json.scname;
                            });
                        }else{
                            getData("{!! config('var.sga') !!}"+"?type=c&id="+that.station.city_id,function(json){
                                that.city=json.ccname;
                                that.state=json.scname;
                            });
                        }
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
