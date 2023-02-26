<div id="alterapp">
<x-modal title="@@{{ user.uname }} 的个性签名" id="sloganModal">
    <div class="modal-body">
        <textarea v-if="editable" id="avatarslogan" v-model="user.uinfo.slogan" placeholder="还没有个性签名哟~" class="form-control" rows="6" style="resize: none;"></textarea>
        <p v-if="!editable">@{{ user.uinfo.slogan==""?"还没有个性签名哟~":user.uinfo.slogan }}</p>
    </div>
    <x-slot name="footer">
        <button v-if="editable" type="button" class="btn btn-outline-success" @click="alterslogan">修改</button>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">关闭</button>
    </x-slot>
</x-modal>
<x-modal title="@@{{ user.uname }} 的个人信息" id="unameModal">
    <div class="modal-body text-center">
        <p>邮箱：@{{ user.uemail }}</p>
        <p>主页：<a :href="(user.uinfo.homepagessl==='0'?'http://':'https://')+user.uinfo.homepage" target="_blank">@{{ user.uinfo.homepage }}</a></p>
        <p>注册时间：@{{ user.utime }}</p>
    </div>
    <div v-if="privatedis" class="modal-body text-center">
        <p>性别：@{{ user.uinfo.sex=="0"?"女":(user.uinfo.sex=="1"?"男":"未知") }}</p>
        <p>手机：@{{ user.uinfo.tel ?? "未知" }}</p>
        <p>QQ：@{{ user.uinfo.qq ?? "未知" }}</p>
        <p>微信：@{{ user.uinfo.wid ?? "未知" }}</p>
    </div>
    <x-slot name="footer">
        <a v-if="editable" href="/user/" target="_blank" class="btn btn-outline-success btn-like">修改信息</a>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">关闭</button>
    </x-slot>
</x-modal>

<div class="crop-banner" id="crop-banner">
<!-- Current avatar -->
    <div v-if="editable" class="banner-view" style="cursor: pointer;" title="更换横幅">
        <img :src="user.banner" style="width: 100%;">
    </div>
    <!-- Cropping modal -->
    <x-modal id="banner-modal" title="更换横幅" class="modal-fullscreen">
        <form id="uploadbanner" class="avatar-form" action="{{ config('var.uub') }}" enctype="multipart/form-data" method="post">
            {{ csrf_field() }}
            <div class="avatar-body">
                <!-- Upload image and data -->
                <div class="avatar-upload">
                    <input class="avatar-src" name="avatar_src" type="hidden">
                    <input class="avatar-data" name="avatar_data" type="hidden">
                    <label for="avatarInput">本地上传</label>
                    <input class="avatar-input" id="avatarInput" name="avatar_file" type="file">
                </div>

                <!-- Crop and preview -->
                <div class="row">
                    <div class="col-md-9">
                        <div class="avatar-wrapper"></div>
                    </div>
                    <div class="col-md-3">
                        <div class="avatar-preview preview-banner-lg"></div>
                        <div class="avatar-preview preview-banner-md"></div>
                        <div class="avatar-preview preview-banner-sm"></div>
                    </div>
                </div>

                <x-slot name="footer">
                    <div class="row avatar-btns">
                        <div class="col-md-9">
                            <div class="btn-group">
                                <button class="btn btn-outline-danger btn-like" data-method="rotate" data-option="-90" type="button" title="Rotate -90 degrees">-90°</button>
                                <button class="btn btn-outline-danger btn-like" data-method="rotate" data-option="-15" type="button">-15°</button>
                                <button class="btn btn-outline-danger btn-like" data-method="rotate" data-option="-30" type="button">-30°</button>
                                <button class="btn btn-outline-danger btn-like" data-method="rotate" data-option="-45" type="button">-45°</button>
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-outline-info" data-method="rotate" data-option="90" type="button" title="Rotate 90 degrees">+90°</button>
                                <button class="btn btn-outline-info" data-method="rotate" data-option="15" type="button">15°</button>
                                <button class="btn btn-outline-info" data-method="rotate" data-option="30" type="button">30°</button>
                                <button class="btn btn-outline-info" data-method="rotate" data-option="45" type="button">45°</button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button form="uploadbanner" class="btn btn-outline-success btn-block avatar-save" type="submit">保存</button>
                        </div>
                    </div>
                </x-slot>
            </div>
        </form>
        <!-- <div class="modal-footer">
          <button class="btn btn-default" data-dismiss="modal" type="button">Close</button>
        </div> -->
    </x-modal>

    <!-- Loading state -->
    <div class="loading" aria-label="Loading" role="img" tabindex="-1"></div>
    <div v-if="!editable" class="banner-view" >
        <img :src="user.banner" style="width: 100%;">
    </div>
</div>
<div class="crop-avatar" id="crop-avatar" style="margin-top: -70px;padding: 0;">
    <div style="display: flex;background: rgba(0,0,0,0.44);padding: 3px;">
    <!-- Current avatar -->
        <div v-if="editable" class="avatar-view" style="cursor: pointer;" :class="{ 'avatar-admin':user.utype==='s' , 'avatar-user':user.utype!=='s' }"  title="更换头像">
            <img :src="user.avatar" alt="头像">
        </div>
        <x-modal id="avatar-modal" title="更换头像" class="modal-fullscreen">
            <form id="uploadavatar" class="avatar-form" action="{{ config('var.uua') }}" enctype="multipart/form-data" method="post">
                {{ csrf_field() }}
                <div class="avatar-body">

                    <!-- Upload image and data -->
                    <div class="avatar-upload">
                        <input class="avatar-src" name="avatar_src" type="hidden">
                        <input class="avatar-data" name="avatar_data" type="hidden">
                        <label for="avatarInput">本地上传</label>
                        <input class="avatar-input" id="avatarInput" name="avatar_file" type="file">
                    </div>

                    <!-- Crop and preview -->
                    <div class="row">
                        <div class="col-md-9">
                            <div class="avatar-wrapper"></div>
                        </div>
                        <div class="col-md-3">
                            <div class="avatar-preview preview-avatar-lg"></div>
                            <div class="avatar-preview preview-avatar-md"></div>
                            <div class="avatar-preview preview-avatar-sm"></div>
                        </div>
                    </div>
                    <x-slot name="footer">
                        <div class="row avatar-btns">
                            <div class="col-md-9">
                                <div class="btn-group">
                                    <button class="btn btn-outline-danger btn-like" data-method="rotate" data-option="-90" type="button" title="Rotate -90 degrees">-90°</button>
                                    <button class="btn btn-outline-danger btn-like" data-method="rotate" data-option="-15" type="button">-15°</button>
                                    <button class="btn btn-outline-danger btn-like" data-method="rotate" data-option="-30" type="button">-30°</button>
                                    <button class="btn btn-outline-danger btn-like" data-method="rotate" data-option="-45" type="button">-45°</button>
                                </div>
                                <div class="btn-group">
                                    <button class="btn btn-outline-info" data-method="rotate" data-option="90" type="button" title="Rotate 90 degrees">+90°</button>
                                    <button class="btn btn-outline-info" data-method="rotate" data-option="15" type="button">15°</button>
                                    <button class="btn btn-outline-info" data-method="rotate" data-option="30" type="button">30°</button>
                                    <button class="btn btn-outline-info" data-method="rotate" data-option="45" type="button">45°</button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button form="uploadavatar" class="btn btn-outline-success btn-block avatar-save" type="submit">保存</button>
                            </div>
                        </div>
                    </x-slot>
                </div>
            </form>
        </x-modal>

        <div v-if="!editable" class="avatar-view" :class="{ 'avatar-admin':user.utype==='s' , 'avatar-user':user.utype!=='s' }" >
            <img :src="user.avatar" alt="用户头像">
        </div>
        <div style="padding: 0 0 0 10px;text-align: left;display: flex;flex-direction: column;width: 75%;">
            <h1 style="cursor: pointer;font-size:32px;margin-bottom: 0;color: white;" data-bs-toggle="modal" data-bs-target="#unameModal">@{{ user.uname }}</h1>
            <h5 style="cursor: pointer;margin: 0;" data-bs-toggle="modal" data-bs-target="#sloganModal"><small class="d-inline-block text-truncate" style="width: 75%;font-size: 12px;line-height:12px;color: #dddddd;" id="slogannow">@{{ user.uinfo.slogan==""?"还没有个性签名哟~":user.uinfo.slogan }}</small></h5>
        </div>
    </div>

</div>
    {{ $slot }}
</div>

<script>
    const alterapp=Vue.createApp({
        data() {
            return {
                user:{
                    uname:"",
                    uidno:"",
                    uidtype:"",
                    banner:"",
                    avatar:"",
                    uemail:"",
                    utype:"",
                    uid:"",
                    utime:"",
                    uinfo:{
                        lang:"cn",
                        private:"0",
                        sex:"2",
                        tel:"",
                        slogan:"",
                        homepage:"",
                        qq:"",
                        wid:"",
                        reg_ip:"",
                        homepagessl:'0',
                    },
                },
                utypes:isJSON({!! json_encode($config_user['type']) !!}),
                
                uidtypes:{
                    "0":"中国居民身份证",
                    "1":"港澳台居民居住证",
                    "2":"港澳居民来往内地通行证",
                    "3":"台湾居民来往大陆通行证",
                    "4":"护照",
                    "5":"外国人永久居留身份证"
                },
                privatedis:true,
                editable:false,

                upwd:"",
                upwd1:"",
                upwd2:"",
            }
        },
        methods:{
            init(){
                if(json.data!==null){
                    this.user=json.data.user;
                    document.title+=this.user.uname;
                    this.user.uinfo.homepagessl=0;
                    this.user.uinfo.homepage=this.user.uinfo.homepage===""?'eps.yono.top/user/'+this.user.uid:this.user.uinfo.homepage;
                    this.user.utime=this.user.utime.replace(" ","T");
                    @if(isset($luser)&&$luser!==null)
                    this.editable=this.user.uid==={!! $luser->uid !!};
                    @endif
                    if(!this.editable){
                        this.privatedis=this.editable||(!'private' in this.user.uinfo||this.user.uinfo.private==='0');
                        $('#avatar-modal').attr('id','1');
                        $('#banner-modal').attr('id','2');
                    }
                }
            },
            alter:function (){
                let data={
                    upwd:this.upwd,
                    upwd1:this.upwd1,
                    upwd2:this.upwd2,

                    lang:this.user.uinfo.lang,
                    private:this.user.uinfo.private,
                    sex:this.user.uinfo.sex,
                    tel:this.user.uinfo.tel,
                    slogan:this.user.uinfo.slogan,
                    homepage:this.user.uinfo.homepage,
                    homepagessl:this.user.uinfo.homepagessl,
                    qq:this.user.uinfo.qq,
                    wid:this.user.uinfo.wid,
                    _token:"{{csrf_token()}}"
                };
                getData("{!! config('var.ual') !!}",null,"#msg",data);
            },
            alterslogan:function (){
                let data={
                    slogan:this.user.uinfo.slogan,
                    _token:"{{csrf_token()}}"
                }
                getData("{!! config('var.uals') !!}",null,"#msg",data);
            }
        },
        mounted(){
            this.init();
        },
    }).mount("#alterapp");


</script>
