{{-- resources/views/asetHabisPakai/_pagination.blade.php --}}
@if ($items->lastPage() > 1)
<ul class="pag-list">

    {{-- First --}}
    <li class="{{ $items->onFirstPage() ? 'pag-disabled' : '' }}">
        <a href="#" class="pag-btn pag-btn-icon pag-link" data-page="1" title="Pertama">
            <i class="bi bi-chevron-double-left"></i>
        </a>
    </li>
    {{-- Prev --}}
    <li class="{{ $items->onFirstPage() ? 'pag-disabled' : '' }}">
        <a href="#" class="pag-btn pag-btn-icon pag-link" data-page="{{ $items->currentPage() - 1 }}" title="Sebelumnya">
            <i class="bi bi-chevron-left"></i>
        </a>
    </li>

    {{-- Page numbers --}}
    @php
        $cur   = $items->currentPage();
        $last  = $items->lastPage();
        $pages = [];
        for ($i = 1; $i <= $last; $i++) {
            if ($i == 1 || $i == $last || ($i >= $cur - 2 && $i <= $cur + 2)) {
                $pages[] = $i;
            }
        }
        $prev = null;
    @endphp
    @foreach($pages as $page)
        @if($prev !== null && $page - $prev > 1)
            <li class="pag-ellipsis"><span>···</span></li>
        @endif
        <li>
            <a href="#" class="pag-btn pag-link {{ $page == $cur ? 'pag-btn-active' : '' }}"
               data-page="{{ $page }}">{{ $page }}</a>
        </li>
        @php $prev = $page; @endphp
    @endforeach

    {{-- Next --}}
    <li class="{{ !$items->hasMorePages() ? 'pag-disabled' : '' }}">
        <a href="#" class="pag-btn pag-btn-icon pag-link" data-page="{{ $items->currentPage() + 1 }}" title="Berikutnya">
            <i class="bi bi-chevron-right"></i>
        </a>
    </li>
    {{-- Last --}}
    <li class="{{ !$items->hasMorePages() ? 'pag-disabled' : '' }}">
        <a href="#" class="pag-btn pag-btn-icon pag-link" data-page="{{ $items->lastPage() }}" title="Terakhir">
            <i class="bi bi-chevron-double-right"></i>
        </a>
    </li>

</ul>
@endif

{{-- Info teks di samping pagination --}}
<span class="pag-info">
    <strong>{{ $items->firstItem() ?? 0 }}–{{ $items->lastItem() ?? 0 }}</strong>
    dari <strong>{{ $items->total() }}</strong> data
    &nbsp;·&nbsp; Hal. <strong>{{ $items->currentPage() }}</strong>/{{ $items->lastPage() }}
</span>