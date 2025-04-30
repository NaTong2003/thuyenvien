@if ($paginator->hasPages())
    <nav>
        <ul class="pagination">
            {{-- Liên kết Previous Page --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">&laquo; {{ __('Trước') }}</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo; {{ __('Trước') }}</a>
                </li>
            @endif

            {{-- Các liên kết số trang --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
                @endif

                {{-- Mảng các liên kết --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Liên kết Next Page --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">{{ __('Sau') }} &raquo;</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">{{ __('Sau') }} &raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif 