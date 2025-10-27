<x-app-layout>

  <style>
  .zigzag{ display:none !important; }
</style>

    {{-- HERO --}}
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <h1 class="text-2xl md:text-3xl font-extrabold grow">My Notes</h1>
            <a href="{{ route('notes.create') }}" class="ml-auto shrink-0 btn-primary inline-flex items-center gap-2">
                <span class="text-lg">＋</span><span>Upload New</span>
            </a>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <div
        class="space-y-6"
        x-data="notesTable({
          indexBase: {{ ($notes->currentPage() - 1) * $notes->perPage() }},
          listUrl: '{{ route('notes.mine') }}',
          baseParams: @js(request()->except(['page', 'q', 'status']))
        })"
      >
        @if ($notes->count() === 0)
            <div class="rounded-xl border bg-white p-10 text-center">
                <div class="text-3xl mb-2">🗒️</div>
                <h2 class="text-lg font-semibold">No notes yet</h2>
                <p class="text-gray-600 mt-1">Upload your first note to get started.</p>
                <a href="{{ route('notes.create') }}" class="btn-primary inline-flex items-center gap-2 mt-4">
                    <span class="text-lg">＋</span><span>Upload Note</span>
                </a>
            </div>
        @else
            {{-- LIST CARD --}}
            <div class="rounded-xl border bg-white overflow-hidden">

                {{-- Toolbar --}}
                <div class="flex flex-wrap items-center gap-3 p-3 bg-gray-50 border-b">
                    {{-- Left: Search --}}
                    <div class="relative">
                        <input
                          x-model.debounce.200ms="q"
                          type="text"
                          placeholder="Search my notes…"
                          class="h-9 w-56 md:w-72 rounded-md border border-gray-300 pl-10 pr-9 text-sm focus:ring-2 focus:ring-indigo-500"
                          aria-label="Search my notes"
                        >
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">🔎</span>
                        <button
                          x-show="q"
                          @click="q=''"
                          type="button"
                          class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-800"
                          aria-label="Clear search"
                        >✕</button>
                    </div>

                    {{-- Right: Status + count --}}
                    <div class="ml-auto flex items-center gap-3">
                        @php $status = request('status'); @endphp
                        <select
                          x-model="status"
                          @change="applyStatus"
                          class="h-9 w-40 md:w-48 rounded-md border border-gray-300 px-3 pr-10 text-sm bg-white"
                          aria-label="Filter status"
                        >
                            <option value="" {{ $status === null ? 'selected' : '' }}>All</option>
                            <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Published</option>
                            <option value="pending"  {{ $status === 'pending'  ? 'selected' : '' }}>Pending</option>
                            <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>

                        <div class="text-xs text-gray-600">
                            Showing <span x-text="visibleCount"></span> of {{ $notes->count() }} on this page
                        </div>
                    </div>
                </div>

                {{-- Full-width table + pinned-right Actions --}}
                <table class="w-full min-w-full divide-y divide-gray-200 table-fixed">
                    <colgroup>
                        <col style="width:64px">  {{-- S.N --}}
                        <col>                      {{-- Title (flex) --}}
                        <col style="width:300px">  {{-- Actions --}}
                    </colgroup>

                    <thead class="bg-gray-50">
                        <tr class="text-[11px] md:text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="pl-4 pr-2 py-3 text-left align-top">S.N</th>
                            <th class="pl-10 pr-6 py-3 text-left align-top" style="text-align:left;padding-left:2.5rem">
                                Title
                            </th>
                            <th class="pl-2 pr-4 py-3 text-right align-top">Actions</th>
                        </tr>
                    </thead>

                    <tbody id="notes-rows" class="divide-y divide-gray-100">
                        @foreach ($notes as $note)
                            @php
                                $approved  = $note->status === 'approved' && $note->is_public && $note->published_at;
                                $rowStatus = $approved ? 'approved' : $note->status;
                                $rowIndex  = ($notes->currentPage() - 1) * $notes->perPage() + $loop->iteration;
                            @endphp

                            <tr
                              x-show="rowVisible($el)"
                              x-init="$nextTick(() => recount())"
                              data-title="{{ $note->title }}"
                              data-meta="{{ $note->faculty->name ?? '' }} {{ $note->semester->name ?? '' }} {{ $note->subject->name ?? '' }}"
                              data-status="{{ $rowStatus }}"
                              class="hover:bg-gray-50"
                            >
                                {{-- S.N --}}
                                <td class="px-4 py-3 align-top text-sm text-gray-500 tabular-nums">
                                    {{ $rowIndex }}
                                </td>

                                {{-- Title + status --}}
                                <td class="pl-10 pr-4 py-3 align-top">
                                    <div class="font-medium truncate" title="{{ $note->title }}">
                                        <a href="{{ route('public.notes.show', $note) }}" class="hover:text-indigo-600">
                                            {{ Str::limit($note->title, 80) }}
                                        </a>
                                    </div>

                                    <div class="mt-1 flex flex-wrap items-center gap-2 text-xs">
                                        @if ($approved)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                Published
                                            </span>
                                            @if ($note->published_at)
                                                <span class="text-gray-500">· {{ $note->published_at->diffForHumans() }}</span>
                                            @endif
                                        @elseif($note->status === 'pending')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                                                Pending review
                                            </span>
                                        @elseif($note->status === 'rejected')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 border border-rose-200">
                                                Rejected
                                            </span>
                                            @if (filled($note->reject_reason))
                                                <details class="inline-block ml-1">
                                                    <summary class="underline decoration-dotted cursor-pointer text-rose-700 hover:text-rose-900">
                                                        Reason
                                                    </summary>
                                                    <div class="mt-2 rounded-xl border border-rose-200 bg-rose-50 p-3 text-rose-900">
                                                        <div class="text-sm leading-relaxed">{{ $note->reject_reason }}</div>
                                                        <div class="mt-3">
                                                            <a href="{{ route('notes.edit', $note) }}"
                                                               class="inline-flex items-center rounded-md bg-gray-900 px-3 py-1.5 text-white text-xs hover:bg-gray-800">
                                                                Edit & Resubmit
                                                            </a>
                                                        </div>
                                                    </div>
                                                </details>
                                            @endif
                                        @endif
                                    </div>
                                </td>

                                {{-- Actions (reverted to your previous style) --}}
                                <td class="px-4 py-3 pr-4 align-top text-right">
                                    <div class="w-full flex items-center justify-end gap-2">
                                        <a href="{{ route('notes.edit', $note) }}"
                                           class="inline-flex items-center h-9 px-3 rounded-md border text-sm bg-white hover:bg-gray-50">
                                            Edit
                                        </a>

                                        <a href="{{ route('public.notes.show', $note) }}"
                                           @unless ($approved) title="Read-only until approval" @endunless
                                           class="inline-flex items-center h-9 px-3 rounded-md border text-sm bg-white hover:bg-gray-50">
                                            {{ $approved ? 'View' : 'Review' }}
                                        </a>

                                        <button
                                          type="button"
                                          @click="openDel('{{ route('notes.destroy', $note) }}', @js($note->title))"
                                          class="inline-flex items-center h-9 px-3 rounded-md border text-sm bg-rose-600 text-white hover:bg-rose-700"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $notes->links() }}
            </div>
        @endif

        {{-- Hidden DELETE form (used by modal) --}}
        <form x-ref="deleteForm" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

        {{-- Delete Modal --}}
        <div
          x-show="delOpen"
          x-cloak
          class="fixed inset-0 z-50 flex items-center justify-center p-4"
          role="dialog"
          aria-modal="true"
          aria-labelledby="delTitle"
          @keydown.escape.window="closeDel()"
          x-trap.noscroll="delOpen"
        >
          <div class="absolute inset-0 bg-black/50" @click="closeDel()"></div>

          <div
            class="relative z-10 w-[92vw] sm:w-full sm:max-w-sm md:max-w-md rounded-xl bg-white p-5 ring-1 ring-black/10 shadow-xl"
            x-transition
          >
            <h3 id="delTitle" class="text-base font-extrabold text-gray-900">Delete this note?</h3>
            <p class="mt-2 text-sm text-gray-600">
              You’re about to permanently delete:
              <span class="font-medium text-gray-900" x-text="delTitle"></span>
            </p>

            <div class="mt-5 flex items-center justify-end gap-2">
              <button
                type="button"
                class="inline-flex items-center h-9 px-4 rounded-md border text-sm bg-white hover:bg-gray-50"
                @click="closeDel()"
              >Cancel</button>

              <button
                type="button"
                class="inline-flex items-center h-9 px-4 rounded-md text-sm bg-rose-600 text-white hover:bg-rose-700"
                @click="confirmDel()"
              >Delete</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>
      function notesTable({ indexBase = 0, listUrl, baseParams }) {
        return {
          q: new URLSearchParams(window.location.search).get('q') || '',
          status: new URLSearchParams(window.location.search).get('status') || '',
          visibleCount: 0,

          delOpen: false,
          delUrl: '',
          delTitle: '',

          init() {
            this.$nextTick(() => this.recount());
            this.$watch('q', () => this.recount());
          },

          normalize(s) {
            return (s || '').toString().toLowerCase().trim();
          },

          rowVisible(el) {
            const term = this.normalize(this.q);
            if (!term) return true;
            const t = this.normalize(el.dataset.title);
            const m = this.normalize(el.dataset.meta);
            return t.includes(term) || m.includes(term);
          },

          recount() {
            const rows = this.$root.querySelectorAll('#notes-rows > tr');
            let c = 0;
            rows.forEach(r => {
              const show = this.rowVisible(r);
              r.style.display = show ? '' : 'none';
              if (show) c++;
            });
            this.visibleCount = c;
          },

          applyStatus() {
            const params = new URLSearchParams(baseParams || {});
            if (this.status) params.set('status', this.status);
            const term = this.q.trim();
            if (term) params.set('q', term);
            window.location.href = listUrl + (params.toString() ? ('?' + params.toString()) : '');
          },

          openDel(url, title) {
            this.delUrl = url;
            this.delTitle = title || 'this note';
            this.delOpen = true;
          },
          closeDel() {
            this.delOpen = false;
            this.delUrl = '';
            this.delTitle = '';
          },
          confirmDel() {
            if (!this.delUrl) return;
            this.$refs.deleteForm.setAttribute('action', this.delUrl);
            this.$refs.deleteForm.submit();
          },
        }
      }
    </script>
</x-app-layout>
