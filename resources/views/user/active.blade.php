@extends('template.master')


@section('title',"用户激活")
@section('body')
    <main class="form-login text-center" >
        <form method="get" action="{!! config('var.ua') !!}">
            <img class="mb-4" src="{{ '/img/icon.png' }}" alt="" width="72" >
            <h1 class="h3 mb-3 fw-normal">请输入您要激活用户的身份证明/邮箱账号</h1>

            <div class="form-floating">
                <input type="text" class="form-control" name="uname" v-model="uname" value="{!! isset($uname)?$uname:'' !!}" placeholder="name@example.com">
                <label for="uname">身份证明/邮箱</label>
            </div>
            <button class="w-100 btn btn-lg btn-success" type="submit">激活</button>
        </form>
    </main>
@endsection


@section('nextJS')
    <script>

    </script>
@endsection
