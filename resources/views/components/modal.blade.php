<!--x-modal继承了这个-->
<!--拟态框-->
<div class="modal fade" onselectstart="return false;" unselectable="on" aria-hidden="true" aria-labelledby="{{ $id }}" tabindex="-1" data-bs-focus="false" role="dialog" id="{{ $id }}">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable {{ $class }}" role="document">
        <div class="modal-content" style="border-radius: .75rem;">
            <div class="modal-header p-4 pb-4 border-bottom-0">
                <h2 class="modal-title fw-bold mb-0 text-truncate" style="width: 100%">{{ $title }}</h2>
                <button type="button" id="{{ $id }}-close" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="{{ $id }}-msg" style="z-index:2000;position:absolute;width: 100%;top:0;"></div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            @if ($footer!="")
                <div class="modal-footer">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>

