{{-- resources/views/location/pagenation.blade.php --}}
@if ($location->lastPage() > 1)
<ul class="pag-list">

    {{-- First --}}
    <li class="{{ $location->onFirstPage() ? 'pag-disabled' : '' }}">
        <a href="{{ $location->url(1) }}" class="pag-btn pag-btn-icon" title="Pertama">
            <i class="bi bi-chevron-double-left"></i>
        </a>
    </li>
    {{-- Prev --}}
    <li class="{{ $location->onFirstPage() ? 'pag-disabled' : '' }}">
        <a href="{{ $location->previousPageUrl() ?? '#' }}" class="pag-btn pag-btn-icon" title="Sebelumnya">
            <i class="bi bi-chevron-left"></i>
        </a>
    </li>

    {{-- Page numbers --}}
    @php
        $cur   = $location->currentPage();
        $last  = $location->lastPage();
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
            <a href="{{ $location->url($page) }}"
               class="pag-btn {{ $page == $cur ? 'pag-btn-active' : '' }}">
                {{ $page }}
            </a>
        </li>
        @php $prev = $page; @endphp
    @endforeach

    {{-- Next --}}
    <li class="{{ !$location->hasMorePages() ? 'pag-disabled' : '' }}">
        <a href="{{ $location->nextPageUrl() ?? '#' }}" class="pag-btn pag-btn-icon" title="Berikutnya">
            <i class="bi bi-chevron-right"></i>
        </a>
    </li>
    {{-- Last --}}
    <li class="{{ !$location->hasMorePages() ? 'pag-disabled' : '' }}">
        <a href="{{ $location->url($location->lastPage()) }}" class="pag-btn pag-btn-icon" title="Terakhir">
            <i class="bi bi-chevron-double-right"></i>
        </a>
    </li>

</ul>
@endif

{{-- Info teks --}}
<span class="pag-info">
    <strong>{{ $location->firstItem() ?? 0 }}–{{ $location->lastItem() ?? 0 }}</strong>
    dari <strong>{{ $location->total() }}</strong> data
    &nbsp;·&nbsp; Hal. <strong>{{ $location->currentPage() }}</strong>/{{ $location->lastPage() }}
</span>
