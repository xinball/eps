@extends('admin.master')

@section('title','问题管理')
@section('nextCSS2')

@endsection

@section('main')
@include('template.probleminsert',['utype'=>'a'])
@include('template.problemalter',['utype'=>'a'])
<button type="button" data-bs-toggle="modal" data-bs-target="#insert" class="btn btn-outline-dark"><i class="bi bi-patch-plus-fill"></i> 添加问题</button>
<button type="button" data-bs-toggle="modal" data-bs-target="#import" class="btn btn-outline-success"><i class="bi bi-upload"></i> 导入题库</button>
<button type="button" data-bs-toggle="modal" data-bs-target="#export" class="btn btn-outline-success"><i class="bi bi-download"></i> 导出题库</button>
@include('template.problemlist',['utype'=>'a'])

@endsection

@section('nextJS')

@endsection
