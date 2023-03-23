<!--侧边栏-->
<!--侧边栏共有的，叉号之类的-->
<div id="offcanvas" class="offcanvas offcanvas-start" style="opacity: 0.9;" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1"  aria-labelledby="offcanvasExampleLabel">
    <div class="offcanvas-header">
        {{-- class="offcanvas-title" --}}
        <h5 class="btn btn-light mb-0"  style="font-weight: bold;" data-bs-dismiss="offcanvas" id="offcanvasExampleLabel">{{ $config_basic['name'] }} <i class="bi bi-search"></i></h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        {{ $slot }}
    </div>
</div>