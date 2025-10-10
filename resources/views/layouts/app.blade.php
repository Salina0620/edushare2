<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'EduShare') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        /* Static zig-zag divider for hero bottom */
        .zigzag {
            height: 22px;
            background:
                linear-gradient(-45deg, #fff 12px, transparent 0) 0 0/24px 24px repeat-x,
                linear-gradient(45deg, #fff 12px, transparent 0) 12px 12px/24px 24px repeat-x;
            pointer-events: none;
        }

        .hero,
        .site-footer {
            isolation: isolate;
        }

        .hero *,
        .site-footer * {
            animation: none !important;
        }

        .hero::before,
        .hero::after,
        .site-footer::before,
        .site-footer::after {
            content: none !important;
        }

        /* === Interactive hero (shared on all pages) === */
        #page-hero {
            --hx: 50%;
            --hy: 38%;
            /* mouse position (x,y) */
        }

        #page-hero .outer-bg {
            /* deep navy base */
            background:
                radial-gradient(1100px 520px at 50% -30%, #0e1626 0%, #0a1221 45%, transparent 75%),
                linear-gradient(180deg, #0d1526 0%, #0a1221 100%);
        }

        #page-hero .field {
            /* purple + blue blobs that we’ll steer with --hx/--hy */
            background:
                radial-gradient(800px 380px at var(--hx) var(--hy), rgba(124, 58, 237, .35), transparent 60%),
                radial-gradient(900px 420px at calc(100% - var(--hx)) calc(var(--hy) + 6%), rgba(59, 130, 246, .30), transparent 62%);
            pointer-events: none;
        }

        #page-hero .panel {
            /* inner rounded panel with subtle glass + outline */
            backdrop-filter: saturate(140%) blur(0.5px);
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .04), rgba(255, 255, 255, .01));
            position: relative;
        }

        #page-hero .panel::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            border: 1px solid rgba(255, 255, 255, .35);
            /* thin white outline */
            box-shadow:
                inset 0 0 0 1px rgba(255, 255, 255, .08),
                /* faint inner line */
                0 30px 60px rgba(0, 0, 0, .35),
                /* soft drop */
                0 10px 30px rgba(31, 41, 55, .35);
            /* extra depth */
            pointer-events: none;
        }
    </style>

    <style>
        /* Force 4 columns on ≥768px and add comfy horizontal gap */
        @media (min-width: 768px) {
            .footer-cols {
                display: flex;
                flex-wrap: nowrap;
                column-gap: 3rem;
            }

            .footer-cols>* {
                width: 25%;
            }
        }
    </style>
<style>
  /* CTA layout: row on desktop, centered vertically, buttons hard-right */
  @media (min-width: 768px){
    .cta-row{
      display:flex;
      flex-direction: row;        /* <-- this was missing */
      align-items: center;        /* vertical centering */
      gap: 1rem;
    }
    .cta-actions{
      margin-left: auto;          /* push to right edge */
      display:flex;
      align-items: center;        /* align buttons to text baseline */
      gap: .75rem;
    }
  }
</style>


</head>

<body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex flex-col">
    {{-- Fixed navbar --}}
    @includeIf('navigation-menu')

    {{-- Spacer equal to navbar height (prevents overlap) --}}
    <div class="h-16 md:h-20"></div>

    {{-- HERO only when a header slot is provided --}}

    @isset($header)
        <header id="page-hero" class="relative overflow-hidden mx-0 md:mx-6 mt-0 mb-10 rounded-[28px] shadow-2xl text-white">
            <!-- deep navy backdrop -->
            <div class="outer-bg absolute inset-0"></div>

            <!-- interactive color field -->
            <div class="field absolute inset-0"></div>

            <!-- inner panel with outline (your page header content sits inside) -->
            <div class="relative z-10 max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
                <div class="panel rounded-[22px] px-6 py-8 sm:px-10 sm:py-12 lg:px-14 lg:py-16">
                    {{ $header }}
                </div>
            </div>

            <div class="absolute inset-x-0 bottom-0 zigzag"></div>
        </header>
    @endisset



    <main class="grow pb-12">
        <div class="max-w-7xl mx-auto px-6">
            @if (session('success') || session('error'))
                <div class="mb-6 space-y-3">
                    @if (session('success'))
                        <div class="rounded-xl bg-green-50 text-green-800 border border-green-200 px-4 py-3">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="rounded-xl bg-rose-50 text-rose-800 border border-rose-200 px-4 py-3">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
            @endif

            {{ $slot }}
        </div>
    </main>
    <footer class="bg-white border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-6 py-12 space-y-12">

            {{-- CTA --}}
           <section class="rounded-xl bg-gray-50 ring-1 ring-gray-200 p-6 md:p-7 mb-10">
  <div class="cta-row flex flex-col gap-4">  {{-- mobile: stacked; desktop: .cta-row makes it a row --}}
    <div class="text-center md:text-left">
      <h3 class="text-base font-semibold text-gray-900">Share what you’ve learned.</h3>
      <p class="mt-1.5 text-sm text-gray-600">Upload helpful notes and make studying easier for everyone.</p>
    </div>

    {{-- buttons: centered on mobile, hard-right on desktop via .cta-actions --}}
    <div class="cta-actions justify-center">
      <a href="{{ route('public.notes.index') }}"
         class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100">
        Browse Notes
      </a>
      @auth
        <a href="{{ route('notes.create') }}"
           class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 shadow-sm">
          Upload Now
        </a>
      @else
        <a href="{{ route('register') }}"
           class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 shadow-sm">
          Create Account
        </a>
      @endauth
    </div>
  </div>
</section>



            {{-- 4 columns --}}
            <section class="footer-cols pb-12 border-b border-gray-200 gap-y-10 gap-x-12">
                {{-- EduShare --}}
                <div>
                    <h2 class="text-xl font-bold text-gray-900">EduShare</h2>
                    <p class="mt-3 text-sm leading-relaxed text-gray-600 max-w-xs">
                        Discover, share, and discuss notes across faculties and semesters.
                    </p>
                </div>

                {{-- Account --}}
                <nav aria-labelledby="footer-account">
                    <h3 id="footer-account"
                        class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 mb-4">Account</h3>
                    <ul class="space-y-2.5 text-sm">
                        @auth
                            <li><a class="hover:text-gray-900" href="{{ route('notes.mine') }}">My Notes</a></li>
                            <li><a class="hover:text-gray-900 " href="{{ route('notes.bookmarks') }}">Saved</a></li>
                            <li><a class="hover:text-gray-900" href="{{ route('profile.show') }}">Profile</a></li>
                        @else
                            <li><a class="hover:text-gray-900" href="{{ route('login') }}">Login</a></li>
                            <li><a class="hover:text-gray-900" href="{{ route('register') }}">Register</a></li>
                        @endauth
                    </ul>
                </nav>

                {{-- Explore --}}
                <nav aria-labelledby="footer-explore">
                    <h3 id="footer-explore"
                        class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 mb-4">Explore</h3>
                    <ul class="space-y-2.5 text-sm">
                        @isset($faculties)
                            @foreach ($faculties->take(5) as $f)
                                <li>
                                    <a class="hover:text-gray-900"
                                        href="{{ route('public.notes.index', ['faculty' => $f->id]) }}">
                                        {{ $f->name }}
                                    </a>
                                </li>
                            @endforeach
                        @else
                            <li><a class="hover:text-gray-900"
                                    href="{{ route('public.notes.index', ['sort' => 'recent']) }}">Recent</a></li>
                            <li><a class="hover:text-gray-900"
                                    href="{{ route('public.notes.index', ['sort' => 'popular']) }}">Popular</a></li>
                        @endisset
                    </ul>
                </nav>

                {{-- Product --}}
                <nav aria-labelledby="footer-product">
                    <h3 id="footer-product"
                        class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 mb-4">Product</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a class="hover:text-gray-900" href="{{ route('public.notes.index') }}">Browse Notes</a>
                        </li>
                        @auth
                            <li><a class="hover:text-gray-900" href="{{ route('notes.create') }}">Upload</a></li>
                            <li><a class="hover:text-gray-900" href="{{ route('notes.recommended') }}">Recommended</a></li>
                        @endauth
                    </ul>
                </nav>
            </section>

            {{-- Bottom strip (extra space above) --}}
            <section class="flex flex-col md:flex-row items-center justify-between gap-4 pt-10 text-sm text-gray-600">
                <p>© {{ date('Y') }} EduShare. All rights reserved.</p>
                <div class="flex items-center gap-6">
                    <a href="#" class="hover:text-gray-900">Privacy</a>
                    <a href="#" class="hover:text-gray-900">Terms</a>
                    <a href="#" class="hover:text-gray-900">Status</a>
                </div>
            </section>

        </div>
    </footer>




    <script>
        (function() {
            const hero = document.getElementById('page-hero');
            if (!hero) return;

            function setPos(x, y) {
                const r = hero.getBoundingClientRect();
                const nx = Math.max(0, Math.min(1, (x - r.left) / r.width));
                const ny = Math.max(0, Math.min(1, (y - r.top) / r.height));
                hero.style.setProperty('--hx', (nx * 100).toFixed(2) + '%');
                hero.style.setProperty('--hy', (ny * 100).toFixed(2) + '%');
            }

            hero.addEventListener('mousemove', e => setPos(e.clientX, e.clientY));
            hero.addEventListener('touchmove', e => {
                if (e.touches && e.touches[0]) setPos(e.touches[0].clientX, e.touches[0].clientY);
            }, {
                passive: true
            });
        })();
    </script>

    @livewireScripts
</body>

</html>
