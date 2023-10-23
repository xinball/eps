@extends('user.master')

@section('title','预约管理')
@section('nextCSS2')

@endsection

@section('main')
@include('appoint.insert')
@include('appoint.alter')

<button type="button" data-bs-toggle="modal" data-bs-target="#insert" data-bs-atime="" data-bs-sid="" data-bs-atype="p" class="btn btn-outline-dark"><i class="bi bi-building-add"></i> 添加预约</button>
@include('appoint.list_tem',['utype'=>'u'])

@endsection

@section('nextJS')

@endsection