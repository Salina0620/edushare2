<x-app-layout>
  @php $s = request('sort','new'); @endphp

  <style>
    /* Hide native dropdown arrow across browsers */
    .select-clean{
      -webkit-appearance:none;
      -moz-appearance:none;
      appearance:none;
      background-image:none;
    }
  </style>

  {{-- HEADER --}}
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="text-2xl md:text-3xl font-extrabold">Browse Notes</h2>
      @auth
        <a href="{{ route('notes.create') }}" class="btn-primary">+ Upload</a>
      @endauth
    </div>
  </x-slot>

  {{-- PAGE WRAPPER WITH LIVE SEARCH --}}
  <div
    x-data="browseNotes({
      baseUrl: '{{ route('public.notes.index') }}',
      initial: @js(request()->all())
    })"
    x-init="init()"
    class="space-y-6"
  >
   {{-- FILTERS / SEARCH — single row, no wrap --}}
<div class="card-glass p-5 md:p-6 mb-6 overflow-x-auto">
  <form class="min-w-max flex flex-nowrap items-end gap-4" @submit.prevent="update()">

    {{-- Faculty --}}
    <div class="shrink-0 w-[240px]">
      <label class="block text-xs font-semibold text-gray-600 mb-1">Faculty</label>
      <div class="relative">
        <select x-model="state.faculty_id" @change="update()"
                class="select-clean h-11 w-full rounded-xl border border-gray-300 bg-white pl-3 pr-10 text-sm shadow-sm
                       focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30">
          <option value="">All Faculties</option>
          @foreach($faculties as $f)
            <option value="{{ $f->id }}">{{ $f->name }}</option>
          @endforeach
        </select>
        {{-- down chevron --}}
        <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-500"
             viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 8l4 4 4-4" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
    </div>

    {{-- Semester --}}
    <div class="shrink-0 w-[240px]">
      <label class="block text-xs font-semibold text-gray-600 mb-1">Semester</label>
      <div class="relative">
        <select x-model="state.semester_id" @change="update()"
                class="select-clean h-11 w-full rounded-xl border border-gray-300 bg-white pl-3 pr-10 text-sm shadow-sm
                       focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30">
          <option value="">All Semesters</option>
          @foreach($semesters as $s)
            <option value="{{ $s->id }}">{{ $s->name }}</option>
          @endforeach
        </select>
        <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-500"
             viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 8l4 4 4-4" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
    </div>

    {{-- Subject --}}
    <div class="shrink-0 w-[300px]">
      <label class="block text-xs font-semibold text-gray-600 mb-1">Subject</label>
      <div class="relative">
        <select x-model="state.subject_id" @change="update()"
                class="select-clean h-11 w-full rounded-xl border border-gray-300 bg-white pl-3 pr-10 text-sm shadow-sm
                       focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30">
          <option value="">All Subjects</option>
          @foreach($subjects as $s)
            <option value="{{ $s->id }}">{{ $s->name }}</option>
          @endforeach
        </select>
        <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-500"
             viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 8l4 4 4-4" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
    </div>

    {{-- Search (flex) --}}
    <div class="min-w-[280px] flex-1">
      <label class="block text-xs font-semibold text-gray-600 mb-1">Search</label>
      <div class="relative">
        <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"
             viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <circle cx="11" cy="11" r="7" stroke-width="1.5"/>
          <path d="M20 20l-3.5-3.5" stroke-width="1.5"/>
        </svg>
        <input type="text"
               x-model.debounce.300ms="state.search"
               @input.debounce.300ms="update()"
               placeholder="Search title or description…"
               class="h-11 w-full rounded-xl border border-gray-300 bg-white pl-10 pr-3 text-sm shadow-sm
                      placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30">
      </div>
    </div>

    {{-- Sort --}}
    <div class="shrink-0 w-[200px]">
      <label class="block text-xs font-semibold text-gray-600 mb-1">Sort</label>
      <div class="relative">
        <select x-model="state.sort" @change="update()"
                class="select-clean h-11 w-full rounded-xl border border-gray-300 bg-white pl-3 pr-10 text-sm shadow-sm
                       focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30">
          <option value="new">Newest</option>
          <option value="popular">Most Viewed</option>
          <option value="downloaded">Most Downloaded</option>
          <option value="discussed">Most Discussed</option>
        </select>
        <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-500"
             viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 8l4 4 4-4" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
    </div>

    {{-- Actions (slightly smaller) --}}
    <div class="shrink-0 flex items-end justify-end gap-2">
      <button class="btn-primary h-10 px-4 text-sm" type="submit">Apply</button>
      <button type="button" class="btn-secondary h-10 px-4 text-sm" x-show="hasAnyFilter" @click="clear()">Clear</button>
    </div>
  </form>
</div>


    {{-- RESULTS (this whole block gets replaced on live fetch) --}}
    <div x-ref="results" data-results>
      {{-- Results head --}}
      <div class="flex items-center justify-between mb-4">
        <div class="text-sm text-gray-600">
          @if($notes->total())
            Showing <span class="font-semibold">{{ $notes->firstItem() }}–{{ $notes->lastItem() }}</span> of
            <span class="font-semibold">{{ $notes->total() }}</span> results
          @else
            0 results
          @endif
        </div>
      </div>

      @if($notes->isEmpty())
        <div class="text-center py-16">
          <div class="text-6xl mb-4">🔎</div>
          <p class="text-gray-600 text-lg mb-2">No notes match your filters.</p>
          <a href="{{ route('public.notes.index') }}" class="btn-secondary">Reset Filters</a>
          @auth
            <div class="mt-3"><a href="{{ route('notes.create') }}" class="btn-primary">Upload a note</a></div>
          @endauth
        </div>
      @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          @foreach($notes as $note)
            @include('public.notes.partials.card', ['note'=>$note])
          @endforeach
        </div>

        {{-- Pagination (annotate links for live wiring) --}}
        <div class="mt-10">
          @if ($notes->hasPages())
            <nav class="flex items-center justify-center gap-2 text-sm">
              {{-- Previous --}}
              @if ($notes->onFirstPage())
                <span class="px-3 py-1.5 rounded border border-gray-200 text-gray-400">Prev</span>
              @else
                <a href="{{ $notes->previousPageUrl() }}"
                   class="px-3 py-1.5 rounded border border-gray-200 hover:bg-white"
                   data-page-link data-page="{{ $notes->currentPage()-1 }}">Prev</a>
              @endif

              {{-- Numbers --}}
              @foreach (range(1, $notes->lastPage()) as $p)
                @if ($p === $notes->currentPage())
                  <span class="px-3 py-1.5 rounded bg-indigo-600 text-white border border-indigo-600">{{ $p }}</span>
                @elseif (abs($p - $notes->currentPage()) <= 2 || $p===1 || $p===$notes->lastPage())
                  <a href="{{ $notes->url($p) }}"
                     class="px-3 py-1.5 rounded border border-gray-200 hover:bg-white"
                     data-page-link data-page="{{ $p }}">{{ $p }}</a>
                @elseif ($p === 2 || $p === $notes->lastPage()-1)
                  <span class="px-2">…</span>
                @endif
              @endforeach

              {{-- Next --}}
              @if ($notes->hasMorePages())
                <a href="{{ $notes->nextPageUrl() }}"
                   class="px-3 py-1.5 rounded border border-gray-200 hover:bg-white"
                   data-page-link data-page="{{ $notes->currentPage()+1 }}">Next</a>
              @else
                <span class="px-3 py-1.5 rounded border border-gray-200 text-gray-400">Next</span>
              @endif
            </nav>
          @endif
        </div>
      @endif
    </div>

    {{-- SKELETON (shown while loading) --}}
    <template x-if="loading">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <template x-for="i in 6" :key="i">
          <div class="rounded-xl border p-4 animate-pulse">
            <div class="h-40 rounded bg-gray-200/70 mb-3"></div>
            <div class="h-4 w-3/4 bg-gray-200/70 rounded mb-2"></div>
            <div class="h-4 w-1/2 bg-gray-200/70 rounded"></div>
          </div>
        </template>
      </div>
    </template>
  </div>

  {{-- ALPINE CONTROLLER --}}
  <script>
    function browseNotes({ baseUrl, initial }) {
      return {
        baseUrl,
        state: {
          faculty_id: initial.faculty_id ?? '',
          semester_id: initial.semester_id ?? '',
          subject_id: initial.subject_id ?? '',
          search: initial.search ?? '',
          sort: initial.sort ?? 'new',
          page: initial.page ? parseInt(initial.page,10) : 1,
        },
        loading: false,
        hasAnyFilter: false,
        aborter: null,

        init() {
          this.computeHasAny();
          this.rewirePagination();
        },

        computeHasAny() {
          const { faculty_id, semester_id, subject_id, search, sort } = this.state;
          this.hasAnyFilter = !!(faculty_id || semester_id || subject_id || search || (sort && sort !== 'new'));
        },

        setSort(s) {
          if (this.state.sort === s) return;
          this.state.sort = s;
          this.state.page = 1;
          this.update();
        },

        clear() {
          this.state = { faculty_id:'', semester_id:'', subject_id:'', search:'', sort:'new', page:1 };
          this.update();
        },

        queryString() {
          const p = new URLSearchParams();
          for (const [k,v] of Object.entries(this.state)) {
            if (v !== '' && v != null) p.set(k, v);
          }
          return p.toString();
        },

        async update(push=true) {
          this.computeHasAny();
          this.loading = true;
          const url = this.baseUrl + '?' + this.queryString() + '&partial=1';

          // abort previous request if any
          if (this.aborter) this.aborter.abort();
          this.aborter = new AbortController();

          try {
            const res = await fetch(url, {
              headers: { 'X-Requested-With': 'XMLHttpRequest' },
              signal: this.aborter.signal
            });
            const html = await res.text();

            const doc = new DOMParser().parseFromString(html, 'text/html');
            const fresh = doc.querySelector('[data-results]');
            const target = this.$refs.results;

            if (fresh && target) {
              target.innerHTML = fresh.innerHTML;
              this.rewirePagination();
            } else {
              window.location.href = this.baseUrl + '?' + this.queryString();
              return;
            }

            if (push) history.replaceState(null, '', this.baseUrl + '?' + this.queryString());
          } catch(e) {
            window.location.href = this.baseUrl + '?' + this.queryString();
          } finally {
            this.loading = false;
            this.aborter = null;
          }
        },

        rewirePagination() {
          const target = this.$refs.results;
          if (!target) return;
          target.querySelectorAll('a[data-page-link], a[href*="page="]').forEach(a => {
            let page = a.dataset.page;
            if (!page) {
              const m = (a.getAttribute('href')||'').match(/[?&]page=(\d+)/);
              if (m) page = m[1];
            }
            if (!page) return;
            a.addEventListener('click', (ev) => {
              ev.preventDefault();
              this.state.page = parseInt(page,10);
              this.update();
            }, { once:true });
          });
        },
      }
    }
  </script>
</x-app-layout>
