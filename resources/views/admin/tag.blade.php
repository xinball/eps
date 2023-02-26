@extends('admin.master')

@section('title','标签管理')
@section('nextCSS2')

@endsection
@section('main')

@include('template.taglist',['utype'=>'a'])

@endsection

@section('nextJS')
@endsection
