<x-app-layout>
    <x-slot name="header">
        <div class="max-w-6xl mx-auto px-4 lg:px-6 flex items-center justify-between">
            <h2 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight">Upload a New Note</h2>
            <a href="{{ route('public.notes.index') }}" class="btn-secondary inline-flex items-center">← Back</a>
        </div>
    </x-slot>

    @if ($errors->any())
        <div class="max-w-6xl mx-auto px-4 lg:px-6 mt-6 bg-rose-50 border border-rose-200 text-rose-700 rounded-lg p-3">
            <ul class="list-disc ml-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="max-w-6xl mx-auto py-10 px-4 lg:px-6" x-data="noteUploader()">
        {{-- One form wraps both columns --}}
        <form id="noteForm" action="{{ route('notes.store') }}" method="POST" enctype="multipart/form-data"
            class="space-y-8">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:items-stretch"> {{-- Left: Details --}}
                <div class="lg:col-span-2 lg:h-full">
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 lg:h-full lg:flex lg:flex-col">
                        <div class="px-6 py-5 border-b border-gray-100">
                            <h3 class="text-base md:text-lg font-semibold text-gray-900">Details</h3>
                            <p class="text-sm text-gray-500">Give your note a clear title and metadata so others can
                                find it.</p>
                        </div>

                        <div class="p-6 space-y-6 lg:grow">
                            {{-- Title --}}
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Title <span
                                        class="text-rose-600">*</span></label>
                                <input type="text" name="title" required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="e.g., Discrete Mathematics – Set Theory" value="{{ old('title') }}">
                                @error('title')
                                    <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Description</label>
                                <textarea name="description" rows="4"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="What’s covered in these notes? Key chapters, tips, exam pointers…">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Meta --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div>
                                    <label class="block mb-1 text-sm font-medium text-gray-700">Faculty</label>
                                    <select name="faculty_id"
                                        class="block w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">—</option>
                                        @foreach ($faculties as $f)
                                            <option value="{{ $f->id }}" @selected(old('faculty_id') == $f->id)>
                                                {{ $f->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block mb-1 text-sm font-medium text-gray-700">Semester</label>
                                    <select name="semester_id"
                                        class="block w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">—</option>
                                        @foreach ($semesters as $s)
                                            <option value="{{ $s->id }}" @selected(old('semester_id') == $s->id)>
                                                {{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block mb-1 text-sm font-medium text-gray-700">Subject</label>
                                    <select name="subject_id"
                                        class="block w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">—</option>
                                        @foreach ($subjects as $s)
                                            <option value="{{ $s->id }}" @selected(old('subject_id') == $s->id)>
                                                {{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Tags --}}
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Tags</label>
                                <input name="tags" value="{{ old('tags') }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="algorithm, programming, exam">
                                <p class="text-xs text-gray-500 mt-1">Comma-separated. Used for search &
                                    recommendations.</p>
                            </div>

                            {{-- Public toggle --}}
                            <div class="flex items-center gap-2">
                                <input id="is_public" type="checkbox" name="is_public" value="1"
                                    {{ old('is_public', 1) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <label for="is_public" class="text-sm text-gray-700">Make this note public</label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: Upload widgets (inside the same form) --}}
                <div class="space-y-8">
                    {{-- Note file dropzone --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-3">Attach your note file</h3>
                        <div class="text-sm text-gray-500 mb-4">PDF, DOCX, PPTX up to 20MB.</div>

                        <div x-ref="dropArea" @dragover.prevent="onDrag = true" @dragleave.prevent="onDrag = false"
                            @drop.prevent="handleDrop($event)" class="relative rounded-xl border-2 border-dashed"
                            :class="onDrag ? 'border-indigo-400 bg-indigo-50' : 'border-gray-300 bg-gray-50'">

                            {{-- File input overlays the dropzone --}}
                            <input type="file" name="file" accept=".pdf,.doc,.docx,.ppt,.pptx"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                @change="handleFile($event)">

                            <div class="px-6 py-10 text-center">
                                <div
                                    class="mx-auto w-16 h-16 rounded-full bg-indigo-600/10 text-indigo-700 grid place-items-center mb-4 text-2xl">
                                    📄
                                </div>
                                <p class="font-medium text-gray-900">Drag & drop your note here</p>
                                <p class="text-sm text-gray-500">or click to browse</p>
                            </div>
                        </div>
                        @error('file')
                            <p class="text-sm text-rose-600 mt-2">{{ $message }}</p>
                        @enderror

                        {{-- Preview --}}
                        <template x-if="file">
                            <div class="mt-4 p-4 rounded-lg border border-gray-200 bg-gray-50 flex items-start gap-3">
                                <div class="text-2xl">📄</div>
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-900 truncate" x-text="file.name"></p>
                                    <p class="text-xs text-gray-500"><span x-text="prettySize"></span> · <span
                                            x-text="fileExt.toUpperCase()"></span></p>
                                </div>
                                <button type="button" class="ml-auto text-sm text-rose-600 hover:text-rose-700"
                                    @click="clearFile()">Remove</button>
                            </div>
                        </template>

                        <p class="text-xs text-gray-500 mt-3">Tip: descriptive filenames improve discovery (e.g.,
                            <em>DSA_Unit2_Trees.pdf</em>).</p>
                    </div>

                    {{-- Cover image (optional) --}}
                   <div x-data="fixedCover()" class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
  <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-3">
    Cover image <span class="text-sm text-gray-500">(optional)</span>
  </h3>

  <div class="flex items-start gap-4">
    {{-- Fixed-size 3:4 preview --}}
    <div class="w-24 h-32 rounded-lg bg-gray-100 border border-gray-200 overflow-hidden grid place-items-center">
      <img x-show="coverUrl" :src="coverUrl" alt="Cover preview" class="w-full h-full object-cover" />
      <span x-show="!coverUrl" class="text-2xl text-gray-400">🖼️</span>
    </div>

    <div class="flex-1">
      <input
        x-ref="coverInput"
        type="file"
        name="cover"
        accept="image/*"
        @change="handleCover($event)"
        class="block w-full text-sm text-gray-700 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-200 file:text-gray-800 hover:file:bg-gray-300"
      >
      @error('cover')
        <p class="text-sm text-rose-600 mt-2">{{ $message }}</p>
      @enderror
      <template x-if="error">
        <p class="text-sm text-rose-600 mt-2" x-text="error"></p>
      </template>
      <p class="text-xs text-gray-500 mt-1">
        Will be saved as fixed <strong>1200×1600</strong> (3:4), up to 4MB.
      </p>
    </div>
  </div>
</div>

<script>
function fixedCover() {
  return {
    // preview + error state
    coverUrl: null,
    error: '',

    // target fixed size (3:4)
    targetW: 1200,
    targetH: 1600,

    // max input file size in bytes (optional client-side guard)
    maxBytes: 4 * 1024 * 1024, // 4MB

    handleCover(e) {
      this.error = '';
      const file = e.target.files?.[0];
      if (!file) return;

      if (!file.type.startsWith('image/')) {
        this.error = 'Please choose an image file.';
        e.target.value = '';
        return;
      }
      if (file.size > this.maxBytes) {
        // you can keep this or let server validate — your choice
        this.error = 'Image is larger than 4MB.';
        e.target.value = '';
        return;
      }

      const img = new Image();
      img.onload = () => {
        // Create canvas at fixed 1200x1600
        const canvas = document.createElement('canvas');
        canvas.width = this.targetW;
        canvas.height = this.targetH;
        const ctx = canvas.getContext('2d');

        // cover-fit: scale to fill, center crop
        const scale = Math.max(this.targetW / img.width, this.targetH / img.height);
        const drawW = img.width * scale;
        const drawH = img.height * scale;
        const dx = (this.targetW - drawW) / 2;
        const dy = (this.targetH - drawH) / 2;

        ctx.imageSmoothingEnabled = true;
        ctx.imageSmoothingQuality = 'high';
        ctx.clearRect(0, 0, this.targetW, this.targetH);
        ctx.drawImage(img, dx, dy, drawW, drawH);

        // Export to blob (JPEG, quality 0.9)
        canvas.toBlob((blob) => {
          if (!blob) {
            this.error = 'Could not process image.';
            return;
          }
          // Replace file input with the processed fixed-size file
          const fixedFile = new File([blob], 'cover.jpg', { type: 'image/jpeg', lastModified: Date.now() });
          const dt = new DataTransfer();
          dt.items.add(fixedFile);
          this.$refs.coverInput.files = dt.files;

          // Update preview
          if (this.coverUrl) URL.revokeObjectURL(this.coverUrl);
          this.coverUrl = URL.createObjectURL(fixedFile);
        }, 'image/jpeg', 0.9);
      };

      img.onerror = () => {
        this.error = 'Could not read image.';
        e.target.value = '';
      };

      // Read file
      const reader = new FileReader();
      reader.onload = (ev) => { img.src = ev.target.result; };
      reader.onerror = () => {
        this.error = 'Could not read image.';
        e.target.value = '';
      };
      reader.readAsDataURL(file);
    },
  };
}
</script>

                    {{-- Safety / guidelines card --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-2">Upload guidelines</h3>
                        <ul class="text-sm text-gray-600 space-y-1 list-disc ml-5">
                            <li>Only upload your own notes or materials you have rights to share.</li>
                            <li>Use clear titles and add tags so others can find your note.</li>
                            <li>Admins review uploads; publication may take a short time.</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="pt-2 flex justify-end gap-3">
                <a href="{{ route('public.notes.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary" :disabled="!fileReady"
                    :class="{ 'opacity-60 pointer-events-none': !fileReady }">
                    Upload
                </button>
            </div>
        </form>
    </div>

    {{-- Alpine logic --}}
    <script>
        function noteUploader() {
            return {
                onDrag: false,
                file: null,
                fileReady: false,
                fileExt: '',
                prettySize: '',
                coverUrl: null,

                handleFile(e) {
                    const f = e.target.files?.[0];
                    if (!f) return;
                    this.setFile(f);
                },
                handleDrop(e) {
                    this.onDrag = false;
                    const f = e.dataTransfer.files?.[0];
                    if (!f) return;
                    this.setFile(f);
                },
                setFile(f) {
                    const ok = ['pdf', 'doc', 'docx', 'ppt', 'pptx'];
                    const ext = f.name.split('.').pop()?.toLowerCase() || '';
                    if (!ok.includes(ext)) {
                        alert('Unsupported file type. Use PDF/DOCX/PPTX.');
                        return;
                    }
                    if (f.size > 25 * 1024 * 1024) {
                        alert('File too large (max 25MB).');
                        return;
                    }

                    this.file = f;
                    this.fileExt = ext;
                    this.prettySize = this.human(f.size);
                    this.fileReady = true;
                },
                clearFile() {
                    this.file = null;
                    this.fileReady = false;
                    this.fileExt = '';
                    this.prettySize = '';
                    const input = document.querySelector('input[name="file"]');
                    if (input) input.value = '';
                },
                handleCover(e) {
                    const f = e.target.files?.[0];
                    if (!f) return;
                    if (!f.type.startsWith('image/')) {
                        alert('Please select an image.');
                        return;
                    }
                    if (this.coverUrl) URL.revokeObjectURL(this.coverUrl);
                    this.coverUrl = URL.createObjectURL(f);
                },
                human(bytes) {
                    const units = ['B', 'KB', 'MB', 'GB'];
                    let i = 0,
                        n = bytes;
                    while (n >= 1024 && i < units.length - 1) {
                        n /= 1024;
                        i++;
                    }
                    return `${n.toFixed(1)} ${units[i]}`;
                }
            }
        }
    </script>
</x-app-layout>
