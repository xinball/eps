@extends('admin.master')

@section('title','用户管理')
@section('nextCSS2')

@endsection

@section('main')

{{-- @include('template.userinsert') --}}
{{-- @include('template.useralter') --}}

{{-- <button type="button" data-bs-toggle="modal" data-bs-target="#insert" class="btn btn-outline-dark"><i class="bi bi-patch-plus-fill"></i> 添加公告</button> --}}
<main id="userlist" class="container list shadow">

    <!--用户筛选的侧边框-->
    <x-offcanvas>
        <form>
            <div class="mb-3 col-12">
                <div class="input-group">
                    <select class="form-select" v-model="params.order">
                        <option v-for="(ordertype,index) in ordertypes" :key="index" :label="ordertype" :value="index">@{{ ordertype }}</option>
                    </select>
                    <button type="button" class="btn btn-outline-dark" @click="set('desc')" :title="params.desc==='0'?'正序【从小到大】':'倒序【从大到小】'"><i class="bi" :class="{'bi-sort-up-alt':params.desc==='0','bi-sort-up':params.desc==='1'}" ></i></button>
                    <button type="button" class="btn btn-outline-info" @click="reset">重置 <i class="bi bi-arrow-clockwise"></i></button>
                    <button type="button" class="btn btn-outline-success" data-bs-dismiss="offcanvas"   @click="getData(params)">查询 <i class="bi bi-search"></i></button>
                </div>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="number" class="form-control" v-model="params.uid" id="paramsuid" placeholder="用户编号">
                <label for="paramsuid">UID</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <select class="form-select" v-model="params.uidtype" id="paramsuidtype">
                    <option v-for="(idtype,index) in idtypes" :key="index" :label="idtype" :value="index">@{{ idtype }}</option>
                    <option value="" label="所有身份证件类型">所有类型</option>
                </select>
                <label for="ruidtype">身份证件类型</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" class="form-control" v-model="params.uidno" id="paramsuidno" placeholder="身份证号">
                <label for="paramsuidno">身份证号</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" class="form-control" v-model="params.uname" id="paramsuname" placeholder="用户名">
                <label for="paramsuname">用户名</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="email" class="form-control" v-model="params.uemail"  id="paramsuemail" placeholder="邮箱">
                <label for="paramsuemail">邮箱</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="datetime-local" class="form-control" v-model="params.ustart" id="paramsustart" placeholder="开始时间范围">
                <label for="paramsustart">开始时间范围</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="datetime-local" class="form-control" v-model="params.uend" id="paramsuend" placeholder="结束时间范围">
                <label for="paramsuend">结束时间范围</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <select class="form-select" v-model="params.type" id="paramstype">
                    <option v-for="(item,index) in adis" :key="index" :label="item.label" :value="index">@{{ item.label }}</option>
                </select>
                <label for="paramstype">用户类型</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <select class="form-select" v-model="params.sex" id="paramssex">
                    <option value="0">女</option>
                    <option value="1">男</option>
                    <option value="2">保密</option>
                    <option value="">所有</option>
                </select>
                <label for="paramssex" class="form-label">性别</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" class="form-control" v-model="params.tel"  id="paramstel" placeholder="手机">
                <label for="paramstel">手机</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="number" class="form-control" v-model="params.qq"  id="paramsqq" placeholder="QQ">
                <label for="paramsqq">QQ</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" class="form-control" v-model="params.wid"  id="paramswid" placeholder="微信号">
                <label for="paramswid">微信号</label>
            </div>
        </form>
    </x-offcanvas>
    <div class="input-group justify-content-center">
        <button v-for="(dis,index) in adis" @click="set('type',index)" class="btn" :class="[params.type===index?'btn-'+dis.btn:'btn-outline-'+dis.btn]" :disabled="data.num[index]===0">
            @{{ dis.label }} <span v-if="data.num[index]>0" :class="dis.num">@{{ data.num[index] }}</span>
        </button>
    </div>

    <!--用户板块显示-->
    <div v-if="users.length>0">
        <div class="item thead-dark thead">
            <div class="row align-items-center">
                <div class="d-none d-sm-block col-1"><a title="按用户编号排序" class="btn btn-fill btn-light" @click="set('order','uid')">#</a></div>
                <div class="col-2 col-sm-1"><a :title="params.desc==='0'?'正序【从小到大】':'倒序【从大到小】'" class="btn btn-fill btn-light" @click="set('desc')"><i class="bi" :class="{'bi-sort-up-alt':params.desc==='0','bi-sort-up':params.desc==='1'}" ></i></a></div>
                <div class="col-2"><a title="按身份证号排序" class="btn btn-fill btn-light" @click="set('order','uidno')">ID</a></div>
                <div class="col-3 col-md-2"><a title="按用户名排序" class="btn btn-fill btn-light" @click="set('order','uname')">用户名</a></div>
                <div class="col-3 col-md-2"><a title="按邮箱排序" class="btn btn-fill btn-light" @click="set('order','uemail')">邮箱</a></div>
                <div class="col-2"><a title="所有用户类型" class="btn btn-fill btn-light" @click="set('type','')">类型</a></div>
                <div class="d-none d-md-block col-2"><a title="按注册时间排序" class="btn btn-fill btn-light" @click="set('order','reg')">注册时间</a></div>
            </div>
        </div>
        <!--显示出来-->
        <div class="item text-center list-group-item-action" v-for="(user,index) in users" :key="index" data-bs-toggle="modal" :data-bs-index="index" title="点击编辑用户信息" data-bs-target="#alter" :data-bs-nid="user.uid">
            <div class="row align-items-center">
                <div class="d-none d-sm-block col-sm-1 thead" >@{{ user.uid }}</div>
                <div class="col-2 col-sm-1"><img class="rounded-pill" style="width: 100%;max-width:32px;" :src="user.avatar" alt=""> </div>
                <div class="col-2 text-truncate" :title="user.uidno">@{{ user.uidno }}</div>
                <div class="col-3 col-md-2 text-truncate" :title="user.uname">@{{ user.uname }}</div>
                <div class="col-3 col-md-2 text-truncate" :title="user.uemail">@{{ user.uemail }}</div>
                <div @click="set('type',user.utype)" class="col-2 text-truncate">
                    <a :title="'筛选用户类型：'+adis[user.utype].label" class="btn btn-fill" :class="['btn-outline-'+adis[user.utype].btn]" @click="set('status',operation.oresult.status)">@{{adis[user.utype].label}}</a>
                </div>
                <div class="d-none d-md-block col-2" style="font-size:x-small;">@{{ user.utime }}</div>
            </div>
        </div>
        @include('template.paginator')
    </div>
    <p v-if="users.length===0">抱歉，查询不到任何用户！</p>

</main>

@endsection

@section('nextJS')


<script>
    const userlist=Vue.createApp({
        data(){
            return{
                timer:null,
                time:{
                    before:7*86400000,
                    length:1000,
                    send:[],
                    update:[]
                },

                dataname:"users",
                paginator:{},
                pagenum:{{ $config_user['pagenum'] }},
                data:{num:{sum:0}},
                users:[],
                url:"{{ config('var.aul') }}",
                adis:{!! json_encode($config_user['adis'],JSON_UNESCAPED_UNICODE) !!},
                idtypes:{!! json_encode($config_user['idnotype'],JSON_UNESCAPED_UNICODE) !!},
                paramspre:{},
                params:{
                    page:"1",
                    uname:"",
                    uemail:"",
                    type:"sum",
                    ustart:"2023-01-01T00:00:00",
                    uend:"2023-12-31T00:00:00",
                    uid:"",
                    uidno:"",
                    uidtype:"",
                    tel:"",
                    qq:"",
                    wid:"",
                    sex:"",
                    order:"uid",
                    desc:"0"
                },
                ordertypes:{
                    uid:"按用户编号排序",
                    uidno:"按身份证号排序",
                    uname:"按用户名排序",
                    uemail:"按邮箱排序",
                    reg:"按注册时间排序",
                },
            }
        },
        mounted(){
            //过滤器
            filterTypes(this.ntypes,this.typekey);
            //初始化分页
            initpaginator(this);
            this.getData();
        },
        computed:{
        },
        methods:{
            //复位
            reset(){
                this.params=this.paramspre={
                    page:"1",
                    uname:"",
                    uemail:"",
                    type:"sum",
                    ustart:"2023-01-01T00:00:00",
                    uend:"2023-12-31T00:00:00",
                    uid:"",
                    uidno:"",
                    uidtype:"",
                    tel:"",
                    qq:"",
                    wid:"",
                    sex:"",
                    order:"uid",
                    desc:"0"
                };
            },
        }
    }).mount('#userlist');
    //current_page,first_page_url,last_page,last_page_url,next_page_url,path,per_page,prev_page_url,from,to,total
</script>
@endsection
