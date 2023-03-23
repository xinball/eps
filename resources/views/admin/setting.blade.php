@extends('admin.master')

@section('title','网站配置')
@section('nextCSS2')

@endsection

@section('main')
<div id="alterapp" class="col-sm-12 col-lg-10 col-xxl-8 align-self-auto justify-content-center" style="margin: auto;">
    <form class="needs-validation" novalidate>
        <h4 class="mb-3">基本</h4>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="name" class="form-label">防疫系统名称</label>
                <input type="text" class="form-control" id="name" v-model="basic.name" placeholder="EPS" required>
            </div>
            <div class="col-md-6">
                <label for="status" class="form-label">网站状态</label>
                <select class="form-select" v-model="basic.status" id="status" required>
                    <option value="1">正常运行</option>
                    <option value="2">维护中</option>
                    <option value="0">关闭</option>
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
                <label for="useravatar" class="form-label">比赛图片路径</label>
                <input type="text" class="form-control" id="contestavatar" v-model="basic.contestavatar" placeholder="/img/contest/" required>
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
        </div>
        <hr class="my-4">
        <button class="w-100 btn btn-outline-success btn-lg" type="button" @click="alter('basic')">修改基本配置</button>
        <hr class="my-4">
        <h4 class="mb-3">用户</h4>
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
        <h4 class="mb-3">通知</h4>
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
        <h4 class="mb-3">问题</h4>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="problemlistnum" class="form-label">每页数量</label>
                <input type="number" class="form-control" id="problemlistnum" v-model="problem.listnum" placeholder="20" required>
            </div>
            <div class="col-md-6">
                <label for="problempagenum" class="form-label">显示页面数量/2</label>
                <input type="number" class="form-control" id="problempagenum" v-model="problem.pagenum" placeholder="3" required>
            </div>
            <div class="col-md-6">
                <label for="problemetype" class="form-label">类型</label>
                <textarea class="form-control" id="problemtype" rows="5" style="resize:none;"  v-model="problem.type" required></textarea>
            </div>
            <div class="col-md-6">
                <label for="problemtypekey" class="form-label">类型分类</label>
                <textarea class="form-control" id="problemtypekey" rows="5" style="resize:none;"  v-model="problem.typekey" required></textarea>
            </div>
            {{-- <div class="col-12">
                <label for="problemadis" class="form-label">管理 类型显示效果</label>
                <textarea class="form-control" id="problemadis" rows="5" style="resize:none;"  v-model="problem.adis" required></textarea>
            </div> --}}
        </div>
        <hr class="my-4">
        <button class="w-100 btn btn-outline-success btn-lg" type="button" @click="alter('problem')">修改问题配置</button>
        <hr class="my-4">
        <h4 class="mb-3">比赛</h4>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="contestlistnum" class="form-label">每页数量</label>
                <input type="number" class="form-control" id="contestlistnum" v-model="contest.listnum" placeholder="20" required>
            </div>
            <div class="col-md-6">
                <label for="contestpagenum" class="form-label">显示页面数量/2</label>
                <input type="number" class="form-control" id="contestpagenum" v-model="contest.pagenum" placeholder="3" required>
            </div>
            <div class="col-md-6">
                <label for="contesttype" class="form-label">类型</label>
                <textarea class="form-control" id="contesttype" rows="5" style="resize:none;"  v-model="contest.type" required></textarea>
            </div>
            <div class="col-md-6">
                <label for="contesttypekey" class="form-label">类型分类</label>
                <textarea class="form-control" id="contesttypekey" rows="5" style="resize:none;"  v-model="contest.typekey" required></textarea>
            </div>
            <div class="col-12">
                <label for="contestudis" class="form-label">用户管理 类型显示效果</label>
                <textarea class="form-control" id="contestudis" rows="5" style="resize:none;"  v-model="contest.udis" required></textarea>
            </div>
            <div class="col-12">
                <label for="contestadis" class="form-label">管理 类型显示效果</label>
                <textarea class="form-control" id="contestadis" rows="5" style="resize:none;"  v-model="contest.adis" required></textarea>
            </div>
        </div>
        <hr class="my-4">
        <button class="w-100 btn btn-outline-success btn-lg" type="button" @click="alter('contest')">修改比赛配置</button>
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
                problem:{!! json_encode($config_problempre) !!},
                contest:{!! json_encode($config_contestpre) !!},

                adis:isJSON({!! json_encode($config_status['adis'],JSON_UNESCAPED_UNICODE) !!},true),
                sdisplay:false,
                adisplay:false,
                udisplay:false,
                info:{
                    snums:{sum:0},
                    sysinfo:{
                        err:null,
                        data:{
                            cpu:0,
                            memory:0,
                            cpu_core:0,
                        },
                    }
                }
            }
        },
        mounted(){
            this.refreshStatus();
            clearInterval(this.timer);
            this.timer=setInterval(() => {
                this.refreshStatus()
            }, 5000);
        },
        methods:{
            alter:function (config){
                let data=this[config];
                data._token="{{csrf_token()}}";
                getData("{!! config('var.aal') !!}"+config,null,"#msg",this[config]);
            },
            refreshStatus(){
                let that=this;
                getData("{!! config('var.jp') !!}",function(json){
                    if(json.status===1){
                        that.info=json.data.info;
                        if(that.info.snums.sum!=='0'&&that.info.snums.sum!==0){
                            that.sdisplay=true;
                        }
                        if(that.info.asnums.sum!=='0'&&that.info.asnums.sum!==0){
                            that.adisplay=true;
                        }
                        if(that.info.usnums.sum!=='0'&&that.info.usnums.sum!==0){
                            that.udisplay=true;
                        }
                    }
                },"#msg",data=null,jump=true,false);
            },
        },
    }).mount("#alterapp");


</script>
@endsection

@section('nextJS')

@endsection
