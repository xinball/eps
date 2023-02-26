@extends('user.master')

@section('title',($u==='i'?"添加":"参与").'比赛管理')
@section('nextCSS2')

@endsection
@section('main')
@include('template.contestinsert',['utype'=>'u','u'=>$u])
@include('template.contestalter',['utype'=>'u','u'=>$u])
{{-- <form action="/user/upload" method="post" enctype="multipart/form-data">
    {{ csrf_field() }}
    <input type="file" name="smfile" class="form-control" id="filepath" required>
    <button class="btn btn-outline-secondary" type="submit">上传</button>
</form> --}}
<button type="button" data-bs-toggle="modal" data-bs-target="#insert" class="btn btn-outline-dark"><i class="bi bi-patch-plus-fill"></i> 添加比赛</button>
@include('template.contestlist',['utype'=>'u','u'=>$u])

@endsection

@section('nextJS')
@endsection
