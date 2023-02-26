@extends('admin.master')

@section('title','公告管理')
@section('nextCSS2')

@endsection

@section('main')

@include('notice.insert',['utype'=>'a'])
@include('notice.alter',['utype'=>'a'])

<button type="button" data-bs-toggle="modal" data-bs-target="#insert" class="btn btn-outline-dark"><i class="bi bi-patch-plus-fill"></i> 添加公告</button>
@include('notice.list_tem',['utype'=>'a'])

@endsection

@section('nextJS')

@endsection
