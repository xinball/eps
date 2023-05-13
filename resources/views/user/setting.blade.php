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
                        <div class="input-group">
                            <span class="input-group-text">@{{ '#' + user.uid }}</span>
                            <div class="form-floating">
                                <input type="text" class="form-control" id="uname" v-model="user.uname" placeholder="Username" disabled>
                                <label for="uname" class="form-label">姓名</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <label class="input-group-text" for="uemail"><i class="bi bi-envelope-at"></i></label>
                            <div class="form-floating">
                                <input type="email" class="form-control" id="uemail" v-model="user.uemail" placeholder="you@example.com" disabled>
                                <label for="uemail" class="form-label">邮箱账号</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <label class="input-group-text" for="uidtype"><i class="bi bi-person-vcard"></i></label>
                            <div class="form-floating">
                                <select class="form-select" v-model="user.uidtype" id="uidtype" disabled>
                                    <option v-for="(uidtype,index) in uidtypes" :value="index" :label="uidtype">@{{ uidtype }}</option>
                                </select>
                                <label for="uidtype" class="form-label">身份类型</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <label class="input-group-text" for="uidno"><i class="bi bi-person-vcard-fill"></i></label>
                            <div class="form-floating">
                                <input type="text" class="form-control" id="uidno" v-model="user.uidno" placeholder="" disabled>
                                <label for="uidno" class="form-label">身份证明</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <label class="input-group-text" for="utype"><i class="bi bi-people"></i></label>
                            <div class="form-floating">
                                <select class="form-select" v-model="user.utype" id="utype" disabled>
                                    <option v-for="(item,index) in adis" :value="index" :label="item.label">@{{ item.label }}</option>
                                </select>
                                <label for="utype" class="form-label">用户类型</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <label class="input-group-text" for="regtime"><i class="bi bi-calendar2-plus"></i></label>
                            <div class="form-floating">
                                <input type="datetime-local" class="form-control" id="regtime" v-model="user.utime" disabled>
                                <label for="regtime" class="form-label">注册时间</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <label class="input-group-text" for="regip"><i class="bi bi-geo-alt"></i></label>
                            <div class="form-floating">
                                <input type="text" class="form-control" id="regip" v-model="user.uinfo.reg_ip" placeholder="localhost" disabled>
                                <label for="regip" class="form-label">注册IP</label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-4">
                <h4 class="mb-3">个人信息</h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <label class="input-group-text" for="sex"><i class="bi" :class="{'bi-x':user.uinfo.sex==='2','bi-gender-female':user.uinfo.sex==='0','bi-gender-male':user.uinfo.sex==='1'}"></i></label>
                            <div class="form-floating">
                                <select class="form-select" v-model="user.uinfo.sex" id="sex">
                                    <option value="0">女</option>
                                    <option value="1">男</option>
                                    <option value="2">保密</option>
                                </select>
                                <label for="sex" class="form-label">性别</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <label class="input-group-text" for="homepage"><i class="bi bi-link-45deg"></i></label>
                            <select class="form-select" v-model="user.uinfo.homepagessl" id="homepagessl">
                                <option value="0">http://</option>
                                <option value="1">https://</option>
                            </select>
                            <div class="form-floating">
                                <input type="text" class="form-control" v-model="user.uinfo.homepage" id="homepage" :placeholder="'eps.yono.top/user/'+user.uid" >
                                <label for="homepage" class="form-label">个人主页</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <label class="input-group-text" for="slogan"><i class="bi bi-vector-pen"></i></label>
                            <div class="form-floating">
                                <input type="text" class="form-control" v-model="user.uinfo.slogan" id="slogan" placeholder="请填写您的个性签名" >
                                <label for="slogan" class="form-label">个性签名</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <label class="input-group-text" for="tel"><i class="bi bi-telephone"></i></label>
                            <div class="form-floating">
                                <input type="tel" class="form-control" v-model="user.uinfo.tel" id="tel" placeholder="请填写您的联系电话">
                                <label for="tel" class="form-label">联系电话</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <label class="input-group-text" for="qq"><i class="bi bi-tencent-qq"></i></label>
                            <div class="form-floating">
                                <input type="text" class="form-control" v-model="user.uinfo.qq" id="qq" placeholder="请填写您的QQ">
                                <label for="qq" class="form-label">QQ</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <label class="input-group-text" for="wid"><i class="bi bi-wechat"></i></label>
                            <div class="form-floating">
                                <input type="text" class="form-control" v-model="user.uinfo.wid" id="wid" placeholder="请填写您的微信号">
                                <label for="wid" class="form-label">微信</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <label class="input-group-text" for="lang"><i class="bi bi-translate"></i></label>
                            <div class="form-floating">
                                <select class="form-select" v-model="user.uinfo.lang" id="lang" required>
                                    <option value="cn">中文</option>
                                    <option value="en">英文</option>
                                </select>
                                <label for="lang" class="form-label">语言</label>
                            </div>
                        </div>
                    </div>
                    <div v-show="con_ids!==undefined&&con_ids.length>0" class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" v-model="user.con_id" @change="getCountries()" id="con_id" required>
                                <option label="请选择洲" value="null">请选择洲</option>
                                <option v-for="con_id in con_ids" :key="con_id.id" :label="con_id.cname" :value="con_id.id">@{{ con_id.cname }}</option>
                            </select>
                            <label for="con_id" class="form-label">所在洲</label>
                        </div>
                    </div>
                    <div v-show="coun_ids!==undefined&&coun_ids.length>0" class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" v-model="user.coun_id" @change="getStates()" id="coun_id" required>
                                <option label="请选择国家" value="">请选择国家</option>
                                <option v-for="coun_id in coun_ids" :key="coun_id.id" :label="coun_id.cname" :value="coun_id.id">@{{ coun_id.cname }}</option>
                            </select>
                            <label for="coun_id" class="form-label">所在国家</label>
                        </div>
                    </div>
                    <div v-show="state_ids!==undefined&&state_ids.length>0" class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" v-model="user.state_id" @change="getCities()" id="state_id" required>
                                <option label="请选择省市区" value="">请选择省市区</option>
                                <option v-for="state_id in state_ids" :key="state_id.id" :label="state_id.cname" :value="state_id.id">@{{ state_id.cname }}</option>
                            </select>
                            <label for="state_id" class="form-label">所在省市区</label>
                        </div>
                    </div>
                    <div v-show="city_ids!==undefined&&city_ids.length>0" class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" v-model="user.city_id" @change="getRegions()" id="city_id" required>
                                <option label="请选择地级市" value="">请选择地级市</option>
                                <option v-for="city_id in city_ids" :key="city_id.id" :label="city_id.cname" :value="city_id.id">@{{ city_id.cname }}</option>
                            </select>
                            <label for="city_id" class="form-label">所在地级市</label>
                        </div>
                    </div>
                    <div v-show="region_ids!==undefined&&region_ids.length>0" class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" v-model="user.region_id" id="region_id" required>
                                <option label="请选择区县" value="">请选择区县</option>
                                <option v-for="region_id in region_ids" :key="region_id.id" :label="region_id.cname" :value="region_id.id">@{{ region_id.cname }}</option>
                            </select>
                            <label for="region_id" class="form-label">所在区县</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <label class="input-group-text" for="addr"><i class="bi bi-geo-alt"></i></label>
                            <div class="form-floating">
                                <input type="text" class="form-control" id="addr" v-model="user.uinfo.addr" placeholder="所在地址描述" required>
                                <label for="addr" class="form-label">所在地址描述</label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-4">
                <h4 class="mb-3">安全信息</h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <label class="input-group-text" for="upwd1"><i class="bi bi-person-lock"></i></label>
                            <div class="form-floating">
                                <input type="password" class="form-control" v-model="upwd1" id="upwd1" placeholder="请填写您新的密码">
                                <label for="upwd1" class="form-label">新密码</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <label class="input-group-text" for="upwd2"><i class="bi bi-person-fill-lock"></i></label>
                            <div class="form-floating">
                                <input type="password" class="form-control" v-model="upwd2" id="upwd2" placeholder="请重复填写您新的密码">
                                <label for="upwd2" class="form-label">重复新密码</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <label class="input-group-text" for="safe"><i class="bi " :class="{'bi-shield-lock':user.uinfo.safe==='0','bi-shield-lock-fill':user.uinfo.safe==='1'}"></i></label>
                            <div class="form-floating">
                                <select class="form-select" v-model="user.uinfo.safe" id="safe" required>
                                    <option value="1">是</option>
                                    <option value="0">否</option>
                                </select>
                                <label for="safe" class="form-label">异地登录保护【未登录设备需要邮箱验证】</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="user.uinfo.safe==='1'">
                        <div class="input-group">
                            <label class="input-group-text" for="safemail"><i class="bi " :class="{'bi-patch-check':user.uinfo.safemail==='0','bi-patch-check-fill':user.uinfo.safemail==='1'}"></i></label>
                            <div class="form-floating">
                                <select class="form-select" v-model="user.uinfo.safemail" id="safemail" required>
                                    <option value="1">是</option>
                                    <option value="0">否</option>
                                </select>
                                <label for="safemail" class="form-label">邮箱验证保护【验证设备与登录设备需要在同一IP下】</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <label class="input-group-text" for="private"><i class="bi " :class="{'bi-house-lock':user.uinfo.private==='0','bi-house-lock-fill':user.uinfo.private==='1'}"></i></label>
                            <div class="form-floating">
                                <select class="form-select" v-model="user.uinfo.private" id="private" required>
                                    <option value="1">保密</option>
                                    <option value="0">公开</option>
                                </select>
                                <label for="private" class="form-label">信息安全</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr class="my-4">
                <h4 class="mb-3">允许登录的IP</h4>
                <div class="row g-3">
                    <div class="col-12">
                        <div class="input-group justify-content-center">
                            <a @click="addip" class="btn btn-outline-info"><i class="bi bi-plus-lg"></i> 添加</a>
                            <a @click="clear" class="btn btn-outline-danger"><i class="bi bi-arrow-clockwise"></i> 清空</a>
                        </div>
                    </div>
                    <div class="col-12" v-for="(ip,index) in user.allowip">
                        <div class="input-group">
                            <label class="input-group-text" :for="'ip'+index">@{{ index+1 }}</label>
                            <input :id="'ip'+index" class="form-control" type="text" v-model="user.allowip[index]" placeholder="0.0.0.0"/>
                            <label v-if="ipstatus[index]" class="input-group-text" style="font-size:x-small" :for="'ip'+index">@{{ ipstatus[index] }}</label>
                            <a class="btn btn-outline-danger" @click="delip(index)"><i class="bi bi-x-lg"></i> 删除</a>
                        </div>
                    </div>
                </div>
                <hr class="my-4">
                <!--点击修改会调用service/user/alter-->
                <!--修改时的逻辑全在alter里-->
                <div class="row g-3">
                    <div class="col-12">
                        <div class="input-group">
                            <label class="input-group-text" for="upwd"><i class="bi bi-person-lock"></i></label>
                            <div class="form-floating">
                                <input type="password" class="form-control" v-model="upwd" id="upwd" placeholder="请填写您当前密码" required>
                                <label for="upwd" class="form-label">请填写您当前密码</label>
                            </div>
                            <button class="btn btn-outline-success btn-lg" type="button" @click="alter"><i class="bi bi-person-fill-gear"></i> 修改</button>
                        </div>
                    </div>
                </div>
            </form>
        </x-avatar>
    </div>
@endsection

@section('nextJS')
@endsection
