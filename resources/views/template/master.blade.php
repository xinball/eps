<!doctype html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta name="description" content="Epidemic Prevention System">
    <meta name="keywords" content="EPS">
    <meta name="author" content="XinBall">
    <title>{{ $config_basic['name'] }} - @yield('title')</title>
    <link rel="icon" href="{{ '/img/favicon.png' }}" type="image/x-icon" />
    <link rel="shortcut icon" href="{{ '/img/favicon.png' }}" type="image/x-icon" />

    @yield('startJS')

    <script charset="utf-8" src="//cdn.iframe.ly/embed.js?api_key=d80aee67662c35f142b878"></script>
    <script src="{{ '/js/jquery-3.6.0.min.js' }}"></script>

    <script src="{{ '/js/main.js' }}"></script>
    <script src="{{ '/js/vue.global.js' }}"></script>
    <script src="{{ '/jszip/jszip.js' }}"></script>
    <script src="{{'/ckeditor5-34.0.5/build/ckeditor.js'}}"></script>
    <script src="{{'/ckeditor5-34.0.5/build/translations/zh.js'}}"></script>
    <script src="{{ '/echarts-5.1.1/dist/echarts.min.js' }}"></script>
    <script src="https://cdn.bootcss.com/vConsole/3.2.0/vconsole.min.js"></script>
    
    <script>
        //let vConsole = new VConsole();
        const result={!! isset($result)?json_encode($result,JSON_UNESCAPED_UNICODE):json_encode('{"status":"0","message":null,"url":null,"data":null}',JSON_UNESCAPED_UNICODE) !!};
        let json=isJSON(result);
        // let tags=[],tags0=[];
        // getTags(json,tags,tags0);

        //javascript: window.open( `https://video-direct-link.vercel.app/bili.mp4?aid=${aid}&bvid=${bvid}&cid=${cid}`)
        let editorconfig={
            placeholder:"在此处输入或粘贴内容",
            link:{
                addTargetToExternalLinks: true,
                decorators: [
                    {
                        mode: 'manual',
                        label: '允许下载',
                        attributes:{
                            download: 'download'
                        }
                    }
                ]
            },
            image: {
                toolbar: [
                    'imageTextAlternative',
                    'imageStyle:inline',
                    'imageStyle:block',
                    'imageStyle:side',
                    'linkImage'
                ]
            },
            table: {
                contentToolbar: [
                    'tableColumn',
                    'tableRow',
                    'mergeTableCells',
                    'tableCellProperties',
                    'tableProperties'
                ]
            },
            mediaEmbed:{
                extraProviders:[
                    {
                        name:'allow-all',
                        url:/^.+mp4/,
                        html:match=>
                            `<div>
                                <iframe src=${match} frameborder="10" allowfullscreen="true" style="position:absolute;width:99%;height:400px;"></iframe>    
                            </div>`
                    }
                ]
            }
        };

    function setproblemsta(sta,stadata,title="") {
    sta.setOption({
        title:{
        left:'center',
            text:title,
        },
        tooltip: {
        trigger: 'item',
        formatter: '{b} : {c}({d}%)'
        },
        legend: {
        bottom: 0,
        left: 'center',
        data: ['AC','CE','WA','RE','TL','ML','SE'],
        },
        series: [{
        type: 'pie',
        radius: ['30%','70%'],
        center: ['50%','40%'],
        selectedMode: 'single',
        itemStyle: {
        borderRadius: 4,
        borderColor: '#fff',
        borderWidth: 1
        },
        label: {
        show: false,
        position: 'center'
        },
        emphasis: {
        itemStyle:{
        shadowBlur:5,
        shadowOffsetX:0,
        shadowColor:'rgb(0,0,0,0.5)'
        },
        label: {
        show: true,
        fontSize: '40',
        fontWeight: 'bold'
        }
        },
        color:[
        "#64c06c",
        "grey",
        "red",
        "sandybrown",
        "orange",
        "deepskyblue",
        "black",
        ],
        data: stadata
        }
        ]
    });
    }
    
    window._AMapSecurityConfig = {
        serviceHost:'_AMapService',
        securityJsCode:'c8c7b75c541cdc9d060c1701ac54d81d'
        // 例如 ：serviceHost:'http://1.1.1.1:80/_AMapService',
    }
    </script>
    @yield('endJS')
    @yield('preCSS')

    <!-- Bootstrap core CSS -->
    <link href="{{ '/bootstrap/css/bootstrap.css' }}" rel="stylesheet">
    <link href="{{ '/css/main.css?m=1' }}" rel="stylesheet">
    <link href="{{ '/bootstrap/icon/bootstrap-icons.css' }}" rel="stylesheet">
    <link href="{{'/cropper/dist/cropper.min.css'}}" rel="stylesheet">
    <link href="{{'/cropper/css/home.css'}}" rel="stylesheet">
    <!-- <link rel="stylesheet" href="https://a.amap.com/jsapi_demos/static/demo-center/css/demo-center.css"/>  -->
    
    <style>
        .bg-ac {
            --bs-bg-opacity: 1;
            background-color: greenyellow !important;
        }
        .btn-pink:focus {
            outline: 0;
            box-shadow: 0 0 0 0.25rem #ff69b440;
        }
        .text-pink{
            color: hotpink;
        }
        .thead{
            font-size: small;
            font-weight:bold;
            vertical-align: middle;
            align-self:center;
        }
        .noexpand{
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }
        .noexpand::-ms-expand{
            display: none;
        }
        .item {
            /* gap: 0.8rem !important; */
            border: 0 !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125) !important;
            /* position: relative; */
            /* display: block; */
            padding: 25px 0 10px 0;
            /* color: #212529; */
            text-decoration: none;
            background-color: #fff;
            margin:0;
        }
        .list-group-item.active{
            background-color: skyblue;
        }
        .item .row{
            margin:0;
        }
        .item .row div{
            padding: 0;
        }
        /* .list .item div{
            padding: 0px;
        } */
        .pagination>.active>a, .pagination>.active>a:focus, .pagination>.active>a:hover, .pagination>.active>span, .pagination>.active>span:focus, .pagination>.active>span:hover,.page-link, .page-link:focus, .page-link:hover {
            z-index: 3;
            color: #fff;
            cursor: default;
            background-color: hotpink;
            border-color: lightpink;
        }
        /* .page-item.active .page-link {
            z-index: 3;
            color: #fff;
            background-color: #0d6efd;
            border-color: #0d6efd;
        } */
        .pagination>li>a, .pagination>li>span {
            position: relative;
            float: left;
            padding: 6px 12px;
            margin-left: -1px;
            /* line-height: 1.42857143; */
            color: #3f3f3f;
            text-decoration: none;
            background-color: #fff;
            border: 1px solid #ddd;
        }
        .page-link:focus {
            box-shadow: 0 0 0 0.25rem lightpink;
        }
        .page-link{

            line-height: 27px;
        }
        


        .pagination-lg>li:last-child>a, .pagination-lg>li:last-child>span {
            border-top-right-radius: 6px;
            border-bottom-right-radius: 6px;
        }
        .pagination>li:last-child>a, .pagination>li:last-child>span {
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
        }
        .pagination>.active>span>label>input{
            z-index: 3;
            color: #fff;
            cursor: default;
            background-color: hotpink;
            font-size: 18px;
            border: 0 lightpink;
            padding: 0;
            margin: 0;
            text-align: center;
        }
        .pagination>.active>span>label>input:focus{
            box-shadow: 0 0 0 0.25rem lightpink;
            outline: 0;
        }

    </style>

    @yield('nextCSS')

</head>
<body id="body">
    <!--最上方会出现的消息框，比如“退出登录成功”-->
<div id="msg" style="z-index:2000;position: fixed;width: 100%;top:0;"></div>          
<!--加载的小圆圈-->
<div id="loading" class="spinner-border" style="z-index:4000;display:none;position: fixed;top: 50%;left: 50%;width: 3rem; height: 3rem;" role="status">
    <span class="visually-hidden">Loading...</span>
  </div>
  <!--整个上方的索引框-->
<nav id="navapp" class="navbar navbar-expand-lg navbar-light fixed-top shadow" style="background-color: white;">
    <div class="container-fluid">
        <h5 class="btn btn-light mb-0" style="font-weight: bold;" data-bs-toggle="offcanvas" href="#offcanvas" role="button" aria-controls="offcanvas">{{ $config_basic['name'] }} <i class="bi bi-search"></i></h5>
        <!--搜索框-->
        <form class="d-flex" action="/search" method="get">
            <div class="input-group">
                <input class="form-control" style="max-width: 130px;width: 50%" type="search" name="keyword" placeholder="通知/问题/比赛/用户" aria-label="Search">
                <button class="btn btn-outline-success" type="submit"><i class="bi bi-search"></i></button>
            </div>
        </form>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!--各个小功能-->
        <div class="collapse navbar-collapse dl-horizontal" id="navbarCollapse" style="margin-left: 10px;">
            <ul id="navs" class="nav nav-tabs navbar-nav me-auto mb-2 mb-md-0">
                <li class="nav-item">
                    <a class="nav-link {{ isset($nactive)&&$nactive?"active":"" }}" href="/"><i class="bi bi-easel2-fill"></i> 公告</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ isset($sactive)&&$sactive?"active":"" }}" href="/station"><i class="bi bi-pin-map-fill"></i> 站点</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ isset($lactive)&&$lactive?"active":"" }}" href="/location"><i class="bi bi-buildings-fill"></i> 报备点</a>
                </li>
            </ul>
            <!--右上方的模块-->
            <div class="navbar-right">
                <ul class="navbar-nav me-auto mb-2 mb-md-0">
                @php
                $uf=0;$af=0;
                if(isset($ladmin)&&$ladmin!==null){
                    $af=1;
                    echo '
                    <!--管理员的登录后的下拉框-->
                <li class="nav-item dropdown">
                    <a href="#" class="btn btn-fill btn-pink rounded-pill dropdown-toggle" data-bs-toggle="dropdown" id="DropdownAdmin" role="button" aria-expanded="false">' .
                        '<img src="'.$ladmin->avatar.'" style="border-radius: 5px;height:24px;border:1px solid #ffffff70;"> '.$ladmin->uname. '</a>
                    <ul class="dropdown-menu" aria-labelledby="DropdownAdmin">
                        <li><a class="dropdown-item" target="_blank" href="/admin/appoint"><i class="bi bi-calendar-event-fill"></i> 预约管理</a></li>
                        <li><a class="dropdown-item" target="_blank" href="/admin/report"><i class="bi bi-clipboard-fill"></i> 报备管理</a></li>
                        <li><a class="dropdown-item" target="_blank" href="/admin/station"><i class="bi bi-geo-fill"></i> 管理站点</a></li>
                        <li><a class="dropdown-item" target="_blank" href="/admin/location"><i class="bi bi-building-fill-gear"></i> 管理报备点</a></li>
                        <li><a class="dropdown-item" target="_blank" href="/admin/user"><i class="bi bi-person-fill-gear"></i> 管理用户</a></li>
                        <li><a class="dropdown-item" target="_blank" href="/admin/notice"><i class="bi bi-easel2-fill"></i> 管理公告</a></li>
                        <li><a class="dropdown-item" target="_blank" href="/admin/setting"><i class="bi bi-gear-fill"></i> 网站配置</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" target="_blank" href="/user/'.$ladmin['uid'].'"><i class="bi bi-house-fill"></i> 个人空间</a></li>
                        <li><a class="dropdown-item" href="#" @click="alogout"><i class="bi bi-box-arrow-right"></i> 退出登录</a></li>
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#alogin"><i class="bi bi-box-arrow-in-right"></i> 登录其他管理员</a></li>
                    </ul>
                </li>
                    ';
                }
                if(isset($luser)&&$luser!==null){
                    echo '
                    <!--用户登录后的下拉框-->
                <li class="nav-item dropdown">
                    <a href="#" class="btn btn-fill btn-deepskyblue rounded-pill dropdown-toggle" data-bs-toggle="dropdown" id="DropdownUser" role="button" aria-expanded="false">' .
                        '<img src="'.$luser->avatar.'" style="border-radius: 5px;height:24px;border:1px solid #ffffff70;"> '.$luser->uname. '</a>
                    <ul class="dropdown-menu" aria-labelledby="DropdownUser">
                        <li><a class="dropdown-item" target="_blank" href="/user/setting"><i class="bi bi-gear-wide-connected"></i> 个人设置</a></li>
                        <li><a class="dropdown-item" target="_blank" href="/user/appoint"><i class="bi bi-calendar-event-fill"></i> 预约管理</a></li>
                        <li><a class="dropdown-item" target="_blank" href="/user/report"><i class="bi bi-clipboard-fill"></i> 报备管理</a></li>
                        <li><a class="dropdown-item" target="_blank" href="/user/feedback"><i class="bi bi-exclamation-octagon-fill"></i> 反馈</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" target="_blank" href="/user/'.$luser['uid'].'"><i class="bi bi-house-fill"></i> 个人空间</a></li>
                        <li><a class="dropdown-item" href="#" @click="ulogout"><i class="bi bi-box-arrow-right"></i> 退出登录</a></li>
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#login"><i class="bi bi-box-arrow-in-right"></i> 登录其他用户</a></li>
                    </ul>
                </li>
                    ';
                    $uf=1;
                }

                if($af==0&&$uf==0){
                    echo '
                <!--都未登录，三个按钮-->
                <button type="button" class="btn rounded-pill btn-outline-dark" data-bs-toggle="modal" data-bs-target="#login" >用户登录</button>
                <button type="button" class="btn rounded-pill btn-outline-dark" data-bs-toggle="modal" data-bs-target="#register" >注册</button>
                <button type="button" class="btn rounded-pill btn-outline-dark" data-bs-toggle="modal" data-bs-target="#alogin" >后台登录</button>
                    ';
                }else if($af==0){
                    echo '
                <!--只有用户登录-->
                <button type="button" class="btn rounded-pill btn-outline-dark" data-bs-toggle="modal" data-bs-target="#register" >注册</button>
                <button type="button" class="btn rounded-pill btn-outline-dark" data-bs-toggle="modal" data-bs-target="#alogin" >后台登录</button>
                    ';
                }else if($uf==0){
                    echo '
                <!--只有后台登录-->
                <button type="button" class="btn rounded-pill btn-outline-dark" data-bs-toggle="modal" data-bs-target="#login" >用户登录</button>
                <button type="button" class="btn rounded-pill btn-outline-dark" data-bs-toggle="modal" data-bs-target="#register" >注册</button>
                    ';
                }else{
                    echo '
                <!--都登陆-->
                <button type="button" class="btn rounded-pill btn-outline-dark" data-bs-toggle="modal" data-bs-target="#register" >注册</button>
                    ';
                }
                @endphp
                </ul>
            </div>
        </div>
    </div>
</nav>
<!--x-model（vue框架）用于绑定，比如这个绑定loginapp-->
<x-modal id='login' title="欢迎登录 {{ $config_basic['name'] }}">
    <div class="form-login text-center" id="loginapp">
        <form>
            <img class="mb-4" style="border-radius: 20px;border: 3px solid deepskyblue;" :src="avatar" alt="" width="72" >
            <div class="form-floating">
            <!--v-model（vue框架）也用于绑定-->
                <input type="text" class="form-control" id="uidno" name="uidno" v-model="uidno" @change="getavatar" placeholder=" " @keyup.enter="login">
                <label for="uidno">请输入身份证明/邮箱</label>
            </div>
            <div class="form-floating">
                <input type="text" class="form-control" id="uname" name="uname" v-model="uname" placeholder=" " @keyup.enter="login">
                <label for="uname">请输入姓名</label>
            </div>

            <div class="form-floating">
                <input type="password" class="form-control" id="upwd" name="upwd" v-model="upwd" placeholder="Password" @keyup.enter="login">
                <label for="upwd">请输入密码</label>
            </div>

            <div class="checkbox mb-3">
                <label>
                    <input type="checkbox" id="uremember" name="uremember" v-model="uremember" value="1"> 记住密码
                </label>
            </div>
            <button class="w-100 btn btn-lg btn-success" @click="login()" type="button">登录</button>
        </form>
        <!--跳转-->
        <a href="#" class="link-primary link-nounderline left" data-bs-toggle="modal" data-bs-target="#register" >立即注册</a>
        <a href="/active" class="link-primary link-nounderline" >用户激活</a>
        <a href="/forget" class="link-primary link-nounderline right" >忘记密码？</a>
    </div>
</x-modal>

<!--x-model（vue框架）用于绑定，比如这个绑定aloginapp-->
<x-modal id='alogin' title="欢迎登录 {{ $config_basic['name'] }} 后台管理">
    <div class="form-login text-center" id="aloginapp">
        <form>
            <img class="mb-4" style="border-radius: 20px;border: 3px solid hotpink;" :src="avatar" alt="" width="72" >
            <!--h1 class="h3 mb-3 fw-normal">欢迎进入 </h1-->
            <div class="form-floating">
                <input type="text" class="form-control" id="aidno" name="aidno" v-model="aidno" @change="getavatar" @keyup.enter="alogin" placeholder=" ">
                <label for="aidno">请输入身份证明/邮箱</label>
            </div>
            <div class="form-floating">
                <input type="text" class="form-control" id="aname" name="aname" v-model="aname" placeholder=" " @keyup.enter="alogin" >
                <label for="aname">请输入姓名</label>
            </div>

            <div class="form-floating">
                <input type="password" class="form-control" id="apwd" name="apwd" v-model="apwd" placeholder="Password" @keyup.enter="alogin">
                <label for="apwd">请输入密码</label>
            </div>
    
            <div class="checkbox mb-3">
                <label>
                    <input type="checkbox" id="aremember" v-model="aremember" name="aremember" value="1"> 记住密码
                </label>
            </div>
            <button class="w-100 btn btn-lg btn-success" @click="alogin()" type="button">登录</button>
        </form>
    </div>
</x-modal>

<!--x-model（vue框架）用于绑定，比如这个绑定registerapp-->
<x-modal id='register' title="欢迎注册 {{ $config_basic['name'] }} 用户">
    <div class="form-login text-center" id="registerapp">
        <form>
            {{-- <img class="mb-4" src="{{ '/img/icon.png' }}" alt="" width="72" > --}}
            <div class="form-floating">
                <input type="text" class="form-control" v-model="runame" name="runame" id="runame" placeholder="name" @keyup.enter="register">
                <label for="runame">请输入您要注册的姓名</label>
            </div>
            <div class="form-floating">
                <select class="form-select" v-model="ruidtype" id="ruidtype" @keyup.enter="register" required>
                    <option v-for="(idtype,index) in idtypes" :key="index" :label="idtype" :value="index">@{{ idtype }}</option>
                </select>
                <label for="ruidtype">请输入您的身份证件类型</label>
            </div>
            <div class="form-floating">
                <input type="text" class="form-control" v-model="ruidno" name="ruidno" id="ruidno" placeholder="name" @keyup.enter="register">
                <label for="ruidno">请输入您的身份编号</label>
            </div>
            <div class="form-floating">
                <input type="text" class="form-control" v-model="ruemail" name="ruemail" id="ruemail" placeholder="name@example.com" @keyup.enter="register">
                <label for="ruemail">请输入您的邮箱用于验证</label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control" v-model="rupwd" name="rupwd" id="rupwd" placeholder="Password" @keyup.enter="register">
                <label for="rupwd">请输入您的密码</label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control" v-model="rupwd1" name="rupwd1" id="rupwd1" placeholder="Password" @keyup.enter="register">
                <label for="rupwd1">请重复输入您的密码</label>
            </div>
            <button class="w-100 btn btn-lg btn-success" @click="register()" type="button">注册</button>
        </form>
        <a href="#" class="link-primary link-nounderline left" data-bs-toggle="modal" data-bs-target="#login" >立即登录！</a>
        <a href="/active" class="link-primary link-nounderline" >用户激活</a>
        <a href="/forget" class="link-primary link-nounderline right" >忘记密码？</a>
    </div>
</x-modal>

<!-- <script type="text/javascript" src="https://webapi.amap.com/maps?v=1.4.15&key=e9740f0d7d50ec4897813769d4551f76&plugin=AMap.CitySearch"></script> -->
<script src="https://webapi.amap.com/loader.js"></script>

@yield('body')

{!! $config_basic['copyright']  !!}
</body>

@yield('preJS')

<script src="{{ '/bootstrap/js/bootstrap.bundle.js' }}"></script>
<script src="{{ '/js/main.js' }}"></script>
<script src="{{'/cropper/assets/js/jquery.min.js'}}"></script>
<script src="{{'/cropper/assets/js/bootstrap.min.js'}}"></script>
<script src="{{'/cropper/dist/cropper.min.js'}}"></script>
<script src="{{'/cropper/js/main.js'}}"></script>

<script>
    $(function () {

        //把解码后的json打印出来，有的时候页面打开后需要提示一些信息，比如页面跳转后，我们还需要提示信息
        echoMsg("#msg",json);
        
        (Array.prototype.slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))).map(function (popoverTriggerEl) { return new bootstrap.Popover(popoverTriggerEl) });
        document.querySelectorAll( 'oembed[url]' ).forEach( element => {
            iframely.load( element, element.attributes.url.value );
        } );
    });


    const aloginapp=Vue.createApp({
        data() {
            return {
                aname:"",
                apwd:"",
                aremember:"",
                aidno:"",
                avatar:"/img/icon.png"
            }
        },
        methods:{
            getavatar(){
                let that=this;
                //用户有头像就用用户的，没有就用默认
                getData("{!! config('var.ug') !!}"+this.aidno,
                function(json){
                    if(json.data!==null){
                        that.avatar=json.data.user.avatar;
                    }else{
                        that.avatar="/img/icon.png";
                    }
                },"#alogin-msg",null,false);
            },
            alogin:function (){
                let data={
                    uname:this.aname,
                    upwd:this.apwd,
                    uidno:this.aidno,
                    remember:this.aremember,
                    _token:"{{csrf_token()}}"
                }
                getData("{!! config('var.al') !!}",null,"#alogin-msg",data);
            }
        }
    }).mount("#aloginapp");


    //用户登录，绑定x-model
    const loginapp=Vue.createApp({
        data() {
            return {
                uname:"",
                upwd:"",
                uidno:"",
                uremember:"",
                avatar:"/img/icon.png"
            }
        },
        methods:{
            //看用户有没有自己的头像，有的话就显示出来，没有的话就用默认
            getavatar(){
                let that=this;
                getData("{!! config('var.ug') !!}"+this.uidno,
                function(json){
                    if(json.data!==null){
                        that.avatar=json.data.user.avatar;
                    }else{
                        that.avatar="/img/icon.png";
                    }
                },"#login-msg",null,false);
            },
            login(){
                let data={
                    uname:this.uname,
                    upwd:this.upwd,
                    uidno:this.uidno,
                    remember:this.uremember,
                    _token:"{{csrf_token()}}"
                };
                getData("{!! config('var.ul') !!}",null,"#login-msg",data);
            }
        }
    }).mount("#loginapp");

    //用户注册，绑定x-model
    const registerapp=Vue.createApp({
        data() {
            return {
                runame:"",
                ruemail:"",
                rupwd:"",
                rupwd1:"",
                ruidno:"",
                ruidtype:"0",
                idtypes:{
                    "0":"中国居民身份证",
                    "1":"港澳台居民居住证",
                    "2":"港澳居民来往内地通行证",
                    "3":"台湾居民来往大陆通行证",
                    "4":"护照",
                    "5":"外国人永久居留身份证"
                }
            }
        },
        methods:{
            register:function (){
                let that=this;
                let data={
                    uname:this.runame,
                    uemail:this.ruemail,
                    upwd:this.rupwd,
                    upwd1:this.rupwd1,
                    uidno:this.ruidno,
                    uidtype:this.ruidtype,   
                    _token:"{{csrf_token()}}"
                };
                getData("{!! config('var.ur') !!}",null,"#register-msg",data);
            }
        }
    }).mount("#registerapp");

    //退出
    const navapp=Vue.createApp({
        data() {
            return {
            }
        },
        methods:{
            ulogout:function (){
                let that=this;
                getData("{!! config('var.ulo') !!}",null,"#msg");
            },

            alogout:function (){
                let that=this;
                getData("{!! config('var.alo') !!}",null,"#msg");
            }
        }
    }).mount("#navapp");
</script>
@yield('nextJS')
</html>
