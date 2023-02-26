@if($current_page<=$last_page)
<nav aria-label="Page navigation" >
    <ul class="pagination justify-content-center">
        @if($preCount-$pageLeng-1>0)
        <li class="page-item"><a class="page-link" href="{{ $path }}"><<</a></li>
        @elseif()
        @else

    </ul>
</nav>

<script>
    $("#pageTo").keydown(function(e){
        if (e.which === 13) {
            window.location.href="'.$navigation.'"+$("#pageTo").val();
        }
    });
</script>
@endif