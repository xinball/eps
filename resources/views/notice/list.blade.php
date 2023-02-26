@extends('template.master')

@section('title','公告')

@section('nextCSS')
@endsection

@section('body')
@include('notice.list_tem')

@endsection

@section('nextJS')
    <script>
        $(function () {
        });
    </script>
@endsection
