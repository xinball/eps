<!--main就是整个公告栏这一大块-->
<main id="noticelist" class="container list shadow">
<!--侧边栏-->
<x-offcanvas>
        <form>
            <div class="mb-3 col-12">
                <div class="input-group">
                    <select class="form-select" v-model="params.order">
                        <option v-for="(ordertype,index) in ordertypes" :key="index" :label="ordertype" :value="index">@{{ ordertype }}</option>
                    </select>
                    <button type="button" class="btn btn-outline-dark" @click="set('desc')" :title="params.desc==='0'?'正序【从早到晚】':'倒序【从晚到早】'"><i class="bi" :class="{'bi-sort-up-alt':params.desc==='0','bi-sort-up':params.desc==='1'}" ></i></button>
                    <button type="button" class="btn btn-outline-info" @click="reset">重置 <i class="bi bi-arrow-clockwise"></i></button>
                    <button type="button" class="btn btn-outline-success" data-bs-dismiss="offcanvas"   @click="getData(params)">查询 <i class="bi bi-search"></i></button>
                </div>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" class="form-control" v-model="params.ntitle" id="paramsntitle" placeholder="公告标题">
                <label for="paramsntitle">公告标题</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" class="form-control" v-model="params.ndes"  id="paramsndes" placeholder="公告描述">
                <label for="paramsndes">公告描述</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="datetime-local" class="form-control" v-model="params.nstart" id="paramsnstart" placeholder="开始时间范围">
                <label for="paramsnstart">开始时间范围</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="datetime-local" class="form-control" v-model="params.nend" id="paramsnend" placeholder="结束时间范围">
                <label for="paramsnend">结束时间范围</label>
            </div>
@if (isset($utype)&&$utype==="a")
@else
@endif
            <div class="mb-3 col-12 form-floating">
                <select class="form-select" v-model="params.type" id="paramstype">
                    <option v-for="(ntype,index) in ntypes" :key="index" :label="ntype.label" :value="index">@{{ ntype.label }}</option>
                    <option value="" label="所有公告类型">所有公告类型</option>
                </select>
                <label for="paramstype">公告类型</label>
            </div>
        </form>    
    </x-offcanvas>

    <!--添加公告按钮在admin/notice里边-->

    <!--下面是公告管理页面上方的类型选择按钮-->

@if (isset($utype)&&$utype==="a")
    <div class="input-group     justify-content-center">
        <button v-for="(dis,index) in adis" style="" @click="set('type',index)" class="btn" :class="[params.type===index?'btn-'+dis.btn:'btn-outline-'+dis.btn]" :disabled="data.num[index]===0">
            @{{ dis.label }} <span v-if="data.num[index]>0" :class="dis.num">@{{ data.num[index] }}</span>
        </button>
    </div>
@endif
    
    <!--有公告，length代表公告的条数-->
    <div v-if="notices.length>0">

        <!--点击信息后的弹出拟态框-->
        <x-modal id='info' title="公告信息">
            <div v-if="notice!==null" style="word-wrap:break-word;word-break:break-all;over-flow:hidden;">
                    标题：@{{ notice.ntitle }}<br>
                    描述：@{{ notice.ndes }}<br>
                    发布时间：@{{ notice.ntime }}<br>
                    @{{ notice.nupdate===notice.ntime?"":"更新时间："+notice.nupdate }}<br>
            </div>
        </x-modal>
        <!--将公告栏划分-->
        <div class="item thead-dark thead row">
             <!--如果是管理员页面则有1用来留给删除-->

@if (isset($utype)&&$utype==="a")
                
                <!--1留给全选，剩下11用来显示各项-->
                <div class="col-1" @click="checkall"><a class="btn btn-outline-dark"><i class="bi bi-check-lg"></i></a></div>
                <div class="col-11 text-center row">
@else
                <div class="col-12 text-center row">
@endif
                <!--col总共12，用来分大小-->
                    <div class="d-none d-sm-block col-sm-1">#</div>
                    <div class="col-6 col-md-5">标题</div>
                    <div class="col-4 col-md-3">描述</div>
                    <div class="col-2 col-sm-1">时间</div>
                    <div class="d-none d-md-block col-md-1">更新时间</div>
                    <div class="d-none d-md-block col-md-1">类型</div>
                </div>
        </div>

        <!--把公告显示出来-->
        <div class="row item list-group-item list-group-item-action " v-for="(notice,index) in notices" style="display: flex;" :class="{'active':check.includes(notice.nid),'delete':notice.ntype==='d'}"  :key="index" >
        <!--如果是管理员端则可以删除和恢复-->
@if (isset($utype)&&$utype==="a")
            <div class="col-1" >
                <!--这一单位的空间有两个用途，1.勾选 2.删除恢复-->
                <input type="checkbox" class="form-check-input"  :value="notice.nid" v-model="check" > 
                <a v-if="notice.ntype!=='d'" class="btn btn-outline-danger" @click="del(index)" style="font-size:xx-small;"><i class="bi bi-trash3-fill"></i></a>
                <a v-else class="btn btn-success" @click="recover(index)" style="font-size:xx-small;" ><i class="bi bi-arrow-repeat"></i></a>
            </div>
@endif
            <div
@if (isset($utype)&&$utype==="a")
                class="col-11 row text-center align-items-center" title="双击编辑公告信息" @dblclick="openalter(index)" 
@else
                class="col-12 row text-center align-items-center" title="双击查看公告信息" @dblclick="openinfo(index)"
@endif      >

<!--上方代码为如果是管理员则可以改变公告信息，如果是用户则只可查看-->
<!--把公告打印显示出来-->

                <div class="d-none d-sm-block col-sm-1 thead" >@{{ notice.nid }}</div>
                <div class="col-6 col-md-5"><a class="btn btn-light text-truncate" style="width:95%;" :title="notice.ntitle" :href="'/notice/'+notice.nid" >@{{ notice.ntitle }}</a></div>
                <div class="col-4 col-md-3 text-truncate" :title="notice.ndes">@{{ notice.ndes }}</div>
                <div class="col-2 col-sm-1" style="font-size:x-small;">@{{ time.ntime[index].status+(time.ntime[index].length!==null?"前":"") }}</div>
                <div class="d-none d-md-block col-md-1" style="font-size:x-small;">@{{ time.nupdate[index].status+(time.nupdate[index].length!==null?"前":"") }}</div>
                <div class="d-none d-md-block col-md-1"style="font-size:x-small;">
                    <select v-model="notice.ntype" class="badge noexpand" :class="['bg-'+ntypes[notice.ntype]['color']]" disabled>
                        <option v-for="(ntype,index) in ntypes" :key="index" :label="ntype.label" :value="index">@{{ ntype.label }}</option>
                    </select>
                </div>
            </div>
        </div>
        <!--分页-->
        @include('template.paginator')
    </div>
    <p v-if="notices.length==0">抱歉，查询不到任何公告！</p>

</main>

<script>
    const noticelist=Vue.createApp({
        data(){
            return{
                time:{ntime:[],nupdate:[]},
                dataname:"notices",
                paginator:{},
                pagenum:{{ $config_notice['pagenum'] }},
                data:{num:{sum:0}},
                notices:[],
                notice:null,
                check:[],
@if (isset($utype)&&$utype==="a")
                url:"{{ config('var.anl') }}",
                typekey:{!! json_encode($config_notice['typekey']['total']) !!},
@else
                url:"{{ config('var.nl') }}",
                typekey:{!! json_encode($config_notice['typekey']['all']) !!},
@endif
                ntypes:{!! json_encode($config_notice['type']) !!},
                adis:{!! json_encode($config_notice['adis'],JSON_UNESCAPED_UNICODE) !!},
                paramspre:{},
                params:{
                    page:"1",
                    ndes:"",
                    ntitle:"",
                    type:"",
                    nstart:"2023-01-01T00:00:00",
                    nend:"2023-12-31T00:00:00",
                    order:"ntime",
                    desc:"1",
                },
                ordertypes:{
                    ntime:"按发布时间",
                    nupdate:"按更新时间",
                    nid:"按序号",
                },
            }
        },
        mounted(){
            //过滤
            filterTypes(this.ntypes,this.typekey);
            //初始化页面，分页，设定几条数据分页
            initpaginator(this);
            this.getData();
        },
        computed:{
        },
        methods:{
            //初始化状态
            initStatus(){
                initStatus(this,"ntime");
                initStatus(this,"nupdate");
            },
            //打开公告
            openinfo(index){
                $('#info').modal("show");
                this.notice = Object.assign({},this.notices[index]);
            },
            openalter(index){
                alterapp.index=index;
                alterapp.nid=this.notices[index].nid;
                $('#alter').modal("show");
            },

            //得到最近更新的时间
            getLastTime(index){
                return getSubTime(getDate(this.notices[index].ntime).getTime(),(new Date()).getTime());
            },
            //重置数据
            reset(){
                this.params=this.paramspre={
                    page:"1",
                    ndes:"",
                    ntitle:"",
                    type:"",
                    nstart:"2023-01-01T00:00:00",
                    nend:"2023-12-31T00:00:00",
                    order:"ntime",
                    desc:"1",
                };
            },

            //删除
            del(index){
                let notice=this.notices[index];
                let that=this;
                getData('{!! config('var.and') !!}'+notice.nid,function(json){
                    if(json.status===1){
                        that.notices[index].ntype="d";
                    }
                },"#msg");
            },

            //恢复
            recover(index){
                let notice=this.notices[index];
                let that=this;
                getData('{!! config('var.anr') !!}'+notice.nid,function(json){
                    if(json.status===1){
                        that.notices[index].ntype="h";
                    }
                },"#msg");
            },

            //全选
            checkall(){
                let flag=true;
                for(notice of this.notices){
                    if(!this.check.includes(notice.nid)){
                        this.check.push(notice.nid);
                        flag=false;
                    }
                }
                if(flag===true){
                    this.check.length=0;
                }
            },
        }
    }).mount('#noticelist');
    //current_page,first_page_url,last_page,last_page_url,next_page_url,path,per_page,prev_page_url,from,to,total
</script>