@extends('template.master')

@section('title','个人主页-')
@section('nextCSS')
    <link href="{{ url('/css/dashboard.css') }}" rel="stylesheet">
@endsection

@section('body')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 col-lg-10 col-xxl-8 align-self-auto justify-content-center" style="margin: auto;">
            <!--正因为有x-avatar，所以才会显示头像一系列的东西-->
                <x-avatar/>
            </div>
        </div>
    </div>
@endsection

@section('nextJS')
@endsection
