
@if ($paginator->hasPages())
@php
    $pagenum=$pagenum??3;
    $pre=$paginator->currentPage()>$pagenum-1?$paginator->currentPage()-$pagenum+1:1;
    $next=$pre+$pagenum*2-2<$paginator->lastPage()?$pre+$pagenum*2-2:$paginator->lastPage();
@endphp

<nav style="padding-top:30px">
    <ul class="pagination justify-content-center">
        {{-- Previous Page Link --}}
        @if ($pre-$pagenum>0)
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->url($pre-$pagenum) }}" aria-label="@lang('pagination.previous')"><span>&laquo;</span></a>
            </li>
        @elseif ($paginator->onFirstPage())
            <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                <a class="page-link"><span aria-hidden="true">&laquo;</span></a>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->url(1) }}" rel="prev" aria-label="@lang('pagination.previous')"><span>&laquo;</span></a>
            </li>
        @endif

        @for ($i=$pre;$i<=$next;++$i)
            @if ($i == $paginator->currentPage())
                <li class="page-item active" aria-current="page"><span><label><input id="pageTo" type="number" value="{{ $i }}" min="1" max="{{ $paginator->lastPage() }}" />/<span style="font-size:12px;">{{ $paginator->lastPage() }}</span></label></span></li>
            @else
                <li class="page-item"><a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a></li>
            @endif
        @endfor

        {{-- Next Page Link --}}
        @if ($next+$pagenum<$paginator->lastPage())
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->url($next+$pagenum) }}" rel="next" aria-label="@lang('pagination.next')"><span>&raquo;</span></a>
            </li>
        @elseif ($paginator->currentPage()!=$paginator->lastPage())
        <li class="page-item">
            <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}" rel="next" aria-label="@lang('pagination.next')"><span>&raquo;</span></a>
        </li>
        @else
            <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                <a class="page-link"><span aria-hidden="true">&raquo;</span></a>
            </li>
        @endif
    </ul>
</nav>
    
@endif