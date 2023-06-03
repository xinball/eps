@extends('admin.master')

@section('title','网站配置')
@section('nextCSS2')

@endsection

@section('main')
<div id="alterapp" class="col-sm-12 col-lg-10 col-xxl-8 align-self-auto justify-content-center" style="margin: auto;">
    <form class="needs-validation" novalidate>
        <h1 class="mb-3">基本</h1>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="name" class="form-label">防疫系统名称</label>
                <input type="text" class="form-control" id="name" v-model="basic.name" placeholder="EPS" required>
            </div>
            <div class="col-md-6">
                <label for="status" class="form-label">网站状态</label>
                <select class="form-select" v-model="basic.status" id="status" required>
                    <option value="1">开启</option>
                    <option value="0">关闭</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="iplimit" class="form-label">每IP注册用户上限</label>
                <input type="number" class="form-control" id="iplimit" v-model="basic.iplimit" placeholder="3" required>
            </div>
            <div class="col-md-6">
                <label for="opttl" class="form-label">普通用户处理请求间隔时间</label>
                <input type="number" class="form-control" id="opttl" v-model="basic.opttl" placeholder="3" required>
            </div>
            <div class="col-md-6">
                <label for="register" class="form-label">新用户注册</label>
                <select class="form-select" v-model="basic.register" id="register" required>
                    <option value="1">允许注册</option>
                    <option value="0">关闭注册</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="useravatar" class="form-label">用户头像路径</label>
                <input type="text" class="form-control" id="useravatar" v-model="basic.useravatar" placeholder="/img/avatar/" required>
            </div>
            <div class="col-md-6">
                <label for="useravatar" class="form-label">用户背景横幅路径</label>
                <input type="text" class="form-control" id="userbanner" v-model="basic.userbanner" placeholder="/img/banner/" required>
            </div>
            <div class="col-md-6">
                <label for="useravatar" class="form-label">站点图片路径</label>
                <input type="text" class="form-control" id="stationavatar" v-model="basic.stationavatar" placeholder="/img/station/" required>
            </div>
            <div class="col-md-6">
                <label for="defaultavatar" class="form-label">用户默认头像</label>
                <input type="text" class="form-control" id="defaultavatar" v-model="basic.defaultavatar" placeholder="/bootstrap/icon/person-circle.svg" required>
            </div>
            <div class="col-md-6">
                <label for="defaultbanner" class="form-label">用户默认背景横幅</label>
                <input type="text" class="form-control" id="defaultbanner" v-model="basic.defaultbanner" placeholder="/img/banner/redchina.png" required>
            </div>
            <div class="col-md-6">
                <label for="avatarwidth" class="form-label">用户头像宽度</label>
                <input type="number" class="form-control" id="avatarwidth" v-model="basic.avatarwidth" placeholder="64" required>
            </div>
            <div class="col-md-6">
                <label for="bannerwidth" class="form-label">用户背景横幅宽度</label>
                <input type="number" class="form-control" id="bannerwidth" v-model="basic.bannerwidth" placeholder="400" required>
            </div>
            <div class="col-md-6">
                <label for="stationwidth" class="form-label">站点图片宽度</label>
                <input type="number" class="form-control" id="stationwidth" v-model="basic.stationwidth" placeholder="64" required>
            </div>
            <div class="col-12">
                <label for="basiccopyright" class="form-label">底部信息</label>
                <textarea class="form-control" id="basiccopyright" rows="5" style="resize:none;"  v-model="basic.copyright" required></textarea>
            </div>
        </div><br/>
        <h5 class="mb-3">封禁IP</h5>
        <div class="row g-3">
            <div class="col-12">
                <div class="input-group justify-content-center">
                    <a @click="addban" class="btn btn-outline-info"><i class="bi bi-plus-lg"></i> 添加</a>
                    <a @click="clear" class="btn btn-outline-danger"><i class="bi bi-arrow-clockwise"></i> 清空</a>
                    <a @click="alterban" class="btn btn-outline-success"><i class="bi bi-check-lg"></i> 保存</a>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4" v-for="(ban,index) in basic.bans">
                <div class="input-group">
                    <label class="input-group-text" :for="'ban'+index">@{{ index+1 }}</label>
                    <input :id="'ban'+index" class="form-control" v-model="basic.bans[index]" placeholder="0.0.0.0"/>
                    <a class="btn btn-outline-danger" @click="delban(index)"><i class="bi bi-x-lg"></i> 删除</a>
                </div>
            </div>
        </div>
        <hr class="my-4">
        <button class="w-100 btn btn-outline-success btn-lg" type="button" @click="alter('basic')">修改基本配置</button>
        <hr class="my-4">
        <h1 class="mb-3">用户</h1>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="userloginttl" class="form-label">用户登录生存时间</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="userloginttl" v-model="user.userloginttl" placeholder="600" required>
                    <span class="input-group-text">秒</span>
                </div>
            </div>
            <div class="col-md-6">
                <label for="userttl" class="form-label">用户保持生存时间 阈值</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="userttl" v-model="user.userttl" placeholder="300" required>
                    <span class="input-group-text">秒</span>
                </div>
            </div>
            <div class="col-md-6">
                <label for="adminloginttl" class="form-label">管理员登录生存时间</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="adminloginttl" v-model="user.adminloginttl" placeholder="12000" required>
                    <span class="input-group-text">秒</span>
                </div>
            </div>
            <div class="col-md-6">
                <label for="adminttl" class="form-label">管理员保持生存时间 阈值</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="adminttl" v-model="user.adminttl" placeholder="6000" required>
                    <span class="input-group-text">秒</span>
                </div>
            </div>
            <div class="col-md-6">
                <label for="activettl" class="form-label">激活用户生存时间</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="activettl" v-model="user.activettl" placeholder="2" required>
                    <span class="input-group-text">天</span>
                </div>
            </div>
            <div class="col-md-6">
                <label for="forgetttl" class="form-label">忘记密码生存时间</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="forgetttl" v-model="user.forgetttl" placeholder="2" required>
                    <span class="input-group-text">天</span>
                </div>
            </div>
            <div class="col-md-6">
                <label for="userlistnum" class="form-label">管理每页数量</label>
                <input type="number" class="form-control" id="userlistnum" v-model="user.listnum" placeholder="20" required>
            </div>
            <div class="col-md-6">
                <label for="userpagenum" class="form-label">管理显示页面数量/2</label>
                <input type="number" class="form-control" id="userpagenum" v-model="user.pagenum" placeholder="3" required>
            </div>
        </div>
        <hr class="my-4">
        <button class="w-100 btn btn-outline-success btn-lg" type="button" @click="alter('user')">修改用户配置</button>
        <hr class="my-4">
        <h1 class="mb-3">通知</h1>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="noticelistnum" class="form-label">每页数量</label>
                <input type="number" class="form-control" id="noticelistnum" v-model="notice.listnum" placeholder="20" required>
            </div>
            <div class="col-md-6">
                <label for="noticepagenum" class="form-label">显示页面数量/2</label>
                <input type="number" class="form-control" id="noticepagenum" v-model="notice.pagenum" placeholder="3" required>
            </div>
            <div class="col-md-6">
                <label for="noticetype" class="form-label">类型</label>
                <textarea class="form-control" id="noticetype" rows="5" style="resize:none;"  v-model="notice.type" required></textarea>
            </div>
            <div class="col-md-6">
                <label for="noticetypekey" class="form-label">类型分类</label>
                <textarea class="form-control" id="noticetypekey" rows="5" style="resize:none;"  v-model="notice.typekey" required></textarea>
            </div>
            <div class="col-12">
                <label for="noticeadis" class="form-label">管理 类型显示效果</label>
                <textarea class="form-control" id="noticeadis" rows="5" style="resize:none;"  v-model="notice.adis" required></textarea>
            </div>
        </div>
        <hr class="my-4">
        <button class="w-100 btn btn-outline-success btn-lg" type="button" @click="alter('notice')">修改通知配置</button>
        <hr class="my-4">
        <h1 class="mb-3">站点</h1>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="stationlistnum" class="form-label">每页数量</label>
                <input type="number" class="form-control" id="stationlistnum" v-model="station.listnum" placeholder="20" required>
            </div>
            <div class="col-md-6">
                <label for="stationpagenum" class="form-label">显示页面数量/2</label>
                <input type="number" class="form-control" id="stationpagenum" v-model="station.pagenum" placeholder="3" required>
            </div>
            <div class="col-md-6">
                <label for="stationtype" class="form-label">类型</label>
                <textarea class="form-control" id="stationtype" rows="5" style="resize:none;"  v-model="station.type" required></textarea>
            </div>
            <div class="col-md-6">
                <label for="stationtypekey" class="form-label">类型分类</label>
                <textarea class="form-control" id="stationtypekey" rows="5" style="resize:none;"  v-model="station.typekey" required></textarea>
            </div>
            <div class="col-md-6">
                <label for="stationstate" class="form-label">状态</label>
                <textarea class="form-control" id="stationstate" rows="5" style="resize:none;"  v-model="station.state" required></textarea>
            </div>
            <div class="col-md-6">
                <label for="stationstimeconfigs" class="form-label">开放时间</label>
                <textarea class="form-control" id="stationstimeconfigs" rows="5" style="resize:none;"  v-model="station.stimeconfigs" required></textarea>
            </div>
        </div>
        <hr class="my-4">
        <button class="w-100 btn btn-outline-success btn-lg" type="button" @click="alter('station')">修改站点配置</button>
        <hr class="my-4">
        <h1 class="mb-3">预约/报备</h1>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="appointlistnum" class="form-label">每页数量</label>
                <input type="number" class="form-control" id="appointlistnum" v-model="appoint.listnum" placeholder="20" required>
            </div>
            <div class="col-md-6">
                <label for="appointpagenum" class="form-label">显示页面数量/2</label>
                <input type="number" class="form-control" id="appointpagenum" v-model="appoint.pagenum" placeholder="3" required>
            </div>
            <div class="col-md-6">
                <label for="appointprocesstype" class="form-label">处理类型</label>
                <textarea class="form-control" id="appointprocesstype" rows="5" style="resize:none;"  v-model="appoint.processtype" required></textarea>
            </div>
            <div class="col-md-6">
                <label for="appointstate" class="form-label">状态</label>
                <textarea class="form-control" id="appointstate" rows="5" style="resize:none;"  v-model="appoint.state" required></textarea>
            </div>
            <div class="col-md-6">
                <label for="appointstatekey" class="form-label">状态分类</label>
                <textarea class="form-control" id="appointstatekey" rows="5" style="resize:none;"  v-model="appoint.statekey" required></textarea>
            </div>
            <div class="col-md-6">
                <label for="appointapplim" class="form-label">每条预约/报备处理数量限制</label>
                <input type="number" class="form-control" id="appointapplim" v-model="appoint.applim" placeholder="3" required>
            </div>
            <div class="col-md-6">
                <label for="appointtodaylim" class="form-label">每日每条预约/报备处理数量限制</label>
                <input type="number" class="form-control" id="appointtodaylim" v-model="appoint.todaylim" placeholder="3" required>
            </div>
            <div class="col-md-6">
                <label for="appointtotallim" class="form-label">每日预约/报备处理数量限制</label>
                <input type="number" class="form-control" id="appointtotallim" v-model="appoint.totallim" placeholder="3" required>
            </div>
        </div>
        <hr class="my-4">
        <button class="w-100 btn btn-outline-success btn-lg" type="button" @click="alter('appoint')">修改预约/报备配置</button>
        <hr class="my-4">
        <h1 class="mb-3">操作</h1>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="operationlistnum" class="form-label">每页数量</label>
                <input type="number" class="form-control" id="operationlistnum" v-model="operation.listnum" placeholder="20" required>
            </div>
            <div class="col-md-6">
                <label for="operationpagenum" class="form-label">显示页面数量/2</label>
                <input type="number" class="form-control" id="operationpagenum" v-model="operation.pagenum" placeholder="3" required>
            </div>
            <div class="col-md-6">
                <label for="operationtype" class="form-label">类型</label>
                <textarea class="form-control" id="operationtype" rows="5" style="resize:none;"  v-model="operation.type" required></textarea>
            </div>
            <div class="col-md-6">
                <label for="operationstatus" class="form-label">结果状态</label>
                <textarea class="form-control" id="operationstatus" rows="5" style="resize:none;"  v-model="operation.status" required></textarea>
            </div>
        </div>
        <hr class="my-4">
        <button class="w-100 btn btn-outline-success btn-lg" type="button" @click="alter('operation')">修改操作配置</button>
        <hr class="my-4">
        <h1 class="mb-3">其他</h1>
        <div class="row g-3">
            <div class="col-md-12">
                <a class="btn btn-outline-dark" target="_blank" href="{{config('var.vhorizon')}}">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 30px;height: 35px;" class="logo"><path d="M5.26176342 26.4094389C2.04147988 23.6582233 0 19.5675182 0 15c0-4.1421356 1.67893219-7.89213562 4.39339828-10.60660172C7.10786438 1.67893219 10.8578644 0 15 0c8.2842712 0 15 6.71572875 15 15 0 8.2842712-6.7157288 15-15 15-3.716753 0-7.11777662-1.3517984-9.73823658-3.5905611zM4.03811305 15.9222506C5.70084247 14.4569342 6.87195416 12.5 10 12.5c5 0 5 5 10 5 3.1280454 0 4.2991572-1.9569336 5.961887-3.4222502C25.4934253 8.43417206 20.7645408 4 15 4 8.92486775 4 4 8.92486775 4 15c0 .3105915.01287248.6181765.03811305.9222506z" class="fill-primary"></path></svg>
                    Horizon 任务队列
                </a>
                <a class="btn btn-outline-dark" target="_blank" href="{{config('var.vredis')}}" style="padding: 12px;">
                    <img src="https://yono.top/redis/images/favicon.png">
                    Redis 管理
                </a>
                <a class="btn btn-outline-dark" target="_blank" href="{{config('var.vbt')}}" style="padding: 8px;">
                    <img src="https://yono.top:8888/static/favicon.ico">
                    宝塔 管理
                </a>

            </div>
        </div>
        <hr class="my-4">
    </form>
</div>

<script>

const alterapp=Vue.createApp({
        data() {
            return {
                timer:null,
                basic:{!! json_encode($config_basic) !!},
                user:{!! json_encode($config_user) !!},
                notice:{!! json_encode($config_noticepre) !!},
                appoint:{!! json_encode($config_appointpre) !!},
                station:{!! json_encode($config_stationpre) !!},
                operation:{!! json_encode($config_operationpre) !!},
            }
        },
        mounted(){
        },
        methods:{
            clear(){
                this.basic.bans.length=0;
            },
            delban(index){
                this.basic.bans.splice(index,1);
            },
            addban(index){
                this.basic.bans.push("");
            },
            alter(config){
                let data=Object.assign({},this[config]);
                for(let i in data){
                    if(typeof data[i] === 'object'){
                        data[i]=JSON.stringify(data[i]);
                    }
                }
                data._token="{{csrf_token()}}";
                getData("{!! config('var.aal') !!}"+config,null,"#msg",data);
            },
        },
    }).mount("#alterapp");

</script>
@endsection

@section('nextJS')

@endsection
