<!--分页-->
<!--页面数大于1-->
<div v-if="paginator.last_page>1">
<nav style="padding-top:30px">
    <ul class="pagination justify-content-center">
        {{-- Previous Page Link --}}
        <li v-if="paginator.pre-pagenum>0" class="page-item">
            <a class="page-link" @click="setpage(paginator.pre-pagenum)"><span>&laquo;</span></a>
        </li>
        <li v-else-if="paginator.current_page===1" class="page-item disabled" aria-disabled="true" >
            <a class="page-link"><span aria-hidden="true">&laquo;</span></a>
        </li>
        <li v-else class="page-item">
            <a class="page-link" @click="setpage(1)" rel="prev"><span>&laquo;</span></a>
        </li>
        {{-- v-if="i>=paginator.pre&&i<=paginator.next" --}}
        {{-- :class="[i==paginator.current_page]" --}}
        <li v-for="page in paginator.pagelist" :key="page"  class="page-item"  :class="{active:page===paginator.current_page}" aria-current="page">
            <span v-if="page===paginator.current_page" ><label><input id="pageTo" type="number" v-model="paramspre.page" v-on:keyup.enter="getData" min="1" :max="paginator.last_page" />/<span style="font-size:12px;">@{{ paginator.last_page }}</span></label></span>
            
            <a v-else class="page-link" @click="setpage(page)">@{{ page }}</a>
        </li>

        {{-- Next Page Link --}}

        <li v-if="paginator.next+pagenum<paginator.last_page" class="page-item">
            <a class="page-link"  @click="setpage(paginator.next+pagenum)" rel="next" ><span>&raquo;</span></a>
        </li>
        <li v-else-if="paginator.current_page!==paginator.last_page" class="page-item">
            <a class="page-link" @click="setpage(paginator.last_page)" rel="next"><span>&raquo;</span></a>
        </li>
        <li v-else class="page-item disabled" aria-disabled="true">
            <a class="page-link"><span aria-hidden="true">&raquo;</span></a>
        </li>
    </ul>
</nav>
    
</div>