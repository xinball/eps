<!--x-avatar继承了这个-->
<div id="alterapp">
    <!--个性签名修改拟态框-->
<x-modal title="@@{{ user.uname }} 的个性签名" id="sloganModal">
    <div class="modal-body">
        <textarea v-if="editable" id="avatarslogan" v-model="user.uinfo.slogan" placeholder="还没有个性签名哟~" class="form-control" rows="6" style="resize: none;"></textarea>
        <p v-if="!editable">@{{ user.uinfo.slogan==""?"还没有个性签名~":user.uinfo.slogan }}</p>
    </div>
    <!--footer表示固定位置-->
    <x-slot name="footer">
        <button v-if="editable" type="button" class="btn btn-outline-success" @click="alterslogan">修改</button>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">关闭</button>
    </x-slot>
</x-modal>

<!--个人信息修改拟态框，点击修改后跳转到setting页面-->
<x-modal title="@@{{ user.uname }} 的个人信息" id="unameModal">
    <div class="modal-body text-center row">
        <div class="col-12">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                <a class="btn btn-light form-control" :href="(user.uinfo.homepagessl==='0'?'http://':'https://')+user.uinfo.homepage" target="_blank">@{{ user.uinfo.homepage }}</a>
            </div>
        </div>
        <div class="col-12">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-calendar2-plus"></i></span>
                <input type="datetime-local" class="form-control" v-model="user.utime" disabled/>
            </div>
        </div>
        <div v-if="privatedis" class="col-12">
            <div class="input-group">
                <span class="input-group-text"><i class="bi" :class="{'bi-x':user.uinfo.sex==='2','bi-gender-female':user.uinfo.sex==='0','bi-gender-male':user.uinfo.sex==='1'}"></i></span>
                <select class="form-select" v-model="user.uinfo.sex" disabled>
                    <option value="0">女</option>
                    <option value="1">男</option>
                    <option value="2">保密</option>
                </select>
            </div>
        </div>
        <div v-if="privatedis" class="col-12">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                <input type="text" class="form-control" v-model='user.uinfo.tel' placeholder="无" disabled/>
            </div>
        </div>
        <div v-if="privatedis" class="col-12">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-tencent-qq"></i></span>
                <input type="text" class="form-control" v-model='user.uinfo.qq' placeholder="无" disabled/>
            </div>
        </div>
        <div v-if="privatedis" class="col-12">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-wechat"></i></span>
                <input type="text" class="form-control" v-model='user.uinfo.wid' placeholder="无" disabled/>
            </div>
        </div>
    </div>
    <x-slot name="footer">
        <a v-if="editable" href="/user/" target="_blank" class="btn btn-outline-success btn-like">修改信息</a>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">关闭</button>
    </x-slot>
</x-modal>

<!--横幅-->
<!--这里是真的把横幅显示出来-->
<div class="crop-banner" id="crop-banner">
<!-- Current avatar -->

    <!--鼠标放上去会显示更换横幅四字-->
    <div v-if="editable" class="banner-view" style="cursor: pointer;" title="更换横幅">
        <img :src="user.banner" style="width: 100%;">
    </div>
    <!-- Cropping modal -->
    <!--更换横幅拟态框-->
    <x-modal id="banner-modal" title="更换横幅" class="modal-fullscreen">
        <!--调用uub，上传横幅-->
        <form id="uploadbanner" class="avatar-form" action="{{ config('var.uub') }}" enctype="multipart/form-data" method="post">
            {{ csrf_field() }}
            <div class="avatar-body">
                <!-- Upload image and data -->
                <!--上方的字，上传横幅-->
                <div class="avatar-upload">
                    <input class="avatar-src" name="avatar_src" type="hidden">
                    <input class="avatar-data" name="avatar_data" type="hidden">
                    <label for="avatarInput">本地上传</label>
                    <!--选择文件-->
                    <input class="avatar-input" name="avatar_file" type="file">
                </div>

                <!-- Crop and preview -->
                <!--右边的三个小图片-->
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

                <!--footer为固定位置的部分-->
                <x-slot name="footer">
                    <div class="row">
                        <div class="input-group">
                            <input class="avatar-btns" type="range" min="-180" max="180" data-method="rotate" data-option="45" value="0" class="form-range" class="" id="banner-btns">
                            <button data-method="rotate" value="0" class="btn btn-outline-info avatar-btns" type="submit">重置</button>
                            <button form="uploadavatar" class="btn btn-outline-success btn-block avatar-save" type="submit">保存</button>
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


<!--头像-->
<div class="crop-avatar" id="crop-avatar" style="margin-top: -70px;padding: 0;">
    <div style="display: flex;background: rgba(0,0,0,0.44);padding: 3px;">
    <!-- Current avatar -->
        <!--鼠标放上去显示更换头像字样-->
        <div v-if="editable" class="avatar-view" style="cursor: pointer;" :class="{ 'avatar-admin':user.utype==='s' , 'avatar-user':user.utype!=='s' }"  title="更换头像">
            <img :src="user.avatar" alt="头像">
        </div>
        <!--更换头像拟态框-->
        <x-modal id="avatar-modal" title="更换头像" class="modal-fullscreen">
            <!--调用uua，上传头像-->
            <form id="uploadavatar" class="avatar-form" action="{{ config('var.uua') }}" enctype="multipart/form-data" method="post">
                {{ csrf_field() }}
                <div class="avatar-body">

                    <!-- Upload image and data -->
                    <!--上传头像-->
                    <div class="avatar-upload">
                        <input class="avatar-src" name="avatar_src" type="hidden">
                        <input class="avatar-data" name="avatar_data" type="hidden">
                        <label for="avatarInput">本地上传</label>
                        <input class="avatar-input" name="avatar_file" type="file">
                    </div>

                    <!-- Crop and preview -->
                    <!--右边的三个大小不同的图-->
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

                    <!--footer为不变-->
                    <x-slot name="footer">
                        <div class="row">
                            <div class="input-group">
                                <input class="avatar-btns" type="range" min="-180" max="180" data-method="rotate" data-option="45" value="0" class="form-range" class="" id="avatar-btns">
                                <button data-method="rotate" value="0" class="btn btn-outline-info avatar-btns" type="submit">重置</button>
                                <button form="uploadavatar" class="btn btn-outline-success btn-block avatar-save" type="submit">保存</button>
                            </div>
                        </div>
                    </x-slot>
                </div>
            </form>
        </x-modal>

        <!--把用户头像显示出来-->
        <div v-if="!editable" class="avatar-view" :class="{ 'avatar-admin':user.utype==='s' , 'avatar-user':user.utype!=='s' }" >
            <img :src="user.avatar" alt="用户头像">
        </div>
        <!--把用户姓名，个性签名显示出来-->
        <div style="padding: 0 0 0 10px;text-align: left;display: flex;flex-direction: column;width: 75%;">
            <h1 style="cursor: pointer;font-size:32px;margin-bottom: 0;color: white;" data-bs-toggle="modal" data-bs-target="#unameModal">@ @{{ user.uname }}</h1>
            <h5 style="cursor: pointer;margin: 0;" data-bs-toggle="modal" data-bs-target="#sloganModal"><small class="d-inline-block text-truncate" style="width: 75%;font-size: 12px;line-height:12px;color: #dddddd;" id="slogannow"><i class="bi bi-vector-pen"></i> @{{ user.uinfo.slogan==""?"还没有个性签名哟~":user.uinfo.slogan }}</small></h5>
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
                    con_id:"",
                    coun_id:"",
                    state_id:"",
                    city_id:"",
                    region_id:"",
                    allowip:[],
                    uinfo:{
                        addr:"",
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
                        save:'false',
                        savemail:'false',
                    },
                },
                ipstatus:[],
                
                uidtypes:{!! json_encode($config_user['idnotype'],JSON_UNESCAPED_UNICODE) !!},
                con_ids:[],
                coun_ids:[],
                state_ids:[],
                city_ids:[],
                region_ids:[],
                adis:{!! json_encode($config_user['adis'],JSON_UNESCAPED_UNICODE) !!},
                privatedis:true,
                editable:false,

                upwd:"",
                upwd1:"",
                upwd2:"",
            }
        },
        methods:{
            init(){
                //初始化，上边先创建一个新的用户对象，如果原来这个用户有数据，就先载入进来
                if(json.data!==null){
                    this.user=json.data.user;
                    document.title+=this.user.uname;
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
                    initaddress(this,"{!! config('var.sla') !!}",this.user,1);
                    const that = this;
                    for(let i in this.user.allowip){
                        this.ipstatus.push("");
                        const ip = this.user.allowip[i];
                        getData("{!! config('var.sgo') !!}"+"?ostart="+toDate(new Date().getTime()-86400000*7)+"&uid="+this.user.uid+"&oip="+ip,function(json){
                            if(json.data&&json.data.operations&&json.data.operations.data&&json.data.operations.data.length>0){
                                that.ipstatus[i] = "七天内共 "+json.data.operations.data.length+" 条操作记录 最近一次在 "+json.data.operations.data[0].otime;
                            }else{
                                that.ipstatus[i] = "最近七天无操作记录";
                            }
                        },"",null);
                    }
                }
            },
            alter:function (){
                //更改，在原有数据载入进来的基础上，把输入的新的需要更改的内容进行修改
                let data={
                    upwd:this.upwd,
                    upwd1:this.upwd1,
                    upwd2:this.upwd2,

                    con_id:this.user.con_id,
                    coun_id:this.user.coun_id,
                    state_id:this.user.state_id,
                    city_id:this.user.city_id,
                    region_id:this.user.region_id,
                    addr:this.user.uinfo.addr,
                    lang:this.user.uinfo.lang,
                    private:this.user.uinfo.private,
                    sex:this.user.uinfo.sex,
                    tel:this.user.uinfo.tel,
                    slogan:this.user.uinfo.slogan,
                    homepage:this.user.uinfo.homepage,
                    homepagessl:this.user.uinfo.homepagessl,
                    qq:this.user.uinfo.qq,
                    wid:this.user.uinfo.wid,
                    safe:this.user.uinfo.safe,
                    safemail:this.user.uinfo.safemail,
                    allowip:JSON.stringify(this.user.allowip),
                    _token:"{{csrf_token()}}"
                };

                //调用service/user/alter
                getData("{!! config('var.ual') !!}",null,"#msg",data);
            },
            alterslogan:function (){
                let data={
                    slogan:this.user.uinfo.slogan,
                    _token:"{{csrf_token()}}"
                }

                //调用/service/user/alterslogan
                getData("{!! config('var.uals') !!}",null,"#msg",data);
            },
            clear(){
                this.user.allowip.length=0;
            },
            delip(index){
                this.user.allowip.splice(index,1);
                this.ipstatus.splice(index,1);
            },
            addip(index){
                this.user.allowip.push("");
            },
        },
        mounted(){
            this.init();
        },
    }).mount("#alterapp");


</script>
