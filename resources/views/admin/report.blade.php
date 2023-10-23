@extends('admin.master')

@section('title','报备管理')
@section('nextCSS2')

@endsection

@section('main')

@include('appoint.list_tem',['utype'=>'a','type'=>'r'])

@endsection

@section('nextJS')

@endsection
