@extends('admin.master')

@section('title','提交状态管理')
@section('nextCSS2')

@endsection

@section('main')

@include('template.statuslist',['utype'=>'a'])

@endsection

@section('nextJS')

@endsection
