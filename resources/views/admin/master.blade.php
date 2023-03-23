@extends('template.master')

@section('nextCSS')
    <link href="{{ url('/css/dashboard.css') }}" rel="stylesheet">
    @yield('nextCSS2')
@endsection

@section('body')

    <!--顶部那一行-->
    <header class="navbar navbar-light sticky-top bg-light flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">管理中心</a>
        <div class="d-none d-md-block " style="padding:10px;width:100%;align-self:center;text-align:center;">{{ $statement }}</div>
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </header>

    <!--不同功能分别跳转-->
    <div class="container-fluid">
        <div class="row">
            <nav id="sidebarMenu"style="margin-top: 5rem;"  class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column nav-pills">
                        <li class="nav-item">
                            <a class="nav-link {{ isset($aactive)&&$aactive?"active":"" }}" href="/admin/appoint">
                                <i class="bi bi-calendar-event-fill"></i> 预约管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ isset($ractive)&&$ractive?"active":"" }}" href="/admin/report">
                                <i class="bi bi-clipboard-fill"></i> 报备管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ isset($sactive)&&$sactive?"active":"" }}" href="/admin/station">
                                <i class="bi bi-geo-fill"></i> 管理站点
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ isset($lactive)&&$lactive?"active":"" }}" href="/admin/location">
                                <i class="bi bi-building-fill-gear"></i> 管理报备点
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ isset($uactive)&&$uactive?"active":"" }}" href="/admin/user">
                                <i class="bi bi-person-fill-gear"></i> 管理用户
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ isset($nactive)&&$nactive?"active":"" }}" href="/admin/notice">
                                <i class="bi bi-easel2-fill"></i> 管理公告
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ isset($seactive)&&$seactive?"active":"" }}" aria-current="page" href="/admin/setting">
                                <i class="bi bi-gear-fill"></i> 网站配置
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


