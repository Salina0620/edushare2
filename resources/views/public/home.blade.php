<x-app-layout>
    {{-- page-only styles --}}
    <style>
        .zigzag{display:none!important;}
    </style>

    {{-- ====================== HEADER / HERO ====================== --}}
    <x-slot name="header">
        <div class="mx-auto max-w-7xl px-4 md:px-6">
            <section id="hero"
                class="relative mb-8 overflow-hidden rounded-3xl shadow-xl text-white min-h-[32vh]"
                style="--mx:50%;--my:30%">
                <div class="pointer-events-none absolute inset-0">
                    <div class="absolute inset-0" style="background:linear-gradient(135deg,#0b1430 0%,#111b3a 45%,#0d1a2f 100%);"></div>
                    <div class="absolute -top-24 -left-24 h-[28rem] w-[28rem] rounded-full opacity-35 blur-3xl" style="background:radial-gradient(closest-side,#5b7cff,transparent 70%);"></div>
                    <div class="absolute -bottom-28 -right-24 h-[28rem] w-[28rem] rounded-full opacity-30 blur-3xl" style="background:radial-gradient(closest-side,#8a5cf6,transparent 70%);"></div>
                    <div class="absolute inset-0" style="background:radial-gradient(800px 320px at var(--mx) var(--my),rgba(255,255,255,.08),transparent 60%);"></div>
                    <div class="absolute inset-0" style="background:radial-gradient(120% 100% at 50% 10%,transparent 60%,rgba(0,0,0,.35));"></div>
                </div>

                <div class="relative z-10 px-6 py-8 sm:px-8 md:py-10">
                    <div class="mx-auto max-w-4xl text-center">
                        <div class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-semibold backdrop-blur-sm">
                            <span>📚 EduShare</span><span class="opacity-80">Study better, together</span>
                        </div>
                    </div>

                    <div class="mx-auto mt-4 max-w-3xl rounded-2xl bg-transparent px-6 py-6 backdrop-blur-md">
                        <div class="text-center">
                            <h1 class="font-extrabold leading-tight tracking-tight drop-shadow-sm"
                                style="font-size:clamp(28px,4vw,44px)">
                                Unlock Knowledge, <span class="opacity-90">Share Insights</span>
                            </h1>

                            <p class="mt-3 text-base md:text-lg leading-7 text-indigo-100/90">
                                Find, upload, and discuss notes across faculties and semesters.
                            </p>

                            <form action="{{ route('public.notes.index') }}" method="GET" class="mx-auto mt-6 max-w-2xl">
                                <div class="flex items-stretch gap-3">
                                    <div class="relative flex-1">
                                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-300">🔎</span>
                                        <input id="q" name="search" value="{{ request('search') }}" autocomplete="off"
                                            placeholder="Search notes by title, description, subject, faculty…"
                                            class="w-full rounded-xl border border-white/20 bg-white/95 pl-10 pr-3 py-3 text-base text-gray-900 shadow
                                            focus:ring-4 focus:ring-indigo-500/30 focus:border-indigo-500" />
                                    </div>
                                    <button type="submit"
                                        class="shrink-0 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white
                                        hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-white">
                                        Search
                                    </button>
                                </div>
                            </form>

                            <div class="mt-4 flex flex-wrap items-center justify-center">
                                <a href="{{ route('public.notes.index') }}"
                                   class="rounded-full border border-white/25 bg-white/10 px-4 py-1.5 text-sm font-medium text-white hover:bg-white/15 backdrop-blur-sm transition">
                                    Browse All Notes →
                                </a>
                                @auth
                                    <a href="{{ route('notes.create') }}"
                                       class="ml-3 mt-2 sm:mt-0 rounded-full bg-white px-4 py-1.5 text-sm font-semibold text-indigo-700 hover:bg-indigo-50 transition">
                                        Upload a Note
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <script>
            (function () {
                const hero = document.getElementById('hero');
                if (!hero) return;
                let raf;
                function move(e){
                    const r = hero.getBoundingClientRect();
                    const x = ((e.clientX - r.left) / r.width * 100).toFixed(2) + '%';
                    const y = ((e.clientY - r.top) / r.height * 100).toFixed(2) + '%';
                    hero.style.setProperty('--mx', x);
                    hero.style.setProperty('--my', y);
                }
                hero.addEventListener('mousemove', e => {
                    cancelAnimationFrame(raf); raf = requestAnimationFrame(()=>move(e));
                }, { passive:true });
                hero.addEventListener('touchmove', ev => {
                    if (!ev.touches || !ev.touches[0]) return;
                    const t = ev.touches[0];
                    cancelAnimationFrame(raf);
                    raf = requestAnimationFrame(()=>move({ clientX:t.clientX, clientY:t.clientY }));
                }, { passive:true });
            })();
        </script>
    </x-slot>

    {{-- ====================== BODY STACK ====================== --}}
    <div class="mx-auto max-w-7xl px-4 md:px-6 space-y-12 md:space-y-16">
        {{-- ========== STATS (with count-up) ========== --}}
        @php $stats = $stats ?? null; @endphp
        @if (!empty($stats))
            <section
                x-data
                x-init="$el.querySelectorAll('[data-count]').forEach(el => {
                    const target = +el.dataset.count; let n = 0;
                    const obs = new IntersectionObserver(([e]) => {
                        if (!e.isIntersecting) return;
                        const step = Math.max(1, Math.round(target / 60));
                        const id = setInterval(() => {
                            n = Math.min(target, n + step);
                            el.textContent = n.toLocaleString();
                            if (n >= target) clearInterval(id);
                        }, 20);
                        obs.disconnect();
                    }, { threshold: .4 });
                    obs.observe(el);
                })">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 rounded-2xl border border-gray-200 bg-white p-6">
                    <div class="flex items-center justify-center gap-3">
                        <span class="text-2xl">📄</span>
                        <div>
                            <div class="text-xl font-semibold leading-tight" data-count="{{ (int) ($stats['notes'] ?? 0) }}">0</div>
                            <div class="text-xs text-gray-500 mt-0.5">Notes</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-center gap-3">
                        <span class="text-2xl">⬇️</span>
                        <div>
                            <div class="text-xl font-semibold leading-tight" data-count="{{ (int) ($stats['downloads'] ?? 0) }}">0</div>
                            <div class="text-xs text-gray-500 mt-0.5">Downloads</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-center gap-3">
                        <span class="text-2xl">👩‍🎓</span>
                        <div>
                            <div class="text-xl font-semibold leading-tight" data-count="{{ (int) ($stats['users'] ?? 0) }}">0</div>
                            <div class="text-xs text-gray-500 mt-0.5">Students</div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        {{-- ========== HOME FEED (no outer border wrapper) ========== --}}
        <section
            x-data="homeFeed({
                baseUrl: '{{ route('public.notes.index') }}',
                initial: { sort: '{{ request('sort', 'recent') }}', faculty_id: '{{ request('faculty_id') ?? '' }}' }
            })"
            x-init="init()"
        >
            <div class="flex flex-wrap items-center gap-3">
                @php $tabs = ['trending' => '🔥 Trending','popular'=>'⭐ Popular','recent'=>'🕒 Recent']; @endphp
                @foreach ($tabs as $key => $label)
                    <a href="{{ route('public.notes.index', ['sort' => $key]) }}"
                       @click.prevent="setSort('{{ $key }}')"
                       class="inline-flex items-center gap-1 rounded-full px-3.5 py-1.5 text-sm border transition hover:bg-gray-50"
                       :class="sort === '{{ $key }}' ? 'border-indigo-300 bg-indigo-50 text-indigo-700' : 'border-gray-200 text-gray-700'">
                        {!! $label !!}
                    </a>
                @endforeach

                @isset($faculties)
                    <div class="ms-auto hidden md:flex flex-wrap items-center gap-2.5">
                        @foreach ($faculties->take(5) as $f)
                            <a href="{{ route('public.notes.index', ['faculty_id' => $f->id]) }}"
                               @click.prevent="setFaculty('{{ $f->id }}')"
                               class="rounded-full border px-3 py-1.5 text-sm transition"
                               :class="faculty_id === '{{ $f->id }}'
                                        ? 'border-indigo-300 bg-indigo-50 text-indigo-700'
                                        : 'border-gray-200 text-gray-700 hover:bg-gray-50'">
                                🎓 {{ $f->name }}
                            </a>
                        @endforeach
                    </div>
                @endisset
            </div>

            <div class="mt-6 mb-4 flex items-end justify-between">
                <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900" x-text="title"></h2>
                <a :href="viewAllHref()" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">View all →</a>
            </div>

            <div x-ref="feed" data-home-grid>
                @php $seed = $latestNotes ?? collect(); @endphp
                @if ($seed->isEmpty())
                    <div class="text-center py-14 rounded-2xl border border-dashed border-gray-300">
                        <div class="text-5xl mb-4">📂</div>
                        <p class="text-gray-600 text-lg">No notes yet.</p>
                        @auth
                            <a href="{{ route('notes.create') }}"
                               class="mt-5 inline-flex items-center px-5 py-2.5 rounded-full font-semibold text-white
                                      bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 shadow">
                                Upload a note
                            </a>
                        @endauth
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" data-grid>
                        @foreach ($seed as $note)
                            <div class="tilt-hover rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md">
                                @include('public.notes.partials.card', ['note' => $note])
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <template x-if="loading">
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <template x-for="i in 6" :key="i">
                        <div class="rounded-xl border p-4 animate-pulse">
                            <div class="h-40 rounded bg-gray-200/70 mb-3"></div>
                            <div class="h-4 w-3/4 bg-gray-200/70 rounded mb-2"></div>
                            <div class="h-4 w-1/2 bg-gray-200/70 rounded"></div>
                        </div>
                    </template>
                </div>
            </template>
        </section>

        {{-- ========== RECOMMENDED ========== --}}
        @auth
            @if (!empty($recommended) && $recommended->count())
                <section>
                    <div class="mb-6 flex items-end justify-between">
                        <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900">Recommended for You</h2>
                        <a href="{{ route('notes.recommended') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">See more →</a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach ($recommended as $note)
                            <div class="tilt-hover rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md">
                                @include('public.notes.partials.card', ['note' => $note])
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        @endauth

        {{-- ========== TRENDING TAGS ========== --}}
        @isset($trendingTags)
            @if ($trendingTags->count())
                <section>
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Trending topics</h3>
                    <div class="flex flex-wrap gap-2.5">
                        @foreach ($trendingTags as $tag)
                            <a href="{{ route('public.notes.index', ['tag' => $tag->slug]) }}"
                               class="rounded-full bg-gray-100 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-200">
                                #{{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        @endisset
    </div>

    <script>
        function homeFeed({ baseUrl, initial }) {
            return {
                baseUrl,
                sort: initial?.sort || 'recent',
                faculty_id: initial?.faculty_id || '',
                loading: false,
                get title() {
                    const map = { trending:'Trending Notes', popular:'Popular Notes', recent:'Latest Additions' };
                    return (map[this.sort] || 'Notes') + (this.faculty_id ? ' · Filtered' : '');
                },
                qs() {
                    const p = new URLSearchParams();
                    if (this.sort) p.set('sort', this.sort);
                    if (this.faculty_id) p.set('faculty_id', this.faculty_id);
                    return p.toString();
                },
                viewAllHref() { return this.baseUrl + '?' + this.qs(); },
                setSort(s) { if (this.sort !== s) { this.sort = s; this.update(); } },
                setFaculty(id) { this.faculty_id = (id === this.faculty_id) ? '' : id; this.update(); },
                async update() {
                    this.loading = true;
                    const url = this.baseUrl + '?' + this.qs() + '&partial=1';
                    try {
                        const res = await fetch(url, { headers: { 'X-Requested-With':'XMLHttpRequest' } });
                        const html = await res.text();
                        const doc = new DOMParser().parseFromString(html, 'text/html');
                        const grid = doc.querySelector('[data-results] [data-grid]') ||
                                     doc.querySelector('[data-results] .grid') ||
                                     doc.querySelector('[data-grid]') ||
                                     doc.querySelector('.grid');
                        if (grid) this.$refs.feed.innerHTML = grid.outerHTML;
                        else window.location.href = this.viewAllHref();
                    } catch {
                        window.location.href = this.viewAllHref();
                    } finally {
                        this.loading = false;
                    }
                },
            }
        }
    </script>
</x-app-layout>
