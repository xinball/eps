@extends('template.master')

@section('nextCSS')
    <link href="{{ url('/css/dashboard.css') }}" rel="stylesheet">
    @yield('nextCSS2')
@endsection

@section('body')
        <!--头部-->
        <header class="navbar navbar-light sticky-top bg-light flex-md-nowrap p-0 shadow">
            <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">个人中心</a>
            <!--诗句-->
            <div class="d-none d-md-block" style="padding:10px;width:100%;align-self:center;text-align:center;">{{ $statement }}</div>
            <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </header>
        <!--用户界面，左边那一栏-->
        <div class="container-fluid">
            <div class="row">
                <nav id="sidebarMenu" style="margin-top: 5rem;" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                    <div class="position-sticky pt-3">
                        <ul class="nav nav-pills flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ isset($seactive)&&$seactive?"active":"" }}" aria-current="page" href="/user/setting">
                                    <i class="bi bi-gear-wide-connected"></i> 个人信息
                                </a>
                            </li>
                            <li class="nav-item">
                                <!--点击会转到appoint-->
                                <a class="nav-link {{ isset($aactive)&&$aactive?"active":"" }}" href="/user/appoint">
                                <i class="bi bi-calendar-event-fill"></i> 预约管理
                                </a>
                            </li>
                            <li class="nav-item">
                                <!--点击会转到report-->
                                <a class="nav-link {{ isset($ractive)&&$ractive?"active":"" }}" href="/user/report">
                                    <i class="bi bi-clipboard-fill"></i> 报备管理
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>

                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4" style="margin-top: 20px;">
                    @yield('main')
                </main>
            </div>
        </div>
@endsection


