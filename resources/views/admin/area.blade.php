@extends('admin.master')

@section('title','区域管理')
@section('nextCSS2')

@endsection
@section('main')

<main id="arealist" class="container list shadow">
    <x-offcanvas>
        <form>
        </form>    
    </x-offcanvas>
    <h3>【省市区】区域管理员</h3>
    <div v-if="state.length>0">
        
    <div class="accordion" id="state">
        <div class="accordion-item" v-for="(item,index) in state">
            <h2 class="accordion-header input-group">
                <button class="accordion-button collapsed" style="width:90%" type="button" data-bs-toggle="collapse" :data-bs-target="'#state'+i" aria-expanded="true" aria-controls="'state'+i">
                @{{index}}
                </button>
                <button class="btn btn-outline-dark" @click="addstate(index)" style="width:10%"><i class="bi bi-plus-lg"></i></button>
            </h2>
                
            <div :id="'state'+i" class="accordion-collapse collapse">
                <div class="input-group p-1">
                    <button class="btn btn-primary">#@{{index}}</button>

                </div>
            </div>
        </div>
    </div>



        <a id="insert" @click="inserttag" class="btn btn-fill btn-outline-info">添加标签</a>
    </div>
    <p v-if="state.length===0">抱歉，查询不到任何【省市区】区域管理员！</p>

</main>

<script>
    const arealist=Vue.createApp({
        data(){
            return{
                region:[],
                city:[],
                state:[],
                params:{
                    uid:"",
                    state_id:"",
                    city_id:"",
                    region_id:"",
                },
            }
        },
        mounted(){
            this.getData();
        },
        methods:{
            getData(){
                const that=this;
                getData("{!! config('var.aag') !!}"+'?'+json2url(this.params),function(json){
                    if(json.status===1&&json.data!==null){
                        that.state=json.data.state;
                        that.city=json.data.city;
                        that.region=json.data.region;
                    }else{
                        that.region=that.city=that.state=[];
                    }
                },"#msg");
            },
            reset(){
                this.params=this.paramspre={
                    uid:"",
                    state_id:"",
                    city_id:"",
                    region_id:"",
                };
            },
            addstate(index){

            },
    // @if(isset($utype)&&$utype==='a')
    //         inserttag(){
    //             this.tags.push({tname:"",tdes:""});
    //         },
    //         deltag(index){
    //             this.tags.splice(index,1);
    //         },
    //         isinsert(index){
    //             this.index=index;
    //             (new bootstrap.Modal(document.getElementById('insertmodal'))).show();
    //         },
    //         isdel(index){
    //             this.index=index;
    //             (new bootstrap.Modal(document.getElementById('delmodal'))).show();
    //         },
    //         insert(){
    //             let data=Object.assign({},this.tags[this.index]);
    //             data._token="{{csrf_token()}}";
    //             let that=this;
    //             getData('{!! config('var.ati') !!}',function(json){
    //                 if(json.status===1){
    //                     that.tags[that.index].tid=json.tid;
    //                 }
    //             },"#msg",data);
    //         },
    //         del(){
    //             let that=this;
    //             getData('{!! config('var.atd') !!}'+that.tags[that.index].tid,function(json){
    //                 if(json.status===1){
    //                     that.tags.splice(that.index,1);
    //                 }
    //             },"#msg");
    //         },
    //         alert(index){
    //             let data=Object.assign({},this.tags[index]);
    //             data._token="{{csrf_token()}}";
    //             getData('{!! config('var.ata') !!}'+this.tags[index].tid,null,"#msg",data);
    //         },
    // @endif
    //         like(index){
    //             let that=this;
    //             getData('{!! config('var.til') !!}'+that.tags[index].tid,function(json){
    //                 if(json.status===1){
    //                     that.tags[index].like=true;
    //                     that.tags[index].tlnum=parseInt(that.tags[index].tlnum)+1;
    //                 }
    //             },"#msg");
    //         },
    //         dellike(index){
    //             let that=this;
    //             getData('{!! config('var.tdl') !!}'+that.tags[index].tid,function(json){
    //                 if(json.status===1){
    //                     that.tags[index].like=false;
    //                     that.tags[index].tlnum=parseInt(that.tags[index].tlnum)-1;
    //                 }
    //             },"#msg");
    //         },
        }
    }).mount('#arealist');
</script>

@endsection

@section('nextJS')
@endsection
