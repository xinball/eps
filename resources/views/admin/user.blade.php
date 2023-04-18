@extends('admin.master')

@section('title','用户管理')
@section('nextCSS2')

@endsection

@section('main')

{{-- @include('template.userinsert') --}}
{{-- @include('template.useralter') --}}

{{-- <button type="button" data-bs-toggle="modal" data-bs-target="#insert" class="btn btn-outline-dark"><i class="bi bi-patch-plus-fill"></i> 添加公告</button> --}}
@include('admin.userlist')

<main id="banlist" class="container list shadow">
    <h4>封禁IP列表</h4>
    <div class="row">
        <div class="col-sm-6 col-lg-4" v-for="(ban,index) in bans">
            <div class="input-group">
                <span class="input-group-text">@{{ index }}</span>
                <a class="btn btn-outline-dark form-control" >@{{ ban }}</a>
                <a class="btn btn-outline-danger" @click="del(index)">删除</a>
            </div>
        </div>
        <div class="col-12 row">
            <div class="col-6">
                <a @click="clear" class="btn btn-fill btn-outline-danger">清空</a>
            </div>
            <div class="col-6">
                <a @click="saveban" class="btn btn-fill btn-outline-success">保存</a>
            </div>
        </div>
    </div>
</main>

<script>

const banlist=Vue.createApp({
    data(){
        return{
            bans:{!! json_encode($ban) !!},
        }
    },
    mounted(){
    },
    computed:{
    },
    methods:{

        //删除被ban的
        del(index){
            this.bans.splice(index,1);
        },

        //保存
        saveban(){
            let data={
                ban:JSON.stringify(this.bans),
                _token:"{{csrf_token()}}",
            };
            getData("{!! config('var.aal') !!}"+'ban',null,"#msg",data);
        },

        //清空被ban的
        clear(){
            this.bans.length=0;
        }
    }
}).mount('#banlist');
</script>
@endsection

@section('nextJS')

@endsection
