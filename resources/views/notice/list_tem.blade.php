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

    <!--添加公告按钮在admin/notice里边-->

    <!--下面是公告管理页面上方的类型选择按钮-->

@if (isset($utype)&&$utype==="a")
    <div class="input-group     justify-content-center">
        <button v-for="(dis,index) in adis" style="" @click="settype(index)" class="btn" :class="[params.type===index?'btn-'+dis.btn:'btn-outline-'+dis.btn]" :disabled="data.num[index]===0">
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
        <div class="item thead-dark thead">
            <div class="row">
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
            <div data-bs-toggle="modal" :data-bs-index="index" 
@if (isset($utype)&&$utype==="a")
class="row text-center col-11" title="点击编辑公告信息" data-bs-target="#alter" :data-bs-nid="notice.nid"
@else
class="row text-center col-12" title="点击查看公告信息" data-bs-target="#info" @click="openinfo($event)"
@endif>

<!--上方代码为如果是管理员则可以改变公告信息，如果是用户则只可查看-->
<!--把公告打印显示出来-->

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
                    //ntime为创建公告时间
                    //send为发布到现在过了多久
                    //判断公告的状态
                    this.getStatus(notice.ntime,this.time.send[i])
                    //nupdate为修改公告时间
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
                        //显示，多少天前修改
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
                    //大于7天
                    if(send.length>this.time.before){
                        send.length=null;
                        send.status=date;
                        //小于7天显示时间
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
                    //更新过的公告
                    if(notice.ntime!==notice.nupdate&&this.time.update[i].length!==null){
                        this.time.update[i].length+=this.time.length;
                        this.getStatus(notice.nupdate,this.time.update[i])
                    }

                }
            },

            //打开公告
            openinfo(event){
                this.notice = Object.assign({},this.notices[event.currentTarget.getAttribute('data-bs-index')]);
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
                    type:"0",
                    nstart:"2021-01-01T00:00:00",
                    nend:"2023-12-31T00:00:00",
                    order:"0",
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