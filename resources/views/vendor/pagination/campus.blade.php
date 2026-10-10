@if ($paginator->hasPages())
    {{-- Style ditulis sendiri (tanpa Tailwind) supaya ikon panah tidak membesar --}}
    @once
        <style>
            .campus-pagination {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 10px;
                margin: 28px 0 8px;
                font-family: 'Sora', Helvetica, sans-serif;
            }

            .campus-pagination__info {
                margin: 0;
                font-size: 13px;
                color: #5b4a78;
            }

            .campus-pagination__list {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 6px;
                margin: 0;
                padding: 0;
                list-style: none;
            }

            .campus-pagination__item {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 38px;
                height: 38px;
                padding: 0 12px;
                border-radius: 8px;
                border: 1px solid #d8cde7;
                background: #fff;
                color: #54269a;
                font-size: 14px;
                font-weight: 600;
                line-height: 1;
                text-decoration: none;
                box-sizing: border-box;
                transition: background-color .15s, color .15s, border-color .15s;
            }

            a.campus-pagination__item:hover {
                background: #eee7f7;
                border-color: #bd93f8;
            }

            .campus-pagination__item--nav {
                font-size: 20px;
                padding: 0 14px;
            }

            .is-active > .campus-pagination__item {
                background: #54269a;
                border-color: #54269a;
                color: #fff;
            }

            .is-disabled > .campus-pagination__item {
                background: #f3eefa;
                color: #b5a6cc;
                cursor: not-allowed;
            }

            .campus-pagination__dots {
                border-color: transparent;
                background: transparent;
                color: #8f6bc1;
            }
        </style>
    @endonce

    <nav class="campus-pagination" role="navigation" aria-label="Navigasi halaman">

        @if ($paginator->total() > 0)
            <p class="campus-pagination__info">
                Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}
                dari {{ $paginator->total() }} fasilitas
            </p>
        @endif

        <ul class="campus-pagination__list">

            {{-- Tombol sebelumnya --}}
            @if ($paginator->onFirstPage())
                <li class="is-disabled" aria-disabled="true">
                    <span class="campus-pagination__item campus-pagination__item--nav" aria-label="Halaman sebelumnya">&lsaquo;</span>
                </li>
            @else
                <li>
                    <a class="campus-pagination__item campus-pagination__item--nav"
                        href="{{ $paginator->previousPageUrl() }}" rel="prev"
                        aria-label="Halaman sebelumnya">&lsaquo;</a>
                </li>
            @endif

            {{-- Nomor halaman --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="is-disabled" aria-disabled="true">
                        <span class="campus-pagination__item campus-pagination__dots">{{ $element }}</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="is-active" aria-current="page">
                                <span class="campus-pagination__item">{{ $page }}</span>
                            </li>
                        @else
                            <li>
                                <a class="campus-pagination__item" href="{{ $url }}"
                                    aria-label="Ke halaman {{ $page }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol berikutnya --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a class="campus-pagination__item campus-pagination__item--nav"
                        href="{{ $paginator->nextPageUrl() }}" rel="next"
                        aria-label="Halaman berikutnya">&rsaquo;</a>
                </li>
            @else
                <li class="is-disabled" aria-disabled="true">
                    <span class="campus-pagination__item campus-pagination__item--nav" aria-label="Halaman berikutnya">&rsaquo;</span>
                </li>
            @endif

        </ul>
    </nav>
@endif
