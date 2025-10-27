<x-app-layout>

    <style>
  .zigzag{ display:none !important; }
</style>

    {{-- HEADER --}}
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl md:text-3xl font-extrabold">Edit Note</h1>
            <a href="{{ route('notes.mine', $note) }}" class="text-sm text-white-600 hover:text-indigo-800">← Back
                to note</a>
        </div>
    </x-slot>

    <style>
        .section-rule {
            margin: 1.75rem 0;
            height: 1px;
            background: linear-gradient(90deg, rgba(0, 0, 0, 0), rgba(17, 24, 39, .15), rgba(0, 0, 0, 0))
        }
    </style>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <form action="{{ route('notes.update', $note) }}" method="POST" enctype="multipart/form-data"
            x-data="editNoteUI({
                title: @js(old('title', $note->title)),
                desc: @js(old('description', $note->description)),
                maxTitle: 255,
                maxDesc: 5000,
                coverSrc: @js($note->cover_url),
                isPublic: {{ old('is_public', $note->is_public) ? 'true' : 'false' }},
            })" class="relative">
            @csrf
            @method('PUT')

            <div class="rounded-2xl overflow-hidden ring-1 ring-black/10 shadow-sm bg-white">
                {{-- Top (compact) --}}
                <div class="px-6 py-4 border-b bg-gray-50/60">
                    <div class="flex flex-wrap items-center gap-3">
                        @php $isApproved = $note->status==='approved' && $note->is_public && $note->published_at; @endphp
                        <div class="text-sm">
                            <span class="font-semibold text-gray-900">Status:</span>
                            @if ($isApproved)
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">Published</span>
                                @if ($note->published_at)
                                    <span class="text-gray-600">· {{ $note->published_at->diffForHumans() }}</span>
                                @endif
                            @elseif($note->status === 'pending')
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-200">Pending
                                    review</span>
                            @elseif($note->status === 'rejected')
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 border border-rose-200">Rejected</span>
                                @if (filled($note->reject_reason))
                                    <details class="inline-block ml-2">
                                        <summary
                                            class="cursor-pointer underline decoration-dotted text-rose-700 hover:text-rose-900 text-sm">
                                            Reason</summary>
                                        <div
                                            class="mt-2 rounded-xl border border-rose-200 bg-rose-50 p-3 text-rose-900 text-sm leading-relaxed max-w-xl">
                                            {{ $note->reject_reason }}</div>
                                    </details>
                                @endif
                            @endif
                        </div>

                        <label class="ml-auto inline-flex items-center gap-2 text-sm select-none">
                            <input type="checkbox" name="is_public" value="1" x-model="isPublic">
                            <span>Public</span>
                        </label>
                    </div>
                </div>

                {{-- BODY --}}
                <div class="p-6 md:p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                        {{-- MAIN --}}
                        <section class="lg:col-span-8">
                            {{-- Basic Info --}}
                            <header class="mb-4">
                                <h2 class="text-base font-extrabold text-gray-900">Basic Info</h2>
                                <p class="text-sm text-gray-500 mt-1">Title first, then a short summary.</p>
                            </header>

                            <div class="grid gap-5">
                                <div>
                                    <div class="flex items-end justify-between">
                                        <label class="block text-sm font-extrabold text-gray-900 mb-1">Title</label>
                                        <span class="text-xs text-gray-500"
                                            x-text="`${title.length}/${maxTitle}`"></span>
                                    </div>
                                    <input type="text" name="title" x-model="title" maxlength="255"
                                        class="input w-full text-[15px] h-11 px-3"
                                        placeholder="e.g. Management – Unit 3 concise notes" required>
                                    @error('title')
                                        <div class="text-sm text-rose-600 mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <br>
                                <div>
                                    <div class="flex items-end justify-between">
                                        <label
                                            class="block text-sm font-extrabold text-gray-900 mb-1">Description</label>
                                        <span class="text-xs text-gray-500" x-text="`${desc.length}/${maxDesc}`"></span>
                                    </div>
                                    <textarea name="description" x-model="desc" rows="6" maxlength="5000"
                                        class="textarea w-full text-[15px] leading-relaxed" placeholder="What’s inside, who it’s for, and how to use it…"></textarea>
                                    @error('description')
                                        <div class="text-sm text-rose-600 mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="section-rule" aria-hidden="true"></div>

                            {{-- Metadata --}}
                            <header class="mb-4">
                                <h2 class="text-base font-extrabold text-gray-900">Metadata</h2>
                                <p class="text-sm text-gray-500 mt-1">Tag the note so others can find it easily.</p>
                            </header>
                            <br>
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-extrabold text-gray-900 mb-1">Faculty</label>
                                    <select name="faculty_id" class="input w-full h-11 px-3 text-sm">
                                        <option value="">—</option>
                                        @foreach ($faculties as $f)
                                            <option value="{{ $f->id }}" @selected(old('faculty_id', $note->faculty_id) == $f->id)>
                                                {{ $f->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <br>
                                <div>
                                    <label class="block text-sm font-extrabold text-gray-900 mb-1">Semester</label>
                                    <select name="semester_id" class="input w-full h-11 px-3 text-sm">
                                        <option value="">—</option>
                                        @foreach ($semesters as $s)
                                            <option value="{{ $s->id }}" @selected(old('semester_id', $note->semester_id) == $s->id)>
                                                {{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <br>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-extrabold text-gray-900 mb-1">Subject</label>
                                    <select name="subject_id" class="input w-full h-11 px-3 text-sm">
                                        <option value="">—</option>
                                        @foreach ($subjects as $s)
                                            <option value="{{ $s->id }}" @selected(old('subject_id', $note->subject_id) == $s->id)>
                                                {{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <br>
                                {{-- TAGS (chips) --}}
                                <div class="sm:col-span-2" x-data="{
                                    tInput: '',
                                    tags: @js(($note->tags ?? collect())->pluck('name') ?? []),
                                    add() { const v = this.tInput.trim(); if (!v) return; if (!this.tags.includes(v)) this.tags.push(v);
                                        this.tInput = ''; },
                                    remove(i) { this.tags.splice(i, 1); }
                                }">
                                    <label class="block text-sm font-extrabold text-gray-900 mb-1">Tags</label>

                                    <div class="flex flex-wrap gap-2 rounded-md border px-2 py-2">
                                        <template x-for="(t,i) in tags" :key="i">
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2 py-1 text-xs ring-1 ring-inset ring-gray-200">
                                                <span x-text="t"></span>
                                                <button type="button" class="text-gray-500 hover:text-gray-800"
                                                    @click="remove(i)">✕</button>
                                            </span>
                                        </template>

                                        <input x-model="tInput" @keydown.enter.prevent="add()"
                                            @keydown.,.prevent="add()" {{-- comma adds too --}}
                                            class="flex-1 min-w-[140px] focus:outline-none text-sm"
                                            placeholder="Type and press Enter">
                                    </div>

                                    {{-- Post as tags[] --}}
                                    <template x-for="t in tags">
                                        <input type="hidden" name="tags[]" :value="t">
                                    </template>

                                    <p class="mt-1 text-xs text-gray-500">Press <strong>Enter</strong> (or comma) to
                                        add. Click ✕ to remove.</p>
                                </div>

                            </div>

                            <div class="section-rule" aria-hidden="true"></div>

                            {{-- Files & Cover (balanced / compact) --}}
                            {{-- Files & Cover (compact, locked height) --}}
                            <header class="mb-2">
                                <h2 class="text-sm font-extrabold text-gray-900">Files & Cover</h2>
                            </header>

                            <div class="grid gap-3 md:grid-cols-2">

                                {{-- FILE — compact row, no tall zone --}}
                                <div class="rounded border bg-white p-3 ring-1 ring-black/5">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-[13px] font-semibold text-gray-900">File</h3>
                                        <div class="flex items-center gap-2 text-[11px]">
                                            <span
                                                class="inline-flex items-center rounded px-1.5 py-0.5 bg-gray-50 ring-1 ring-inset ring-gray-200 text-gray-700">
                                                {{ strtoupper($note->file_ext ?? '—') }}
                                            </span>
                                            @if ($note->file_url)
                                                <a class="text-indigo-600 hover:text-indigo-800"
                                                    href="{{ $note->file_url }}" target="_blank"
                                                    rel="noopener">Open</a>
                                            @endif
                                        </div>
                                    </div>

                                    <button type="button" @click="onFileClick()" @dragover.prevent="dzActive = true"
                                        @dragleave.prevent="dzActive = false" @drop.prevent="onDropFile($event)"
                                        class="mt-2 w-full h-9 rounded border border-dashed text-left px-3 text-[12px] flex items-center gap-2 transition"
                                        :class="dzActive ? 'border-indigo-400 bg-indigo-50/40' :
                                            'hover:border-indigo-300 hover:bg-indigo-50/30'">
                                        <svg class="w-3.5 h-3.5 text-gray-500" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor">
                                            <path stroke-width="1.5"
                                                d="M12 16V4m0 0l-4 4m4-4l4 4M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" />
                                        </svg>
                                        <span><span class="font-medium text-indigo-700">Choose</span> or drag &
                                            drop</span>
                                        <span class="ml-auto text-[11px] text-gray-500">PDF/DOC/DOCX/PPT/PPTX ·
                                            ≤20MB</span>
                                    </button>

                                    <template x-if="fileName">
                                        <div
                                            class="mt-2 flex items-center justify-between rounded bg-white px-2 py-1 text-[12px] ring-1 ring-inset ring-gray-200">
                                            <span class="truncate" x-text="fileName"></span>
                                            <button type="button" class="text-gray-500 hover:text-gray-800"
                                                @click="clearSelected()">Clear</button>
                                        </div>
                                    </template>

                                    {{-- keep the real input hidden to avoid a second native button --}}
                                    <input x-ref="file" type="file" name="file"
                                        accept=".pdf,.doc,.docx,.ppt,.pptx" class="sr-only" style="display:none"
                                        @change="onFileChange($event)">
                                    <br>

                                    <div class="mt-2 text-[11px] text-gray-600">
                                        - Keep names short & descriptive.

                                        <br>- Upload the finalized document.
                                    </div>

                                    @error('file')
                                        <div class="text-xs text-rose-600 mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- COVER — small, fixed preview (cannot stretch) --}}
                                <div class="rounded border bg-white p-3 ring-1 ring-black/5">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-[13px] font-semibold text-gray-900">Cover</h3>
                                        <span class="text-[11px] text-gray-500">3:2 · JPG/PNG/WebP · ≤4MB</span>
                                    </div>

                                    <div class="mt-2 flex items-center gap-3">
                                        {{-- force exact size with inline CSS so it never elongates --}}
                                        <img :src="coverSrc || '{{ asset('images/placeholder-4x3.png') }}'"
                                            alt="Cover preview" class="block rounded ring-1 ring-black/5 bg-gray-50"
                                            style="width:160px;height:180px;object-fit:cover;object-position:center;">

                                        <div class="flex flex-col gap-1">
                                            <button type="button" @click="$refs.cover.click()"
                                                class="inline-flex items-center gap-1.5 rounded border px-2 py-1 text-[12px] hover:bg-gray-50">
                                                Change
                                            </button>
                                            <button type="button" x-show="coverSrc" @click="removeCover()"
                                                class="inline-flex items-center gap-1.5 rounded border px-2 py-1 text-[12px] hover:bg-gray-50">
                                                Remove
                                            </button>
                                        </div>
                                    </div>

                                    <input x-ref="cover" type="file" name="cover" accept="image/*"
                                        class="sr-only" style="display:none" @change="previewCover($event)">

                                    @error('cover')
                                        <div class="text-xs text-rose-600 mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="section-rule" aria-hidden="true"></div>

                            <p class="text-xs text-gray-500">
                                Making it <span class="font-semibold">Public</span> allows it to be listed, but it will
                                only appear to everyone after admin approval.
                            </p>
                        </section>

                        {{-- SIDEBAR --}}
                        <aside class="lg:col-span-4">
                            <div class="rounded-xl border bg-white p-5 ring-1 ring-black/5">
                                <h3 class="text-base font-extrabold text-gray-900 mb-2">Tips</h3>
                                <ul class="space-y-2 text-sm text-gray-700 list-disc pl-5 leading-relaxed">
                                    <li>Clear, searchable titles perform best.</li>
                                    <li>“Public” notes still require admin approval to appear for everyone.</li>
                                    <li>Use the description to highlight what’s inside.</li>
                                </ul>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>

            {{-- ACTION BAR --}}
            <div class="mt-6">
                <div
                    class="mx-auto max-w-6xl rounded-xl border bg-white/90 backdrop-blur px-5 py-4 ring-1 ring-black/5 shadow">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('public.notes.show', $note) }}"
                            class="inline-flex items-center justify-center h-10 px-4 rounded-md border text-sm bg-white hover:bg-gray-50">Cancel</a>
                        <button class="btn-primary h-10 px-5">Save Changes</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function editNoteUI({
            title,
            desc,
            maxTitle,
            maxDesc,
            coverSrc,
            isPublic
        }) {
            return {
                title,
                desc,
                maxTitle,
                maxDesc,
                coverSrc,
                isPublic,
                dzActive: false,
                fileName: '',
                onFileClick() {
                    this.$refs.file?.click()
                },
                onFileChange(e) {
                    this.fileName = e.target.files?.[0]?.name || ''
                },
                onDropFile(e) {
                    const f = e.dataTransfer?.files?.[0];
                    if (!f) return;
                    this.$refs.file.files = e.dataTransfer.files;
                    this.fileName = f.name;
                    this.dzActive = false;
                },
                clearSelected() {
                    this.fileName = '';
                    if (this.$refs.file) this.$refs.file.value = '';
                },
                previewCover(e) {
                    const f = e.target.files?.[0];
                    if (!f) return;
                    this.coverSrc = URL.createObjectURL(f);
                },
                removeCover() {
                    this.coverSrc = null;
                    if (this.$refs.cover) this.$refs.cover.value = '';
                },
            }
        }
    </script>
</x-app-layout>
