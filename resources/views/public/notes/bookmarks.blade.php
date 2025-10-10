<x-app-layout>
<x-slot name="header">
  <h1 class="text-2xl md:text-3xl font-extrabold text-white">Saved</h1>
</x-slot>


  @if ($notes->count() === 0)
    <div class="rounded-xl border bg-white p-8 text-center">
      <div class="text-2xl mb-2">🔖</div>
      <h2 class="text-lg font-semibold">No saved notes yet</h2>
      <p class="text-gray-600 mt-1">Bookmark notes to find them quickly later.</p>
      <a href="{{ route('public.notes.index') }}" class="btn-primary inline-flex items-center gap-2 mt-4">
        Browse Notes
      </a>
    </div>
  @else
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
      @foreach ($notes as $bm)
        @php $n = $bm->note ?? null; @endphp
        @if ($n)
          <div class="rounded-xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition">
            <a href="{{ route('public.notes.show', $n) }}" class="block aspect-video bg-gray-50 overflow-hidden rounded-t-xl">
              @if ($n->cover_url)
                <img src="{{ $n->cover_url }}" alt="" class="w-full h-full object-cover">
              @else
                <div class="w-full h-full grid place-items-center text-gray-500 text-sm">No cover</div>
              @endif
            </a>
            <div class="p-5">
              <a href="{{ route('public.notes.show', $n) }}" class="font-semibold hover:text-indigo-600 line-clamp-1">
                {{ $n->title }}
              </a>
              <div class="text-xs text-gray-500 mt-1">
                {{ $n->faculty->name ?? '—' }} · {{ $n->semester->name ?? '—' }}
              </div>
              <div class="flex items-center justify-between text-xs text-gray-500 mt-3">
                <div class="flex gap-3">
                  <span>👁 {{ number_format($n->views) }}</span>
                  <span>⬇️ {{ number_format($n->downloads) }}</span>
                </div>
                <span class="text-[11px] px-2 py-0.5 rounded-full bg-gray-100">{{ strtoupper($n->file_ext ?? '') }}</span>
              </div>
            </div>
          </div>
        @endif
      @endforeach
    </div>

    <div class="mt-6">
      {{ $notes->links() }}
    </div>
  @endif
</x-app-layout>
