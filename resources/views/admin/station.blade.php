@extends('admin.master')

@section('title','站点管理')
@section('nextCSS2')

@endsection

@section('main')
@include('station.insert',['utype'=>'a'])
<button type="button" data-bs-toggle="modal" data-bs-target="#insert" class="btn btn-outline-dark"><i class="bi bi-patch-plus-fill"></i> 添加站点</button>
<button type="button" data-bs-toggle="modal" data-bs-target="#import" class="btn btn-outline-success"><i class="bi bi-upload"></i> 导入站点</button>
<button type="button" data-bs-toggle="modal" data-bs-target="#export" class="btn btn-outline-success"><i class="bi bi-download"></i> 导出站点</button>
@include('station.list_tem',['utype'=>'a'])

@endsection

@section('nextJS')

@endsection
