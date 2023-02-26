<!--main就是整个公告栏这一大块-->
<main id="noticelist" class="container list shadow">
<!--侧边栏-->    
<x-offcanvas>
        <form>
            <div class="mb-3 col-12">
                <div class="input-group">
                    <select class="form-select" v-model="params.order">
                        <option v-for="(ordertype,index) in ordertypes" :key="index" :label="ordertype" :value="index">@{{ ordertype }}</option>
                        <option value="0" label="默认排序【序号倒序】">默认排序【序号倒序】</option>
                    </select>
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
                    <option value="0" label="未选择公告类型">未选择公告类型</option>
                </select>
                <label for="paramstype">公告类型</label>
            </div>
        </form>    
    </x-offcanvas>
    @if (isset($utype)&&$utype==="a")
    <div class="">
        <button v-for="(dis,index) in adis" style="margin-left:10px;" @click="settype(index)" class="btn" :class="[params.type===index?'btn-'+dis.btn:'btn-outline-'+dis.btn]" :disabled="data.num[index]===0">
            @{{ dis.label }} <span v-if="data.num[index]>0" :class="dis.num">@{{ data.num[index] }}</span>
        </button>
    </div>
    @endif
    
    <!--有公告，length代表公告的条数-->
    <div v-if="notices.length>0">

        <!--点击信息后的弹出拟态框-->
        <x-modal id='info' title="公告信息">
            <div v-if="notice!==null" class="modal-body" style="word-wrap:break-word;word-break:break-all;over-flow:hidden;">
                <div class='modal-body' style='text-align:left;'>
                    标题：@{{ notice.ntitle }}<br>
                    描述：@{{ notice.ndes }}<br>
                    发布时间：@{{ notice.ntime }}<br>
                    @{{ notice.nupdate===notice.ntime?"":"更新时间："+notice.nupdate }}<br>
                </div>
            </div>
        </x-modal>
        <!--将公告栏划分-->
        <div class="item thead-dark thead">
            <div class="row">
                <!--col总共12，用来分大小-->
                <div class="d-none d-sm-block col-sm-1">#</div>
                <div class="col-6 col-md-5">标题</div>
                <div class="col-4 col-md-3">描述</div>
                <div class="col-2 col-sm-1">时间</div>
                <div class="d-none d-md-block col-md-1">更新时间</div>
                <div class="d-none d-md-block col-md-1">类型</div>
            </div>
        </div>
        <div class="item text-center list-group-item-action" v-for="(notice,index) in notices" :key="index" data-bs-toggle="modal" :data-bs-index="index" 

@if (isset($utype)&&$utype==='a')
        title="点击编辑公告信息" data-bs-target="#alter" :data-bs-nid="notice.nid"
@else
        title="点击查看公告信息" data-bs-target="#info" @click="openinfo($event)"
@endif>

<!--把公告打印显示出来-->
            <div class="row">
                <div class="d-none d-sm-block col-sm-1 thead" >@{{ notice.nid }}</div>
                <div class="col-6 col-md-5"><a class="btn btn-light text-truncate" style="width:95%;" :title="notice.ntitle" :href="'/notice/'+notice.nid" >@{{ notice.ntitle }}</a></div>
                <div class="col-4 col-md-3 text-truncate" style="vertical-align: middle;align-self:center;" :title="notice.ndes">@{{ notice.ndes }}</div>
                <div class="col-2 col-sm-1" style="vertical-align: middle;align-self:center;font-size:x-small;">@{{ time.send[index].status+(time.send[index].length!==null?"前":"") }}</div>
                <div class="d-none d-md-block col-md-1" style="vertical-align: middle;align-self:center;font-size:x-small;">@{{ time.update[index].status+(time.update[index].length!==null?"前":"") }}</div>
                <div class="d-none d-md-block col-md-1"style="vertical-align: middle;align-self:center;font-size:x-small;">
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
                timer:null,
                time:{
                    before:7*86400000,
                    length:1000,
                    send:[],
                    update:[]
                },

                dataname:"notices",
                paginator:{},
                pagenum:{{ $config_notice['pagenum'] }},
                data:{num:{sum:0}},
                notices:[],
                notice:null,
                @if (isset($utype)&&$utype==="a")
                url:"{{ config('var.anl') }}",
                typekey:{!! json_encode($config_notice['typekey']['total']) !!},
                @else
                url:"{{ config('var.nl') }}",
                typekey:{!! json_encode($config_notice['typekey']['all']) !!},
                @endif
                ntypes:isJSON({!! json_encode($config_notice['type']) !!}),
                adis:isJSON({!! json_encode($config_notice['adis'],JSON_UNESCAPED_UNICODE) !!}),
                paramspre:{
                    page:"1",
                    ndes:"",
                    ntitle:"",
                    type:"0",
                    nstart:"2021-01-01T00:00:00",
                    nend:"2023-12-31T00:00:00",
                    order:"0",
                },
                params:{
                    page:"1",
                    ndes:"",
                    ntitle:"",
                    type:"0",
                    nstart:"",
                    nstart:"2021-01-01T00:00:00",
                    nend:"2023-12-31T00:00:00",
                    order:"0",
                },
                ordertypes:{
                    ntime:"按发布时间排序",
                    nupdate:"按更新时间排序",
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
            //初始化状态，显示在公告栏倒数第三栏
            initStatus(){
                for(i in this.notices){
                    let notice=this.notices[i];
                    this.time.send[i]={
                        //发布时间距今多久
                        length:(new Date()).getTime()-getDate(notice.ntime).getTime(),
                        status:""
                    };
                    this.getStatus(notice.ntime,this.time.send[i])
                    if(notice.ntime===notice.nupdate){
                        this.time.update[i]={
                            length:null,
                            status:"/"
                        };
                    }else{
                        this.time.update[i]={
                            //最近更新时间距现在多久
                            length:(new Date()).getTime()-getDate(notice.nupdate).getTime(),
                            status:""
                        };
                        //显示
                        this.getStatus(notice.nupdate,this.time.update[i])
                    }
                }
                clearInterval(this.timer);
                this.timer=setInterval(() => {
                    this.refreshStatus()
                }, 1000);
            },

            //超过before（7天）就显示日期
            //没超过7天就显示多少天多少小时前
            getStatus(date,send){
                if(send.length!==null){
                    if(send.length>this.time.before){
                        send.length=null;
                        send.status=date;
                    }else if(send.length>=0){
                        send.status=getSubTime(0,send.length);
                    }else{
                        send.length=null;
                        send.status="未发布";
                    }
                }
            },


            //刷新时间
            refreshStatus(){
                for(i in this.notices){
                    let notice=this.notices[i];
                    if(this.time.send[i].length!==null){
                        this.time.send[i].length+=this.time.length;
                        this.getStatus(notice.ntime,this.time.send[i])
                    }
                    if(notice.ntime!==notice.nupdate&&this.time.update[i].length!==null){
                        this.time.update[i].length+=this.time.length;
                        this.getStatus(notice.nupdate,this.time.update[i])
                    }

                }
            },
            openinfo(event){
                this.notice = Object.assign({},this.notices[event.currentTarget.getAttribute('data-bs-index')]);
            },

            //得到最近更新的时间
            getLastTime(index){
                return getSubTime(getDate(this.notices[index].ntime).getTime(),(new Date()).getTime());
            },
            //重置
            reset(){
                this.params=this.paramspre={
                    page:"1",
                    ndes:"",
                    ntitle:"",
                    type:"0",
                    nstart:"2021-01-01T00:00:00",
                    nend:"2023-12-31T00:00:00",
                    order:"0",
                };
            },
        }
    }).mount('#noticelist');
    //current_page,first_page_url,last_page,last_page_url,next_page_url,path,per_page,prev_page_url,from,to,total
</script>