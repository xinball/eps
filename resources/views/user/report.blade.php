@extends('user.master')

@section('title','报备管理')
@section('nextCSS2')

@endsection

@section('main')
@include('appoint.insert',['type'=>'r'])
@include('appoint.alter',['type'=>'r'])

<button type="button" data-bs-toggle="modal" data-bs-target="#insert" data-bs-atime="" data-bs-sid="0" data-bs-atype="r" class="btn btn-outline-dark"><i class="bi bi-building-add"></i> 添加报备</button>
@include('appoint.list_tem',['utype'=>'u','type'=>'r'])

@endsection

@section('nextJS')

@endsection
