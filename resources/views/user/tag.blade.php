@extends('user.master')

@section('title','标签收藏管理')
@section('nextCSS2')

@endsection
@section('main')

@include('template.taglist',['utype'=>'u'])

@endsection

@section('nextJS')
@endsection
