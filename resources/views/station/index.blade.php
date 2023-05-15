@extends('template.master')

@section('title','站点')

@section('nextCSS')

@endsection

@section('body')
@include('station.alter')
@include('appoint.insert')
    <main id="station" class="container p-3 pb-3">
        <div class="row">
            <div class="article col-xl-9 col-lg-8 col-md-7 col-12">
                <div class="break">
                    <h1>@{{ station.sname }}</h1>
                    <h5><a target="_blank" title="筛选该行政区" :href="'/station?state_id='+station.state_id+'&city_id='+station.city_id+'&region_id='+station.region_id" class="btn text-dark btn-outline-primary badge" v-text="state!==''?state+' '+city+' '+region:'无'"></a> <span class="badge bg-dark">@{{station.slat+"°N "+station.slng+"°E"}}</span></h5>
                    <p>@{{ station.sinfo.addr }}</p>
                </div>
                <hr class="my-4">
                <h4 class="mb-3 text-info"><i class="bi bi-tools"></i> 操作</h4>
                <div style="text-align: left;">
@if(isset($ladmin)&&$ladmin!==null)
                    <a v-if="station.sstate==='c'" aria-hidden="true" class="btn rounded-pill btn-secondary"><i class="bi bi-building-slash"></i> 已关闭</a>
                    <a v-else-if="station.sstate==='o'" class="btn rounded-pill btn-success"><i class="bi bi-building"></i> 开放中</a>
                    <a v-if="station.editable===true&&station.sstate!=='d'"  class="btn rounded-pill btn-outline-success" @click="openalter()" ><i class="bi bi-building-gear"></i> 编辑</a>
                    <a v-if="station.editable===true&&station.sstate!=='d'"  class="btn rounded-pill btn-outline-danger" @click="del()"><i class="bi bi-building-x"></i> 删除</a>
                    <a v-if="station.editable===true&&station.sstate==='d'"  class="btn rounded-pill btn-outline-success" @click="recover()"><i class="bi bi-building-check"></i> 恢复</a>  
@elseif(isset($luser)&&$luser!==null)
                    <a v-if="station.sstate==='o'" :data-bs-atype="atype" :data-bs-atime="atime" :data-bs-sid="station.sid" data-bs-target="#insert" data-bs-toggle="modal" class="btn rounded-pill btn-outline-success"><i class="bi bi-clipboard-plus"></i> 预约</a>
@else
                    请登录后进行操作！
@endif
                </div>
                <hr class="my-4">
                <h4 class="mb-3 text-info"><i class="bi bi-body-text"></i> 描述</h4>
                <div class="break" style="padding: 20px;"  v-html='station.sinfo.des===""?"无":station.sinfo.des'></div>
                <hr class="my-4">
                <h4 class="mb-3 text-info"><i class="bi bi-calendar2-range-fill"></i> 最近预约</h4>
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="form-floating">
                            <select class="form-select" v-model="atype" id="atype">
                                <option v-for="(item,index) in services" :key="index" :label="item.label" :value="index">@{{ item.label }}</option>
                            </select>
                            <label for="atype">服务</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="datetime-local" class="form-control" id="atime" v-model="atime" placeholder="站点名称" required>
                            <label for="atime" class="form-label">预约时间</label>
                        </div>
                    </div>
                </div>
                <hr class="my-4">
                <h4 class="mb-3 text-info"><i class="bi bi-calendar2-week-fill"></i> 开放时间</h4>
                <div class="row g-3">
                    <div class="table-responsive-xxl text-center">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th width="10%">星期</th>
                                    <th>开放时间</th>
                                </tr>
                            </thead>
                            <tbody class="align-middle">
                                <tr v-for="(sday,i) in station.stime">
                                    <th scope="row" v-html="getDateType(i)+'<br/>'+sday.length+'个配置'"></th>
                                    <td>
                                    <div class="progress-stacked" v-for="(item,j) in sday">
                                        <div class="progress" role="progressbar" :style="{width: getWidth('00:00',item.start)}">
                                            <div class="progress-bar bg-light"></div>
                                        </div>
                                        <div class="progress" role="progressbar" :style="{width: getWidth(item.start,item.end)}">
                                            <div class="progress-bar bg-success" v-text="item.start+'~'+item.end"></div>
                                        </div>
                                        <div class="progress" role="progressbar" :style="{width: getWidth(item.end,'24:00')}">
                                            <div class="progress-bar bg-light"></div>
                                        </div>
                                    </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
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
                <x-card style="" header="站点管理员" icon="bi bi-person-lines-fill">
                    <div class="row g-3">
                        <div class="col-md-12 input-group" v-for="(admin,index) in station.sadmin" :key="index">
                            <button type="button" class="badge bg-dark" disabled>@{{ index+1 }}</button>
                            <a class="form-control text-truncate btn btn-outline-secondary" target="_blank" title="进入该用户主页" :href="'/user/'+admin.uid">@{{ "#"+admin.uid+" "+admin.uname }}</a>
                            <a class="form-control text-truncate btn btn-outline-secondary" target="_blank" title="发邮件给该用户" :href="'mailto:'+admin.uemail">@{{ admin.uemail }}</a>
                        </div>
                    </div>
                </x-card>
                <x-card style="" header="区域管理员" icon="bi bi-person-workspace">
                    <div class="row g-3">
                        <div class="col-md-12 input-group" v-for="(admin,index) in station.aadmin" :key="index">
                            <button type="button" class="badge bg-dark" disabled>@{{ index+1 }}</button>
                            <a class="form-control text-truncate btn btn-outline-secondary" target="_blank" title="进入该用户主页" :href="'/user/'+admin.uid">@{{ "#"+admin.uid+" "+admin.uname }}</a>
                            <a class="form-control text-truncate btn btn-outline-secondary" target="_blank" title="发邮件给该用户" :href="'mailto:'+admin.uemail">@{{ admin.uemail }}</a>
                        </div>
                    </div>
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
                    services:{!! json_encode($config_station['type'],JSON_UNESCAPED_UNICODE) !!},

                    atype:"p",
                    atime:toDate(new Date().getTime()+86400000)+' 09:00',

                };
            },
            mounted(){
                if(json.data!==null){
                    let station=json.data.station;
                    document.title+="-"+station.sname;
                    this.station=station;
                    this.init();
                }
                this.refreshStatus();
                clearInterval(this.timer);
                this.timer=setInterval(() => {
                    this.refreshStatus()
                }, 2000);
            },
            methods:{
                init(){
                    this.infos={
                            ID:this.station.sid,
                            地址:this.station.sinfo.addr??"无",
                            时间:this.station.sinfo.time??"无",
                            预约确认时间限制:this.station.sinfo.approvetime==='1'?"限制":"不限制",
                            报备:this.station.sinfo.r==='1'?"<span style='color:green;'>开放</span>":"<span style='color:red;'>关闭</span>",
                            报备人数:this.station.sinfo.r==='1'?(this.station.sinfo.rnum===-1?"无限制":this.station.sinfo.rnum):null,
                            核酸检测:this.station.sinfo.p==='1'?"<span style='color:green;'>开放</span>":"<span style='color:red;'>关闭</span>",
                            核酸检测人数:this.station.sinfo.p==='1'?(this.station.sinfo.pnum===-1?"无限制":this.station.sinfo.pnum):null,
                            抗原检测:this.station.sinfo.a==='1'?"<span style='color:green;'>开放":"<span style='color:red;'>关闭</span>",
                            抗原检测人数:this.station.sinfo.a==='1'?(this.station.sinfo.anum===-1?"无限制":this.station.sinfo.anum):null,
                            疫苗注射:this.station.sinfo.v==='1'?"<span style='color:green;'>开放</span>":"<span style='color:red;'>关闭</span>",
                            疫苗注射人数:this.station.sinfo.v==='1'?(this.station.sinfo.vnum===-1?"无限制":this.station.sinfo.vnum):null,
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
                },
                refreshStatus(){
                    let that=this;
                },
                openalter(){
                    alterapp.sid=this.station.sid;
                    $('#alter').modal("show");
                },
                //删除站点
                del(index){
                    let that=this;
                    getData('{!! config('var.asd') !!}'+this.station.sid,function(json){
                        if(json.status===1){
                            that.station.sstate="d";
                        }
                    },"#msg");
                },
                getDateType(i){
                    return DAYTYPE[i];
                },
                getWidth(starttime,endtime){
                    const end=getDayTime(endtime);
                    const start=getDayTime(starttime);
                    if(end>start)
                        return(end-start)/864000+'%';
                    else
                        return 0;
                },

                //恢复站点
                recover(index){
                    let that=this;
                    getData('{!! config('var.asr') !!}'+this.station.sid,function(json){
                        if(json.status===1){
                            that.station.sstate="c";
                        }
                    },"#msg");
                },
            }

        }).mount('#station');

        
    </script>
@endsection
