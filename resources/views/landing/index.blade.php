<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Temukan produk pilihan, buat pesanan dengan mudah, dan pantau statusnya dalam satu tempat.">
    <meta name="theme-color" content="#f8f8f4">
    <title>{{ config('app.name', 'RuangKita') }} — Belanja pilihan, lebih mudah</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f8f8f4] font-sans text-[#20251f] antialiased">
    <header class="relative z-10 border-b border-[#e7e9e1] bg-[#f8f8f4]/95">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-5 py-5 sm:px-8 lg:px-12" aria-label="Navigasi utama">
            <a href="{{ route('home') }}" class="flex items-center gap-3" aria-label="Beranda {{ config('app.name', 'RuangKita') }}">
                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-[#315c43] text-white">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4 10.5h16l-1.6 9H5.6L4 10.5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                        <path d="m8 10.5 4-6 4 6M9 14v2m6-2v2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="text-lg font-bold tracking-tight">{{ config('app.name', 'RuangKita') }}</span>
            </a>

            <div class="hidden items-center gap-8 md:flex">
                <a href="#koleksi" class="text-sm font-medium text-[#687166] transition hover:text-[#315c43]">Koleksi</a>
                <a href="#cara-kerja" class="text-sm font-medium text-[#687166] transition hover:text-[#315c43]">Cara kerja</a>
                <a href="#tentang" class="text-sm font-medium text-[#687166] transition hover:text-[#315c43]">Tentang</a>
            </div>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('products.index') }}" class="hidden text-sm font-semibold text-[#465344] transition hover:text-[#315c43] sm:inline-flex">Katalog</a>
                    <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-2 rounded-full bg-[#315c43] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#234631]">
                        Pesanan
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M4 10h12m-5-5 5 5-5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden px-3 py-2 text-sm font-semibold text-[#465344] transition hover:text-[#315c43] sm:inline-flex">Masuk</a>
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-full bg-[#315c43] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#234631] sm:px-5">
                        Mulai sekarang
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M4 10h12m-5-5 5 5-5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                @endauth
            </div>
        </nav>
    </header>

    <main>
        <section class="relative overflow-hidden">
            <div class="absolute -right-32 -top-32 h-96 w-96 rounded-full bg-[#e8eddc] blur-3xl" aria-hidden="true"></div>
            <div class="absolute -bottom-48 left-1/3 h-96 w-96 rounded-full bg-[#efe9dc] blur-3xl" aria-hidden="true"></div>
            <div class="relative mx-auto grid max-w-7xl items-center gap-14 px-5 py-16 sm:px-8 sm:py-20 lg:grid-cols-[1.05fr_0.95fr] lg:gap-16 lg:px-12 lg:py-24">
                <div class="max-w-2xl">
                    <span class="inline-flex items-center gap-2 rounded-full border border-[#dfe5d6] bg-white/80 px-3.5 py-2 text-xs font-semibold tracking-wide text-[#416149] shadow-sm">
                        <span class="h-2 w-2 rounded-full bg-[#7eaa70]"></span>
                        RUANG BELANJA YANG LEBIH PERSONAL
                    </span>
                    <h1 class="mt-7 max-w-xl text-5xl font-semibold leading-[1.08] tracking-[-0.055em] text-[#202b21] sm:text-6xl lg:text-7xl">
                        Temukan yang
                        <span class="font-serif font-medium italic text-[#64825b]">kamu suka.</span>
                    </h1>
                    <p class="mt-6 max-w-lg text-base leading-7 text-[#697267] sm:text-lg sm:leading-8">
                        Pilih produk favorit, pesan dalam beberapa langkah, dan ikuti perjalanan pesananmu dari awal hingga sampai.
                    </p>
                    <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                        @auth
                            <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center gap-3 rounded-full bg-[#315c43] px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-[#315c43]/15 transition hover:-translate-y-0.5 hover:bg-[#234631]">
                                Jelajahi katalog
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path d="M4 10h12m-5-5 5 5-5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-3 rounded-full bg-[#315c43] px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-[#315c43]/15 transition hover:-translate-y-0.5 hover:bg-[#234631]">
                                Mulai belanja
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path d="M4 10h12m-5-5 5 5-5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-full border border-[#d9ded3] bg-white/70 px-6 py-3.5 text-sm font-semibold text-[#435141] transition hover:bg-white">Saya sudah punya akun</a>
                        @endauth
                    </div>
                    <div class="mt-11 flex flex-wrap items-center gap-x-8 gap-y-4 border-t border-[#e0e4db] pt-6">
                        <div>
                            <p class="text-2xl font-semibold tracking-tight text-[#263b2b]">{{ number_format($productCount) }}<span class="text-[#77936d]">+</span></p>
                            <p class="mt-1 text-xs text-[#81887d]">produk tersedia</p>
                        </div>
                        <span class="hidden h-9 w-px bg-[#dfe3da] sm:block"></span>
                        <div>
                            <p class="text-2xl font-semibold tracking-tight text-[#263b2b]">{{ number_format($categoryCount) }}<span class="text-[#77936d]">+</span></p>
                            <p class="mt-1 text-xs text-[#81887d]">kategori pilihan</p>
                        </div>
                        <span class="hidden h-9 w-px bg-[#dfe3da] sm:block"></span>
                        <div>
                            <p class="text-2xl font-semibold tracking-tight text-[#263b2b]">Mudah</p>
                            <p class="mt-1 text-xs text-[#81887d]">pesan dan lacak</p>
                        </div>
                    </div>
                </div>

                <div class="relative mx-auto w-full max-w-xl">
                    <div class="absolute -left-5 top-16 z-10 hidden rounded-2xl border border-white/80 bg-white/90 p-4 shadow-xl shadow-[#29352a]/10 backdrop-blur sm:block">
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#edf2e8] text-[#55774f]">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M5 7h14v13H5zM9 7V5a3 3 0 0 1 6 0v2m-6 5h6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <div>
                                <p class="text-xs text-[#81887d]">Belanja pilihanmu</p>
                                <p class="mt-0.5 text-sm font-semibold text-[#29372a]">Semua dalam satu tempat</p>
                            </div>
                        </div>
                    </div>
                    <div class="absolute -right-4 bottom-14 z-10 hidden rounded-2xl border border-white/80 bg-white/90 p-4 shadow-xl shadow-[#29352a]/10 backdrop-blur sm:block">
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#f6eddf] text-[#a17d45]">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 3v3m0 12v3m9-9h-3M6 12H3m15.36-6.36-2.12 2.12m-8.48 8.48-2.12 2.12m12.72 0-2.12-2.12M8.76 8.76 6.64 6.64" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                    <circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.7"/>
                                </svg>
                            </span>
                            <div>
                                <p class="text-xs text-[#81887d]">Pantau setiap langkah</p>
                                <p class="mt-0.5 text-sm font-semibold text-[#29372a]">Status selalu jelas</p>
                            </div>
                        </div>
                    </div>
                    <div class="relative overflow-hidden rounded-[2rem] bg-[#dfe7d7] p-5 sm:rounded-[2.5rem] sm:p-8">
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_25%,rgba(255,255,255,0.8),transparent_38%),radial-gradient(circle_at_85%_80%,rgba(128,157,111,0.25),transparent_38%)]" aria-hidden="true"></div>
                        <div class="relative flex aspect-[4/4.2] flex-col justify-between overflow-hidden rounded-[1.5rem] border border-white/60 bg-[#f4f3e9] p-6 sm:rounded-[2rem] sm:p-9">
                            <div class="flex items-start justify-between">
                                <span class="rounded-full bg-white/80 px-3 py-1.5 text-[10px] font-semibold tracking-[0.18em] text-[#526b4f]">PILIHAN HARI INI</span>
                                <span class="flex h-11 w-11 items-center justify-center rounded-full border border-[#dfe3d7] bg-white/70 text-[#587150]">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M6 8h12l-1 12H7L6 8Zm3 0a3 3 0 1 1 6 0" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </div>

                            <div class="relative mx-auto flex h-56 w-56 items-center justify-center sm:h-72 sm:w-72">
                                <div class="absolute inset-4 rounded-full bg-[#e5c987]"></div>
                                <div class="absolute inset-0 rounded-full border border-[#c8d4bd]"></div>
                                <div class="absolute inset-8 rounded-full border border-white/70"></div>
                                <div class="relative flex h-40 w-32 -rotate-6 flex-col items-center justify-center rounded-[2rem] border border-[#d4c19b] bg-gradient-to-br from-[#faf4df] via-[#e8d8ae] to-[#d7bd85] shadow-2xl shadow-[#7b6840]/25 sm:h-52 sm:w-40">
                                    <div class="absolute inset-x-0 top-7 h-2 bg-[#b79c67]/60"></div>
                                    <div class="absolute inset-x-0 bottom-7 h-2 bg-[#b79c67]/60"></div>
                                    <svg class="h-16 w-16 text-[#596e4b] sm:h-20 sm:w-20" viewBox="0 0 80 80" fill="none" aria-hidden="true">
                                        <path d="M40 11c-8 12-19 20-19 35a19 19 0 0 0 38 0c0-15-11-23-19-35Z" fill="currentColor" fill-opacity=".92"/>
                                        <path d="M40 25c-4 9-11 15-11 24a11 11 0 0 0 22 0c0-9-7-15-11-24Z" fill="#E8D8AE"/>
                                        <path d="M35 50c2 4 5 6 10 5" stroke="#718362" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    <span class="mt-2 text-[9px] font-bold tracking-[0.3em] text-[#596e4b] sm:text-[10px]">PILIHAN</span>
                                </div>
                                <span class="absolute right-2 top-10 h-3 w-3 rounded-full bg-[#829774]"></span>
                                <span class="absolute bottom-9 left-2 h-2 w-2 rounded-full bg-[#c4a661]"></span>
                            </div>

                            <div class="flex items-end justify-between gap-4">
                                <div>
                                    <p class="text-xs font-medium text-[#788273]">Belanja lebih tenang</p>
                                    <p class="mt-1 text-xl font-semibold tracking-tight text-[#29372a] sm:text-2xl">Pilihan untukmu</p>
                                </div>
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#315c43] text-white">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M7 17 17 7M7 7h10v10" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>
                    <p class="mt-4 text-center text-xs text-[#81887d]">Temukan produk yang sesuai dengan kebutuhanmu.</p>
                </div>
            </div>
        </section>

        <section id="koleksi" class="border-t border-[#eceee7] bg-white py-16 sm:py-20">
            <div class="mx-auto max-w-7xl px-5 sm:px-8 lg:px-12">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                    <div class="max-w-xl">
                        <p class="text-xs font-semibold tracking-[0.2em] text-[#76906c]">KURASI UNTUKMU</p>
                        <h2 class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-[#263329] sm:text-4xl">Temukan inspirasi baru.</h2>
                        <p class="mt-3 text-sm leading-6 text-[#788075]">Lihat pilihan produk yang sedang tersedia di katalog kami.</p>
                    </div>
                    @auth
                        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#315c43] hover:text-[#234631]">
                            Lihat semua produk
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                <path d="M4 10h12m-5-5 5 5-5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#315c43] hover:text-[#234631]">
                            Gabung untuk melihat katalog
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                <path d="M4 10h12m-5-5 5 5-5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    @endauth
                </div>

                @if ($categories->isNotEmpty())
                    <div class="mt-8 flex flex-wrap gap-2.5">
                        @foreach ($categories as $category)
                            <span class="inline-flex items-center gap-2 rounded-full border border-[#e6e9e0] bg-[#f9faf7] px-3.5 py-2 text-xs font-medium text-[#657061]">
                                {{ $category->name }}
                                <span class="rounded-full bg-[#e8eee3] px-1.5 py-0.5 text-[10px] font-semibold text-[#55704f]">{{ $category->available_products_count }}</span>
                            </span>
                        @endforeach
                    </div>
                @endif

                @if ($products->isNotEmpty())
                    <div class="mt-9 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ($products as $index => $product)
                            @php($artwork = [
                                ['background' => 'bg-[#e9eee2]', 'shape' => 'bg-[#c6d3b9]', 'accent' => 'text-[#58734f]'],
                                ['background' => 'bg-[#f3e9dc]', 'shape' => 'bg-[#e4c9a6]', 'accent' => 'text-[#9a6d42]'],
                                ['background' => 'bg-[#e7eced]', 'shape' => 'bg-[#bdced0]', 'accent' => 'text-[#50727a]'],
                                ['background' => 'bg-[#efeadf]', 'shape' => 'bg-[#d9c99e]', 'accent' => 'text-[#897342]'],
                            ][$index % 4])
                            <article class="group overflow-hidden rounded-2xl border border-[#eaede6] bg-white transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-[#33402f]/[0.07]">
                                <div class="relative flex aspect-[1.12/1] items-center justify-center overflow-hidden {{ $artwork['background'] }}">
                                    <div class="absolute -right-7 -top-8 h-36 w-36 rounded-full bg-white/40"></div>
                                    <div class="absolute -bottom-10 -left-7 h-32 w-32 rounded-full bg-white/35"></div>
                                    <div class="relative flex h-36 w-36 items-center justify-center rounded-full {{ $artwork['shape'] }} transition duration-500 group-hover:scale-105">
                                        <svg class="h-14 w-14 {{ $artwork['accent'] }}" viewBox="0 0 64 64" fill="none" aria-hidden="true">
                                            <path d="M13 25h38l-4 27H17l-4-27Z" fill="currentColor" fill-opacity=".13" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                            <path d="M23 25c0-5 4-9 9-9s9 4 9 9M24 35v7m16-7v7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            <path d="M32 29v4m-2-2h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                        </svg>
                                    </div>
                                    <span class="absolute left-4 top-4 rounded-full bg-white/80 px-2.5 py-1 text-[10px] font-semibold tracking-wide text-[#596854]">PILIHAN</span>
                                </div>
                                <div class="p-4">
                                    <div class="flex min-h-5 items-center justify-between gap-2">
                                        <p class="truncate text-[11px] font-medium text-[#879082]">{{ $product->categories->first()?->name ?? 'Pilihan katalog' }}</p>
                                        <span class="shrink-0 text-[10px] text-[#7b8875]">Stok {{ $product->stock }}</span>
                                    </div>
                                    <h3 class="mt-2 truncate font-semibold text-[#2c382d]" title="{{ $product->name }}">{{ $product->name }}</h3>
                                    <div class="mt-3 flex items-center justify-between gap-2">
                                        <p class="text-sm font-bold text-[#315c43]">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</p>
                                        @auth
                                            <a href="{{ route('orders.create') }}" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#eef2eb] text-[#315c43] transition hover:bg-[#315c43] hover:text-white" aria-label="Beli {{ $product->name }}">
                                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                                    <path d="M4 10h12m-5-5 5 5-5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </a>
                                        @else
                                            <a href="{{ route('register') }}" class="text-xs font-semibold text-[#315c43] hover:underline">Pilih</a>
                                        @endauth
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="mt-9 rounded-2xl border border-dashed border-[#dfe4da] bg-[#fafbf8] px-6 py-12 text-center">
                        <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-[#edf2e8] text-[#55734f]">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 8h16l-1.5 11h-13L4 8Zm4 0a4 4 0 0 1 8 0" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <h3 class="mt-4 font-semibold text-[#303c30]">Koleksi baru sedang disiapkan</h3>
                        <p class="mt-1 text-sm text-[#7a8376]">Silakan kembali lagi nanti untuk melihat produk pilihan.</p>
                    </div>
                @endif
            </div>
        </section>

        <section id="cara-kerja" class="bg-[#eff2eb] py-16 sm:py-20">
            <div class="mx-auto max-w-7xl px-5 sm:px-8 lg:px-12">
                <div class="mx-auto max-w-2xl text-center">
                    <p class="text-xs font-semibold tracking-[0.2em] text-[#76906c]">SINGKAT DAN PRAKTIS</p>
                    <h2 class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-[#263329] sm:text-4xl">Belanja dalam tiga langkah.</h2>
                    <p class="mt-3 text-sm leading-6 text-[#788075]">Dari menemukan produk sampai memantau pesanan, semuanya dirancang tetap sederhana.</p>
                </div>

                <div class="mt-10 grid gap-5 md:grid-cols-3">
                    <article class="rounded-2xl border border-white bg-white/80 p-6">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#e7eee1] text-[#4c7049]">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle cx="10.8" cy="10.8" r="6.8" stroke="currentColor" stroke-width="1.7"/>
                                <path d="m16 16 4 4m-9-9h3m-1.5-1.5v3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <p class="mt-5 text-xs font-semibold tracking-[0.15em] text-[#8b9784]">LANGKAH 01</p>
                        <h3 class="mt-2 text-lg font-semibold text-[#2c382d]">Jelajahi pilihan</h3>
                        <p class="mt-2 text-sm leading-6 text-[#778073]">Temukan produk dan lihat ketersediaan stok di katalog.</p>
                    </article>
                    <article class="rounded-2xl border border-white bg-white/80 p-6">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#f3ecdf] text-[#9a7748]">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 5h2l2.2 10.2a2 2 0 0 0 2 1.6h7.6a2 2 0 0 0 1.9-1.4L21 9H7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="10" cy="20" r="1" fill="currentColor"/><circle cx="18" cy="20" r="1" fill="currentColor"/>
                            </svg>
                        </span>
                        <p class="mt-5 text-xs font-semibold tracking-[0.15em] text-[#8b9784]">LANGKAH 02</p>
                        <h3 class="mt-2 text-lg font-semibold text-[#2c382d]">Buat pesanan</h3>
                        <p class="mt-2 text-sm leading-6 text-[#778073]">Pilih produk, atur jumlah, dan masukkan alamat pengiriman.</p>
                    </article>
                    <article class="rounded-2xl border border-white bg-white/80 p-6">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#e9edf0] text-[#587081]">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 7h10v11H4zM14 11h4l3 3v4h-7z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="8" cy="19" r="2" fill="white" stroke="currentColor" stroke-width="1.7"/>
                                <circle cx="18" cy="19" r="2" fill="white" stroke="currentColor" stroke-width="1.7"/>
                            </svg>
                        </span>
                        <p class="mt-5 text-xs font-semibold tracking-[0.15em] text-[#8b9784]">LANGKAH 03</p>
                        <h3 class="mt-2 text-lg font-semibold text-[#2c382d]">Pantau pesanan</h3>
                        <p class="mt-2 text-sm leading-6 text-[#778073]">Lihat status dan riwayat pesanan melalui akunmu.</p>
                    </article>
                </div>
            </div>
        </section>

        <section id="tentang" class="bg-white px-5 py-16 sm:px-8 sm:py-20 lg:px-12">
            <div class="mx-auto flex max-w-7xl flex-col items-start justify-between gap-8 rounded-[2rem] bg-[#274c36] px-7 py-10 text-white sm:px-10 sm:py-12 lg:flex-row lg:items-center lg:px-14">
                <div class="max-w-2xl">
                    <p class="text-xs font-semibold tracking-[0.2em] text-[#c2d5b9]">SIAP MENJELAJAH?</p>
                    <h2 class="mt-3 text-3xl font-semibold tracking-[-0.04em] sm:text-4xl">Temukan pilihanmu hari ini.</h2>
                    <p class="mt-3 max-w-xl text-sm leading-6 text-white/70">Buat akun untuk melihat katalog, memesan produk, dan memantau setiap pesananmu.</p>
                </div>
                @auth
                    <a href="{{ route('products.index') }}" class="inline-flex shrink-0 items-center gap-3 rounded-full bg-[#f3f1e8] px-6 py-3.5 text-sm font-semibold text-[#274c36] transition hover:bg-white">
                        Buka katalog
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M4 10h12m-5-5 5 5-5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-flex shrink-0 items-center gap-3 rounded-full bg-[#f3f1e8] px-6 py-3.5 text-sm font-semibold text-[#274c36] transition hover:bg-white">
                        Buat akun gratis
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M4 10h12m-5-5 5 5-5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                @endauth
            </div>
        </section>
    </main>

    <footer class="border-t border-[#e7e9e1] bg-[#f8f8f4]">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-5 py-6 sm:flex-row sm:items-center sm:justify-between sm:px-8 lg:px-12">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 text-sm font-bold text-[#364437]">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-[#315c43] text-white">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4 10.5h16l-1.6 9H5.6L4 10.5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                        <path d="m8 10.5 4-6 4 6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                {{ config('app.name', 'RuangKita') }}
            </a>
            <p class="text-xs text-[#858c81]">&copy; {{ now()->year }} {{ config('app.name', 'RuangKita') }}. Belanja dengan lebih mudah.</p>
            <div class="flex items-center gap-5 text-xs font-medium text-[#657061]">
                @auth
                    <a href="{{ route('products.index') }}" class="hover:text-[#315c43]">Katalog</a>
                    <a href="{{ route('orders.index') }}" class="hover:text-[#315c43]">Pesanan</a>
                @else
                    <a href="{{ route('login') }}" class="hover:text-[#315c43]">Masuk</a>
                    <a href="{{ route('register') }}" class="hover:text-[#315c43]">Daftar</a>
                @endauth
            </div>
        </div>
    </footer>
</body>
</html>
