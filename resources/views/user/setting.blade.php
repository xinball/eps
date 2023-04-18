@extends('user.master')

@section('title','个人信息-')
@section('nextCSS2')

@endsection

@section('main')


<!--上方的个人头像和横幅是一个小零件，在components的avatar.blade.php里边-->
<!--个人中心的页面内容-->
    <div class="col-sm-12 col-lg-10 col-xxl-8 align-self-auto justify-content-center" style="margin: auto;">
    <!--因为有x-avatar，所以上方能出现用户的个人信息标签和头像，因为x-avatar继承了avatar.blade.php-->
        <x-avatar>
            <form class="needs-validation" novalidate>
                <hr class="my-4">
                <h4 class="mb-3">只读信息</h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="uname" class="form-label">姓名</label>
                        <div class="input-group has-validation">
                            <span class="input-group-text" v-text="user.uid"></span>
                            <input type="text" class="form-control" id="uname" v-model="user.uname" placeholder="Username" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="uemail" class="form-label">邮箱账号</label>
                        <input type="email" class="form-control" id="uemail" v-model="user.uemail" placeholder="you@example.com" disabled>
                    </div>
                    <div class="col-md-6">
                        <label for="uidtype" class="form-label">身份类型</label>
                        <select class="form-select" v-model="user.uidtype" id="uidtype" disabled>
                            <option v-for="(uidtype,index) in uidtypes" :value="index" :label="uidtype">@{{ uidtype }}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="uidno" class="form-label">身份证明</label>
                        <input type="text" class="form-control" id="uidno" v-model="user.uidno" placeholder="" disabled>
                    </div>
                    <div class="col-md-6">
                        <label for="utype" class="form-label">用户类型</label>
                        <select class="form-select" v-model="user.utype" id="utype" disabled>
                            <option v-for="(utype,index) in utypes" :value="index" :label="utype">@{{ utype }}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="regtime" class="form-label">注册时间</label>
                        <input type="datetime-local" class="form-control" id="regtime" v-model="user.utime" disabled>
                    </div>
                    <div class="col-md-6">
                        <label for="regip" class="form-label">注册IP</label>
                        <input type="text" class="form-control" id="regip" v-model="user.uinfo.reg_ip" placeholder="localhost" disabled>
                    </div>
                </div>
                <hr class="my-4">
                <h4 class="mb-3">必填信息</h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="upwd" class="form-label">密码</label>
                        <div class="input-group has-validation">
                            <input type="password" class="form-control" v-model="upwd" id="upwd" placeholder="请填写您的密码" required>
                            <div class="invalid-feedback">
                                必须填写密码！
                            </div>
                        </div>
                    </div>


                    <!--div class="col-12">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" placeholder="1234 Main St" required>
                        <div class="invalid-feedback">
                            Please enter your shipping address.
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="address2" class="form-label">Address 2 <span class="text-muted">(Optional)</span></label>
                        <input type="text" class="form-control" id="address2" placeholder="Apartment or suite">
                    </div-->


                    <div class="col-md-6">
                        <label for="lang" class="form-label">语言</label>
                        <select class="form-select" v-model="user.uinfo.lang" id="lang" required>
                            <option value="cn">中文</option>
                            <option value="en">英文</option>
                        </select>
                        <div class="invalid-feedback">
                            必须选择您的语言
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="private" class="form-label">信息安全</label>
                        <select class="form-select" v-model="user.uinfo.private" id="private" required>
                            <option value="1">保密</option>
                            <option value="0">公开</option>
                        </select>
                        <div class="invalid-feedback">
                            必须选择您的信息安全选项
                        </div>
                    </div>

                </div>

                <hr class="my-4">
                <h4 class="mb-3">选填信息</h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="sex" class="form-label">性别</label>
                        <select class="form-select" v-model="user.uinfo.sex" id="sex">
                            <option value="0">女</option>
                            <option value="1">男</option>
                            <option value="2">保密</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="homepage" class="form-label">个人主页</label>
                        <div class="input-group has-validation">
                            <select class="form-select" v-model="user.uinfo.homepagessl" id="homepagessl">
                                <option value="0">http://</option>
                                <option value="1">https://</option>
                            </select>
                            <input type="text" class="form-control" v-model="user.uinfo.homepage" id="homepage" :placeholder="'eps.yono.top/user/'+user.uid" >
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="slogan" class="form-label">个性签名</label>
                        <input type="text" class="form-control" v-model="user.uinfo.slogan" id="slogan" placeholder="请填写您的个性签名" >
                    </div>
                    <div class="col-md-6">
                        <label for="tel" class="form-label">联系电话</label>
                        <input type="tel" class="form-control" v-model="user.uinfo.tel" id="tel" placeholder="请填写您的联系电话" >
                    </div>
                    <div class="col-md-6">
                        <label for="qq" class="form-label">QQ</label>
                        <input type="text" class="form-control" v-model="user.uinfo.qq" id="qq" placeholder="请填写您的QQ">
                    </div>
                    <div class="col-md-6">
                        <label for="wid" class="form-label">微信</label>
                        <input type="text" class="form-control" v-model="user.uinfo.wid" id="wid" placeholder="请填写您的微信号">
                    </div>
                </div>

                <hr class="my-4">
                <h4 class="mb-3">安全信息</h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="upwd1" class="form-label">新密码</label>
                        <input type="password" class="form-control" v-model="upwd1" id="upwd1" placeholder="请填写您新的密码">
                    </div>
                    <div class="col-md-6">
                        <label for="upwd2" class="form-label">重复新密码</label>
                        <input type="password" class="form-control" v-model="upwd2" id="upwd2" placeholder="请重复填写您新的密码">
                    </div>
                </div>
                <hr class="my-4">
                <!--点击修改会调用service/user/alter-->
                <!--修改时的逻辑全在alter里-->
                <button class="w-100 btn btn-outline-success btn-lg" type="button" @click="alter">修改</button>
            </form>
        </x-avatar>
    </div>
@endsection

@section('nextJS')
@endsection
