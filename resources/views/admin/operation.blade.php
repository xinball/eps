@extends('admin.master')

@section('title','用户日志')
@section('nextCSS2')

@endsection

@section('main')
@include('template.operationlist',['utype'=>'a'])
@endsection

@section('nextJS')
@endsection
