<div data-reveal class="perspective-1000">


    <div data-tilt
        class="preserve-3d rounded-xl bg-white border border-gray-100 shadow-lg hover:shadow-2xl transition will-change-transform">
       <a href="{{ route('public.notes.show', $note) }}"
   class="block aspect-video relative overflow-hidden rounded-t-xl">

    @if(!empty($note->cover_url))
        {{-- Cover image --}}
        <img
            src="{{ $note->cover_url }}"
            alt="{{ $note->title }}"
            class="absolute inset-0 w-full h-full object-cover"
            loading="lazy"
        >
        {{-- slight overlay for readability --}}
        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>

        {{-- file ext chip (top-left) --}}
        <div class="absolute top-2 left-2">
            <span class="chip bg-white/90 backdrop-blur text-gray-800">
                {{ strtoupper($note->file_ext ?? 'FILE') }}
            </span>
        </div>
    @else
        {{-- Fallback gradient + ext chip (your previous style) --}}
        <div class="absolute inset-0 bg-gradient-to-br from-slate-100 to-slate-200"></div>
        <div class="absolute inset-0 flex items-center justify-center">
            <span class="chip backface-hidden">
                {{ strtoupper($note->file_ext ?? 'FILE') }}
            </span>
        </div>
        <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full border-4 border-white/60 animate-float"></div>
    @endif
</a>




        <div class="p-5">
            {{-- Status badge (only visible to owner, not public) --}}
            @auth
                @if (auth()->id() === ($note->user_id ?? null) && $note->status !== 'approved')
                    <div class="mb-2">
                        @if ($note->status === 'pending')
                            <span
                                class="inline-flex items-center px-2 py-0.5 text-xs font-semibold
                       rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                                Pending review
                            </span>
                        @elseif($note->status === 'rejected')
                            <span
                                class="inline-flex items-center px-2 py-0.5 text-xs font-semibold
                       rounded-full bg-rose-100 text-rose-800 border border-rose-200">
                                Rejected
                            </span>
                            @if ($note->reject_reason)
                                <span class="ml-2 text-xs text-gray-500">
                                    Reason: {{ Str::limit($note->reject_reason, 60) }}
                                </span>
                            @endif
                        @endif
                    </div>
                @endif
            @endauth


            <h3 class="text-lg font-semibold text-gray-900 mb-1 line-clamp-1">
                <a class="hover:text-indigo-600" href="{{ route('public.notes.show', $note) }}">{{ $note->title }}</a>
            </h3>
            <p class="text-sm text-gray-600 line-clamp-2 mb-3">{{ $note->description }}</p>

            <div class="text-xs text-gray-500 mb-4">
                {{ $note->faculty->name ?? '—' }} · {{ $note->semester->name ?? '—' }}
            </div>

            <div class="flex items-center justify-between text-xs text-gray-500">
                <div class="flex gap-3">
                    <span>👁 {{ number_format($note->views) }}</span>
                    <span>⬇️ {{ number_format($note->downloads) }}</span>
                    <span>💬 {{ $note->comments_count ?? $note->comments()->count() }}</span>
                </div>
                <a href="{{ route('public.notes.index', ['author' => $note->user_id]) }}"
                    class="text-indigo-600 hover:text-indigo-800 font-medium">
                    {{ $note->user->name }}
                </a>

            </div>
        </div>
    </div>
</div>
