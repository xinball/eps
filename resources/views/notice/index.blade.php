@extends('template.master')

@section('title','公告-')

@section('nextCSS')
@endsection

@section('body')

<main id="notice" class="container p-3 pb-3">
    <div class="row">
        <div class="article col-12">
            <div class="break">
                <h1>@{{ notice.ntitle }}</h1>
                <h4>@{{ notice.ndes }}</h4>    
            </div>
            <div class="break" style="padding: 20px;" v-html="notice.ninfo"></div>
        </div>
    </div>

</main>
@endsection

@section('nextJS')


<script>
    const notice=Vue.createApp({
        data(){
            return {
                notice:(json.data!==null?json.data.notice:null),

            };
        },
        mounted(){
            this.init();
        },
        methods:{
            init(){
                if(json.data!==null){
                    document.title+=this.notice.ntitle;
                }
            },
        }

    }).mount('#notice');

    
</script>

@endsection
