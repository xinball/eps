@extends('user.master')

@section('title','比赛问题管理')
@section('nextCSS2')

@endsection
@section('main')
@include('template.probleminsert',['utype'=>'u'])
@include('template.problemalter',['utype'=>'u'])
{{-- <form action="/user/upload" method="post" enctype="multipart/form-data">
    {{ csrf_field() }}
    <input type="file" name="smfile" class="form-control" id="filepath" required>
    <button class="btn btn-outline-secondary" type="submit">上传</button>
</form> --}}
<button type="button" data-bs-toggle="modal" data-bs-target="#insert" class="btn btn-outline-dark"><i class="bi bi-patch-plus-fill"></i> 添加比赛问题</button>
@include('template.problemlist',['utype'=>'u'])

@endsection

@section('nextJS')
@endsection
