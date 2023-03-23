<main id="userlist" class="container list shadow">

    <!--用户筛选的侧边框-->
    <x-offcanvas>
        <form>
            <div class="mb-3 col-12">
                <div class="input-group">
                    <select class="form-select" v-model="params.order">
                        <option v-for="(ordertype,index) in ordertypes" :key="index" :label="ordertype" :value="index">@{{ ordertype }}</option>
                        <option value="0" label="未选择排序方式" disabled="disabled">未选择排序方式</option>
                    </select>
                    <button type="button" class="btn btn-outline-info" @click="reset">重置 <i class="bi bi-arrow-clockwise"></i></button>
                    <button type="button" class="btn btn-outline-success" data-bs-dismiss="offcanvas"   @click="getData(params)">查询 <i class="bi bi-search"></i></button>
                </div>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="number" class="form-control" v-model="params.uid" id="paramsuid" placeholder="用户编号">
                <label for="paramsuid">UID</label>
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
                    <option v-for="(utype,index) in utypes" :key="index" :label="utype" :value="index">@{{ utype }}</option>
                    <option value="0" label="未选择用户类型" disabled="disabled">未选择用户类型</option>
                </select>
                <label for="paramstype">用户类型</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" class="form-control" v-model="params.nickname"  id="paramsnickname" placeholder="昵称">
                <label for="paramsnickname">昵称</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="number" class="form-control" v-model="params.sn"  id="paramssn" placeholder="学号">
                <label for="paramssn">学号</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" class="form-control" v-model="params.major"  id="paramsmajor" placeholder="专业">
                <label for="paramsmajor">专业</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <select class="form-select" v-model="params.sex" id="paramssex">
                    <option value="0">女</option>
                    <option value="1">男</option>
                    <option value="2">保密</option>
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
    @if (isset($utype)&&$utype==="a")
    {{-- <div class="">
        <button v-for="(dis,index) in adis" style="margin-left:10px;" @click="settype(index)" class="btn" :class="[params.type===index?'btn-'+dis.btn:'btn-outline-'+dis.btn]" :disabled="data.num[index]===0">
            @{{ dis.label }} <span v-if="data.num[index]>0" :class="dis.num">@{{ data.num[index] }}</span>
        </button>
    </div> --}}
    @endif

    <!--用户板块显示-->
    <div v-if="users.length>0">
        <div class="item thead-dark thead">
            <div class="row">
                <div class="d-none d-sm-block col-1">#</div>
                <div class="col-2 col-sm-1">头像</div>
                <div class="col-4 col-md-3">用户名</div>
                <div class="col-3 col-md-2">邮箱</div>
                <div class="col-3" @click="settype('')">类型</div>
                <div class="d-none d-md-block col-2">注册时间</div>
            </div>
        </div>
        <!--显示出来-->
        <div class="item text-center list-group-item-action" v-for="(user,index) in users" :key="index" data-bs-toggle="modal" :data-bs-index="index" title="点击编辑用户信息" data-bs-target="#alter" :data-bs-nid="user.uid">
            <div class="row">
                <div class="d-none d-sm-block col-sm-1 thead" >@{{ user.uid }}</div>
                <div class="col-2 col-sm-1"><img class="rounded-pill" style="width: 100%;max-width:32px;" :src="user.avatar" alt=""> </div>
                <div class="col-4 col-md-3 text-truncate" style="vertical-align: middle;align-self:center;" :title="user.uname">@{{ user.uname }}</div>
                <div class="col-3 col-md-2 text-truncate" style="vertical-align: middle;align-self:center;" :title="user.uemail">@{{ user.uemail }}</div>
                <div @click="settype(user.utype)" class="col-3 text-truncate" style="vertical-align: middle;align-self:center;" >
                    <select v-model="user.utype" style="width: 100%;font-size:x-small;" class="badge noexpand" :class="['bg-'+adis[user.utype].btn]" disabled>
                        <option v-for="(utype,index) in utypes" :key="index" :label="utype" :value="index">@{{ utype }}</option>
                    </select>
                </div>
                <div class="d-none d-md-block col-2" style="vertical-align: middle;align-self:center;font-size:x-small;">@{{ user.utime }}</div>
            </div>
        </div>
        <!--分页-->


        @include('template.paginator')
    </div>
    <p v-if="users.length===0">抱歉，查询不到任何用户！</p>

</main>

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
                utypes:isJSON({!! json_encode($config_user['type']) !!}),
                adis:isJSON({!! json_encode($config_user['adis'],JSON_UNESCAPED_UNICODE) !!}),
                paramspre:{
                    page:"1",
                    uname:"",
                    uemail:"",
                    type:"0",
                    ustart:"2021-01-01T00:00:00",
                    uend:"2023-12-31T00:00:00",
                    nickname:"",
                    sn:"",
                    major:"",
                    tel:"",
                    qq:"",
                    wid:"",
                    sex:"",
                    order:"0",
                },
                params:{
                    page:"1",
                    uname:"",
                    uemail:"",
                    type:"0",
                    ustart:"2021-01-01T00:00:00",
                    uend:"2023-12-31T00:00:00",
                    nickname:"",
                    sn:"",
                    major:"",
                    tel:"",
                    qq:"",
                    wid:"",
                    sex:"",
                    order:"0",
                },
                ordertypes:{
                    uname:"按用户名排序",
                    uid:"按UID排序",
                    uemail:"按邮箱排序",
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
                    type:"0",
                    ustart:"2021-01-01T00:00:00",
                    uend:"2023-12-31T00:00:00",
                    nickname:"",
                    sn:"",
                    major:"",
                    tel:"",
                    qq:"",
                    wid:"",
                    sex:"",
                    order:"0",
                };
            },
        }
    }).mount('#userlist');
    //current_page,first_page_url,last_page,last_page_url,next_page_url,path,per_page,prev_page_url,from,to,total
</script>