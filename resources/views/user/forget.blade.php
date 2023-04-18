@extends('template.master')

@section('title',$title??"忘记密码")
@section('body')
    @if(isset($fuser)&&$fuser)   <!--看是forget的第几个页面-->
        <!--邮件里发的链接点开后的页面，active没有是因为active的邮件链接点进去后只有提示-->
        <main class="form-login text-center" >
            <form method="get" action="{!! config('var.uf') !!}">
                <img class="mb-4" src="{{ '/img/icon.png' }}" alt="" width="72" >
                <h1 class="h3 mb-3 fw-normal">请重置 {{ $fuser['uname'] }} 的密码</h1>
                <input type="hidden" name="code" value="{{ $code??"" }}"/>
                <input type="hidden" name="uid" value="{{ isset($fuser)?$fuser->uid:"" }}"/>
                <div class="form-floating">
                    <input type="password" class="form-control" name="upwd" placeholder="新的密码" value="{{ $upwd??"" }}">
                    <label for="upwd">新的密码</label>
                </div>
                <div class="form-floating">
                    <input type="password" class="form-control" name="upwd1" placeholder="重复新的密码" value="{{ $upwd1??"" }}">
                    <label for="upwd1">重复新的密码</label>
                </div>
                <button class="w-100 btn btn-lg btn-success" type="submit">重置密码</button>
            </form>
        </main>
    @else
        <!--forget的页面-->
        <main class="form-login text-center" >
            <form method="get" action="{!! config('var.uf') !!}">
                <img class="mb-4" src="{{ '/img/icon.png' }}" alt="" width="72" >
                <h1 class="h3 mb-3 fw-normal">请输入您遗忘的身份证明/邮箱 及 姓名</h1>

                <div class="form-floating">
                    <input type="text" class="form-control" name="uidno" placeholder="身份证明/邮箱" value="{{ $uidno??"" }}">
                    <label for="uidno">身份证明/邮箱</label>
                </div>
                <div class="form-floating">
                    <input type="text" class="form-control" name="uname" placeholder="姓名" value="{{ $uname??"" }}">
                    <label for="uname">姓名</label>
                </div>
                <button class="w-100 btn btn-lg btn-success" type="submit">重置密码</button>
            </form>
        </main>
    @endif

@endsection


@section('nextJS')
    <script>

    </script>
@endsection
