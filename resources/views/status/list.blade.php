@extends('template.master')

@section('title','提交状态')

@section('nextCSS')
@endsection

@section('body')
@include('template.statuslist')

@endsection

@section('nextJS')
    <script>
        $(function () {
        });
    </script>
@endsection
