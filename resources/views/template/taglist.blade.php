
<main id="taglist" class="container list shadow">
    @if(isset($utype)&&$utype==='a')
    <x-modal id="insertmodal" title="添加确认">
        您确定要添加该标签吗？
        <x-slot name="footer">
            <a @click="insert" data-bs-dismiss="modal" class="btn btn-outline-success">确定</a>
        </x-slot>
    </x-modal>
    <x-modal id="delmodal" title="删除确认！！！">
        您确定要删除该标签吗？【此操作不能恢复，请慎重操作】
        <x-slot name="footer">
            <a @click="del" data-bs-dismiss="modal" class="btn btn-outline-danger">确定</a>
        </x-slot>
    </x-modal>
    @endif
    <x-offcanvas>
        <form>
            <div class="mb-3 col-12">
                <div class="input-group">
                    <select class="form-select" v-model="params.order">
                        <option v-for="(ordertype,index) in ordertypes" :key="index" :label="ordertype" :value="index">@{{ ordertype }}</option>
                        <option value="0" label="默认排序【序号倒序】">默认排序【序号倒序】</option>
                    </select>
                    <button type="button" class="btn btn-outline-info" @click="reset">重置 <i class="bi bi-arrow-clockwise"></i></button>
                    <button type="button" class="btn btn-outline-success" data-bs-dismiss="offcanvas"   @click="getData">查询 <i class="bi bi-search"></i></button>
                </div>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" class="form-control" v-model="params.tname" id="paramstname" placeholder="标签标题">
                <label for="paramstname">标签名称</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" class="form-control" v-model="params.tdes"  id="paramstdes" placeholder="标签描述">
                <label for="paramstdes">标签描述</label>
            </div>
        </form>    
    </x-offcanvas>
    <div v-if="tags.length>0">
        <div class="item thead-dark thead">
            <div class="row">
                <div class="col-1">#</div>
                <div class="col-2">标签名称</div>
                <div class="col-4">标签描述</div>
                <div class="col-1">问题数量</div>
                <div class="col-1">收藏数量</div>
                <div class="col-3">操作</div>
            </div>
        </div>
        <div class="item text-center list-group-item-action" v-for="(tag,index) in tags" :key="index">
            <div class="row text-center" >
                <div class="col-1 thead" >@{{ 'tid' in tag?tag.tid:"?" }}</div>
                @if (isset($utype)&&$utype==='a')
                <div class="col-2" style="align-self: center;">
                    <div class="form-floating">
                        <input type="text" class="form-control" :id="'tname'+index" placeholder="输入标签名称" v-model="tag.tname">
                        <label :for="'tname'+index">输入标签名称</label>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-floating">
                        <textarea class="form-control" v-model="tag.tdes" cols="5" rows="3" :id="'tdes'+index" placeholder="输入标签详细描述" ></textarea>
                        <label :for="'tdes'+index">输入标签详细描述</label>
                    </div>
                </div>
                <div class="col-1 text-center" style="align-self: center;text-align:center;">@{{ 'tnum' in tag?tag.tnum:0 }}</div>
                <div class="col-1 text-center" style="align-self: center;text-align:center;">@{{ 'tlnum' in tag?tag.tlnum:0 }}</div>
                <div class="col-3" style="text-align: center;align-self: center;">
                    <div class="input-group" style="display: block;">
                        <a v-if="'tid' in tag" class="btn btn-outline-success" @click="alert(index)">修改</a>
                        <a v-if="'tid' in tag" class="btn btn-outline-danger" @click="isdel(index)">删除</a>
                        <a v-if="!('tid' in tag)" class="btn btn-outline-success" @click="isinsert(index)">添加</a>
                        <a v-if="!('tid' in tag)" class="btn btn-outline-danger" @click="deltag(index)">删除</a>
                    </div>
                </div>
                @else
                <div class="col-2" style="align-self: center;">@{{ tag.tname }}</div>
                <div class="col-4" style="align-self: center;">@{{ tag.tdes }}</div>
                <div class="col-1 text-center" style="align-self: center;text-align:center;">@{{ 'tnum' in tag?tag.tnum:0 }}</div>
                <div class="col-1 text-center" style="align-self: center;text-align:center;">@{{ 'tlnum' in tag?tag.tlnum:0 }}</div>
                <div class="col-3 txet-center" style="text-align: center;align-self: center;">
                    <div class="input-group text-center" style="display:block;">
                        <a v-if="tag.like===false" class="btn btn-outline-danger" @click="like(index)"><i class="bi bi-heart"></i> 收藏</a>
                        <a v-if="tag.like===true" class="btn btn-danger" @click="dellike(index)"><i class="bi bi-heart"></i> 取消收藏</a>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @if(isset($utype)&&$utype==='a')
        <a id="insert" @click="inserttag" class="btn btn-fill btn-outline-info">添加标签</a>
        @endif
    </div>
    <p v-if="tags.length===0">抱歉，查询不到任何标签！</p>

</main>

<script>
    const taglist=Vue.createApp({
        data(){
            return{
                tags:json.tags,
                index:"",
                params:{
                    tdes:"",
                    tname:"",
                    order:"0",
                },
                ordertypes:{
                    tnum:"按标签绑定问题数量排序",
                    tlnum:"按标签收藏数量排序",
                },
            }
        },
        mounted(){
            setParams(this.params);
            this.getData();
        },
        methods:{
            getData(){
                let that=this;
                getData('{!! config('var.tl') !!}'+'?'+json2url(this.params),function(json){
                    if(json.tags!==null){
                        that.tags=json.tags;
                    }else{
                        that.tags=[];
                    }
                },"#msg");
            },
            reset(){
                this.params=this.paramspre={
                    tdes:"",
                    tname:"",
                    order:"0",
                };
            },
    @if(isset($utype)&&$utype==='a')
            inserttag(){
                this.tags.push({tname:"",tdes:""});
            },
            deltag(index){
                this.tags.splice(index,1);
            },
            isinsert(index){
                this.index=index;
                (new bootstrap.Modal(document.getElementById('insertmodal'))).show();
            },
            isdel(index){
                this.index=index;
                (new bootstrap.Modal(document.getElementById('delmodal'))).show();
            },
            insert(){
                let data=Object.assign({},this.tags[this.index]);
                data._token="{{csrf_token()}}";
                let that=this;
                getData('{!! config('var.ati') !!}',function(json){
                    if(json.status===1){
                        that.tags[that.index].tid=json.tid;
                    }
                },"#msg",data);
            },
            del(){
                let that=this;
                getData('{!! config('var.atd') !!}'+that.tags[that.index].tid,function(json){
                    if(json.status===1){
                        that.tags.splice(that.index,1);
                    }
                },"#msg");
            },
            alert(index){
                let data=Object.assign({},this.tags[index]);
                data._token="{{csrf_token()}}";
                getData('{!! config('var.ata') !!}'+this.tags[index].tid,null,"#msg",data);
            },
    @endif
            like(index){
                let that=this;
                getData('{!! config('var.til') !!}'+that.tags[index].tid,function(json){
                    if(json.status===1){
                        that.tags[index].like=true;
                        that.tags[index].tlnum=parseInt(that.tags[index].tlnum)+1;
                    }
                },"#msg");
            },
            dellike(index){
                let that=this;
                getData('{!! config('var.tdl') !!}'+that.tags[index].tid,function(json){
                    if(json.status===1){
                        that.tags[index].like=false;
                        that.tags[index].tlnum=parseInt(that.tags[index].tlnum)-1;
                    }
                },"#msg");
            },
        }
    }).mount('#taglist');
    //current_page,first_page_url,last_page,last_page_url,next_page_url,path,per_page,prev_page_url,from,to,total
</script>