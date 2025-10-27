<x-app-layout>
    <style>
        [x-cloak] {
            display: none !important
        }

        .icon-16 {
            width: 16px;
            height: 16px
        }

        .icon-18 {
            width: 18px;
            height: 18px
        }

        .action-btn svg {
            width: 18px;
            height: 18px
        }

        .badge-count {
            font-size: 11px
        }
    </style>

    <style>
  .zigzag{ display:none !important; }
</style>


    <x-slot name="header">
        <div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                {{ $note->faculty->name ?? '—' }} · {{ $note->semester->name ?? '—' }} ·
                {{ $note->subject->name ?? '—' }}
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold">{{ $note->title }}</h1>
        </div>
    </x-slot>

    @php
  // If controller already passed $readonly, keep it. Otherwise compute it.
  $readonly = $readonly ?? !($note->status === 'approved' && $note->is_public && $note->published_at);
@endphp


    <div class="grid lg:grid-cols-3 gap-8" x-data="noteShow({
        liked: @json($liked),
        bookmarked: @json($bookmarked),
        likes: {{ $note->likes_count }},
        bookmarks: {{ $note->bookmarks_count }},
        likeUrl: '{{ route('i.like', $note) }}',
        bookmarkUrl: '{{ route('i.bookmark', $note) }}',
        commentUrl: '{{ route('i.comment', $note) }}',
        reportUrl: '{{ route('i.report', $note) }}',
        csrf: '{{ csrf_token() }}',
    })">
        {{-- LEFT: content --}}
        <article class="lg:col-span-2 card dark:bg-gray-800 dark:border-gray-700">
            <div class="p-5 space-y-6">
               @php
  $ext = strtolower($note->file_ext ?: pathinfo($note->file_path ?? '', PATHINFO_EXTENSION));
@endphp





{{-- =========================
   Media (PDF / DOCX preview)
   ========================= --}}
@php
    // Decide extension & a public URL (e.g., /storage/…)
    $ext = strtolower($note->file_ext ?? pathinfo($note->file_path ?? '', PATHINFO_EXTENSION));
    $fileUrl = $note->file_url
        ?: ($note->file_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($note->file_path) : null);
@endphp

@if ($ext === 'pdf' && $fileUrl)
  <div class="aspect-[4/3] bg-gray-100 dark:bg-gray-700 rounded-xl overflow-hidden ring-1 ring-black/5">
    <iframe src="{{ $fileUrl }}#toolbar=0"
            class="w-full h-full"
            title="PDF Preview"
            loading="lazy"></iframe>
  </div>

@elseif (in_array($ext, ['docx']) && $fileUrl)
  <div class="rounded-xl ring-1 ring-black/5 bg-white">
    <div id="docx-wrap"
         data-src="{{ $fileUrl }}"
         class="p-4 overflow-auto max-h-[70vh] min-h-[320px] text-sm text-gray-500">
      Loading document preview…
    </div>
  </div>

  <script>
    (function () {
      var el = document.getElementById('docx-wrap');
      if (!el) return;

      function showNoPreview() {
        el.innerHTML =
          '<div class="aspect-[4/3] grid place-items-center rounded-xl bg-gray-900/90 text-white">'+
            '<div class="text-center space-y-3">'+
              '<div class="text-base font-medium">No preview available</div>'+
              '<a href="{{ route('public.notes.download', $note) }}" '+
                 'class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-white text-gray-900 ring-1 ring-black/10 shadow">'+
                '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.5" d="M3 16.5v1.125A2.375 2.375 0 0 0 5.375 20h13.25A2.375 2.375 0 0 0 21 17.625V16.5"/><path stroke-width="1.5" d="M12 3v11.25m0 0 3.75-3.75M12 14.25 8.25 10.5"/></svg>'+
                '<span>Download</span>'+
              '</a>'+
            '</div>'+
          '</div>';
      }

      function renderDocx() {
        if (!window.docx) return showNoPreview();

        var src = el.dataset.src;
        // Normalize same-origin path (avoids CORS/cookie issues on localhost)
        try {
          var u = new URL(src, window.location.href);
          if (u.origin === window.location.origin) src = u.pathname + u.search + u.hash;
        } catch(_) {}

        fetch(src, { credentials: 'same-origin' })
          .then(function (res) {
            if (!res.ok) throw new Error('DOCX fetch failed: ' + res.status);
            var ct = res.headers.get('content-type') || '';
            // If the server returns HTML (e.g., error page), don’t try to render
            if (ct.includes('text/html')) throw new Error('Got HTML instead of DOCX');
            return res.arrayBuffer();
          })
          .then(function (buf) {
            return window.docx.renderAsync(buf, el, null, {
              className: 'docx',
              inWrapper: true,
              breakPages: true,
              ignoreWidth: false,
              ignoreHeight: false,
              useBase64URL: false,
              useMathMLPolyfill: true
            });
          })
          .catch(function (e) {
            console.error('DOCX preview error:', e);
            showNoPreview();
          });
      }

      // Load the renderer if not present, then render; otherwise render immediately
      if (window.docx) {
        renderDocx();
      } else {
        var s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/docx-preview@0.3.4/dist/docx-preview.min.js';
        s.defer = true;
        s.onload = renderDocx;
        s.onerror = showNoPreview;
        document.head.appendChild(s);
      }
    })();
  </script>

@elseif (in_array($ext, ['doc']) && $fileUrl)
  <div class="aspect-[4/3] grid place-items-center rounded-xl bg-gray-900/90 text-white">
    <div class="text-center space-y-3">
      <div class="text-base font-medium">Legacy .DOC files cannot be previewed</div>
      <a href="{{ route('public.notes.download', $note) }}"
         class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-white text-gray-900 ring-1 ring-black/10 shadow">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.5" d="M3 16.5v1.125A2.375 2.375 0 0 0 5.375 20h13.25A2.375 2.375 0 0 0 21 17.625V16.5"/><path stroke-width="1.5" d="M12 3v11.25m0 0 3.75-3.75M12 14.25 8.25 10.5"/></svg>
        <span>Download</span>
      </a>
    </div>
  </div>

@elseif ($note->cover_url)
  <img src="{{ $note->cover_url }}" class="w-full rounded-xl ring-1 ring-black/5" alt="Cover image">

@else
  <div class="aspect-[4/3] grid place-items-center rounded-xl bg-gray-900/90 text-white">
    <div class="text-center space-y-3">
      <div class="text-base font-medium">No preview available</div>
      <a href="{{ route('public.notes.download', $note) }}"
         class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-white text-gray-900 ring-1 ring-black/10 shadow">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.5" d="M3 16.5v1.125A2.375 2.375 0 0 0 5.375 20h13.25A2.375 2.375 0 0 0 21 17.625V16.5"/><path stroke-width="1.5" d="M12 3v11.25m0 0 3.75-3.75M12 14.25 8.25 10.5"/></svg>
        <span>Download</span>
      </a>
    </div>
  </div>
@endif







                {{-- Description --}}
                @if (filled($note->description))
                    <div class="prose dark:prose-invert max-w-none">{!! nl2br(e($note->description)) !!}</div>
                @endif

                {{-- Tags --}}
                @if ($note->tags->count())
                    <div class="flex flex-wrap gap-2">
                        @foreach ($note->tags as $t)
                            <a class="chip hover:bg-indigo-100"
                                href="{{ route('public.notes.index', ['tag' => $t->slug]) }}">#{{ $t->name }}</a>
                        @endforeach
                    </div>
                @endif

                {{-- Stats --}}
                <div class="flex flex-wrap items-center gap-6 text-sm text-gray-600 dark:text-gray-400">
                    <span class="inline-flex items-center gap-2">
                        <svg class="icon-16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-width="1.5"
                                d="M2.036 12.322c2.362-4.167 6.014-6.5 9.964-6.5s7.602 2.333 9.964 6.5a.75.75 0 0 1 0 .756c-2.362 4.167-6.014 6.5-9.964 6.5s-7.602-2.333-9.964-6.5a.75.75 0 0 1 0-.756Z" />
                            <circle cx="12" cy="12" r="3" stroke-width="1.5" />
                        </svg>
                        {{ number_format($note->views) }} views
                    </span>

                    <span class="inline-flex items-center gap-2">
                        <svg class="icon-16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-width="1.5"
                                d="M3 16.5v1.125A2.375 2.375 0 0 0 5.375 20h13.25A2.375 2.375 0 0 0 21 17.625V16.5" />
                            <path stroke-width="1.5" d="M12 3v11.25m0 0 3.75-3.75M12 14.25 8.25 10.5" />
                        </svg>
                        {{ number_format($note->downloads) }} downloads
                    </span>

                    <span class="inline-flex items-center gap-2">
                        <svg class="icon-16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-width="1.5"
                                d="M16 2v4M8 2v4M3.75 9.75h16.5M4 6h16a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2Z" />
                        </svg>
                        Published {{ $note->published_at?->diffForHumans() }}
                    </span>
                </div>
            </div>
        </article>

        {{-- RIGHT: author + interactions --}}
        <aside class="lg:col-span-1 space-y-6">
            {{-- Author --}}
            <div class="card p-5 dark:bg-gray-800 dark:border-gray-700">
                <h4 class="font-semibold mb-3">Author</h4>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-300 to-purple-300"></div>
                    <div class="min-w-0">
                        <a href="{{ route('public.notes.index', ['author' => $note->user_id]) }}"
                            class="font-medium hover:text-indigo-600 truncate">
                            {{ $note->user->name }}
                        </a>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Contributor</div>
                    </div>
                </div>
            </div>

            @auth
                {{-- Interact --}}
                <div class="card p-5 dark:bg-gray-800 dark:border-gray-700 space-y-4">
                    <h4 class="font-semibold">Interact</h4>

                    <div class="grid grid-cols-2 gap-2">
                        {{-- LIKE --}}
                        <button type="button" @click="toggleLike" :disabled="busy"
                            class="action-btn inline-flex items-center justify-center gap-2 h-9 rounded-md border text-sm transition ring-1 ring-black/5 shadow-sm px-3 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            :class="liked ? 'bg-indigo-600 text-white border-indigo-600 hover:bg-indigo-700' :
                                'bg-white text-gray-800 border-gray-200'">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-width="1.5"
                                    d="M7.5 11.25V20.25H4.5V11.25H7.5ZM9.75 20.25H15.75C17.4069 20.25 18.75 18.9069 18.75 17.25V10.5c0-1.24264-.7574-2.34686-1.875-2.769L12.75 6.1875V4.5c0-1.24264-1.0074-2.25-2.25-2.25-.714 0-1.362.366-1.725.975L7.5 5.25v6" />
                            </svg>
                            <span x-text="liked ? 'Liked' : 'Like'"></span>
                            <span class="badge-count px-1.5 py-0.5 rounded" :class="liked ? 'bg-white/30' : 'bg-gray-100'">
                                <span x-text="likes"></span>
                            </span>
                        </button>

                        {{-- BOOKMARK --}}
                        <button type="button" @click="toggleBookmark" :disabled="busy"
                            class="action-btn inline-flex items-center justify-center gap-2 h-9 rounded-md border text-sm transition ring-1 ring-black/5 shadow-sm px-3 hover:bg-amber-50 focus:outline-none focus:ring-2 focus:ring-amber-500"
                            :class="bookmarked ? 'bg-amber-500 text-white border-amber-500 hover:bg-amber-600' :
                                'bg-white text-gray-800 border-gray-200'">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-width="1.5"
                                    d="M6.75 3.75h10.5a1.5 1.5 0 0 1 1.5 1.5V21l-7.5-3-7.5 3V5.25a1.5 1.5 0 0 1 1.5-1.5Z" />
                            </svg>
                            <span x-text="bookmarked ? 'Saved' : 'Save'"></span>
                            <span class="badge-count px-1.5 py-0.5 rounded"
                                :class="bookmarked ? 'bg-white/30 text-white/90' : 'bg-gray-100 text-gray-700'">
                                <span x-text="bookmarks"></span>
                            </span>
                        </button>

                        {{-- DOWNLOAD (full width) --}}
                        <form action="{{ route('public.notes.download', $note) }}" method="POST" class="col-span-2">
                            @csrf
                            <button
                                class="w-full inline-flex items-center justify-center gap-2 h-9 rounded-md border text-sm bg-white text-gray-800 border-gray-200 ring-1 ring-black/5 shadow-sm px-3 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                <svg class="icon-18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-width="1.5"
                                        d="M3 16.5v1.125A2.375 2.375 0 0 0 5.375 20h13.25A2.375 2.375 0 0 0 21 17.625V16.5" />
                                    <path stroke-width="1.5" d="M12 3v11.25m0 0 3.75-3.75M12 14.25 8.25 10.5" />
                                </svg>
                                <span>Download</span>
                            </button>
                        </form>
                    </div>

                    {{-- Report (AJAX) --}}
                    <details class="group mt-4">
                        <summary
                            class="text-sm text-gray-600 dark:text-gray-300 cursor-pointer select-none inline-flex items-center gap-2 hover:text-gray-900 dark:hover:text-white">
                            <svg class="icon-18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-width="1.5" d="M4.5 3v18M5 4h10l-1.5 3H19l-2 4h-9.5" />
                            </svg>
                            Report an issue
                        </summary>

                        <form class="mt-2 flex gap-2" @submit.prevent="postReport($event)">
                            @csrf
                            <input type="text" name="reason" class="input flex-1" placeholder="Reason…" required>
                            <button class="btn-danger h-9">Report</button>
                        </form>
                    </details>
                </div>

                {{-- Comments (AJAX) --}}
                <div class="card p-5 dark:bg-gray-800 dark:border-gray-700">
                    <h4 class="font-semibold mb-3">Comments</h4>

                    <form class="mb-4" @submit.prevent="postComment($event)">
                        @csrf
                        <textarea name="body" rows="3" class="textarea" placeholder="Write a comment…" required></textarea>
                        <div class="mt-2">
                            <button class="btn-primary h-9">Post Comment</button>
                        </div>
                    </form>

                    <div class="space-y-4" id="comments-list">
                        @forelse($note->comments as $c)
                            <div class="p-3 border rounded-xl dark:border-gray-700">
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $c->user->name }} ·
                                    {{ $c->created_at->diffForHumans() }}</div>
                                <div class="mt-1">{{ $c->body }}</div>
                            </div>
                        @empty
                            <p class="text-gray-500">No comments yet.</p>
                        @endforelse
                    </div>
                </div>
            @endauth

            {{-- Related --}}
            @if ($related->count())
                <div class="card p-5 dark:bg-gray-800 dark:border-gray-700">
                    <h4 class="font-semibold mb-3">Related Notes</h4>
                    <ul class="space-y-3 text-sm">
                        @foreach ($related as $r)
                            <li>
                                <a href="{{ route('public.notes.show', $r) }}"
                                    class="hover:text-indigo-600">{{ $r->title }}</a>
                                <div class="text-xs text-gray-500">{{ $r->faculty->name ?? '—' }} ·
                                    {{ $r->semester->name ?? '—' }}</div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </aside>

        {{-- Toast (top-right, within same x-data) --}}
        <div x-cloak x-show="toast.show" x-transition.opacity x-transition.scale.origin.top.right
            class="fixed top-4 right-4 z-50 pointer-events-none" aria-live="polite">
            <div class="pointer-events-auto flex items-center gap-2 rounded-lg px-4 py-2 shadow-lg ring-1 ring-black/10"
                :class="{
                    'bg-emerald-600 text-white': toast.type === 'ok',
                    'bg-amber-500 text-white': toast.type === 'warn',
                    'bg-rose-600 text-white': toast.type === 'error',
                }">
                <svg x-show="toast.type === 'ok'" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M20 6 9 17l-5-5" />
                </svg>
                <svg x-show="toast.type !== 'ok'" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v4m0 4h.01M12 2l9 20H3L12 2z" />
                </svg>
                <span class="text-sm font-medium" x-text="toast.text"></span>
            </div>
        </div>
    </div>

    <script>
        function noteShow({
            liked,
            bookmarked,
            likes,
            bookmarks,
            likeUrl,
            bookmarkUrl,
            commentUrl,
            reportUrl,
            csrf
        }) {
            return {
                liked,
                bookmarked,
                likes,
                bookmarks,
                likeUrl,
                bookmarkUrl,
                commentUrl,
                reportUrl,
                csrf,
                busy: false,
                toast: {
                    show: false,
                    text: '',
                    type: 'ok',
                    showAs(msg, type = 'ok') {
                        this.text = msg;
                        this.type = type;
                        this.show = true;
                        setTimeout(() => this.show = false, 1600)
                    },
                    ok(msg) {
                        this.showAs(msg, 'ok')
                    },
                    warn(msg) {
                        this.showAs(msg, 'warn')
                    },
                    error(msg) {
                        this.showAs(msg, 'error')
                    },
                },

                async post(url, body = {}) {
                    this.busy = true;
                    try {
                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': this.csrf,
                                'Accept': 'application/json',
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: new URLSearchParams(body),
                            credentials: 'same-origin',
                        });
                        return await res.json();
                    } catch (e) {
                        console.error(e);
                        return {
                            ok: false
                        };
                    } finally {
                        this.busy = false;
                    }
                },

                async toggleLike() {
                    const data = await this.post(this.likeUrl);
                    if (data?.ok) {
                        this.liked = !!data.liked;
                        if (typeof data.likes === 'number') this.likes = data.likes;
                        this.toast.ok(data.message || 'Like updated');
                    } else {
                        this.toast.error('Could not update like');
                    }
                },

                async toggleBookmark() {
                    const data = await this.post(this.bookmarkUrl);
                    if (data?.ok) {
                        this.bookmarked = !!data.bookmarked;
                        if (typeof data.bookmarks === 'number') this.bookmarks = data.bookmarks;
                        this.toast.ok(data.message || 'Bookmark updated');
                    } else {
                        this.toast.error('Could not update bookmark');
                    }
                },

                async postComment(ev) {
                    const form = ev.target;
                    const body = form.body.value.trim();
                    if (!body) return;

                    const data = await this.post(this.commentUrl, {
                        body
                    });
                    if (data?.ok) {
                        this.toast.ok(data.message || 'Comment posted');

                        const list = document.getElementById('comments-list');
                        // remove "No comments yet." if present
                        if (list.firstElementChild && list.firstElementChild.tagName === 'P') {
                            list.firstElementChild.remove();
                        }
                        list.insertAdjacentHTML('afterbegin', `
                            <div class="p-3 border rounded-xl dark:border-gray-700">
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    ${data.user_name} · just now
                                </div>
                                <div class="mt-1">${escapeHtml(data.body)}</div>
                            </div>
                        `);
                        form.reset();
                    } else {
                        this.toast.error('Could not post comment');
                    }
                },

                async postReport(ev) {
                    const form = ev.target;
                    const reason = form.reason.value.trim();
                    if (!reason) return;

                    const data = await this.post(this.reportUrl, {
                        reason
                    });
                    if (data?.ok) {
                        this.toast.ok(data.message || 'Report submitted');
                        form.reset();
                    } else {
                        this.toast.error('Could not submit report');
                    }
                },
            }
        }

        // Tiny escape helper to avoid injecting raw HTML into comments
        function escapeHtml(str) {
            const p = document.createElement('p');
            p.innerText = str;
            return p.innerHTML;
        }
    </script>
</x-app-layout>
