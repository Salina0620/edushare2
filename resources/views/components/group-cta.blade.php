@props([
  'show' => false,
  'pendingCount' => 0,
])

@if ($show)
  <div class="fixed z-[60] right-4 bottom-6 sm:right-6 sm:bottom-8">
    <a href="{{ route('groups.index') }}"
       class="group inline-flex items-center gap-2 rounded-full px-4 py-2.5 shadow-lg ring-1 ring-black/5
              bg-gradient-to-r from-emerald-500 via-indigo-500 to-blue-500 text-white
              hover:brightness-110 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/80">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
              d="M16 14a4 4 0 10-8 0m12 0a5 5 0 00-10 0m10 0v2a2 2 0 002 2h.5M6 14a5 5 0 0110 0m-10 0v2a2 2 0 01-2 2H3.5" />
      </svg>
      <span class="text-sm font-semibold tracking-tight">Join groups</span>

      @if ($pendingCount > 0)
        <span class="ml-1 inline-flex items-center justify-center rounded-full bg-white/90 text-emerald-700
                     text-[10px] font-bold h-5 min-w-[20px] px-1.5 ring-1 ring-emerald-700/20">
          {{ $pendingCount }}
        </span>
      @endif
    </a>
  </div>
@endif
