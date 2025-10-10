{{-- FIXED, FULL-WIDTH NAV --}}
<nav x-data="{ open: false }"
     class="fixed top-0 left-0 right-0 z-50 w-full bg-white border-b border-gray-200 shadow-sm">

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex h-16 items-center justify-between">
      {{-- Left: Brand + Primary --}}
      <div class="flex items-center gap-8">
        {{-- Brand --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2 group">
          <svg class="w-7 h-7 text-indigo-600 group-hover:text-indigo-700 transition-colors"
               viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20a1 1 0 1 1 0 2H6.5C5.67 5 5 5.67 5 6.5V19a2 2 0 0 0 2 2h13a1 1 0 1 1 0 2H7a4 4 0 0 1-4-4V6.5z"/>
            <path d="M8 7h11a1 1 0 1 1 0 2H8a1 1 0 1 1 0-2zm0 4h11a1 1 0 1 1 0 2H8a1 1 0 1 1 0-2zm0 4h11a1 1 0 1 1 0 2H8a1 1 0 1 1 0-2z"/>
          </svg>
          <span class="text-xl font-bold tracking-tight text-gray-900">EduShare</span>
        </a>

        {{-- Primary links (desktop) --}}
        <div class="hidden md:flex items-center gap-1">
          <a href="{{ route('home') }}"
             class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('home') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
            Home
          </a>
          <a href="{{ route('public.notes.index') }}"
             class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('public.notes.*') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
            Browse Notes
          </a>

          @auth
            <a href="{{ route('notes.create') }}"
               class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('notes.create') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
              Upload
            </a>
            <a href="{{ route('notes.recommended') }}"
               class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('notes.recommended') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
              Recommended
            </a>
          @endauth
        </div>
      </div>

      {{-- Right: Actions --}}
      <div class="hidden md:flex items-center gap-3">
        @auth
          <a href="{{ route('notes.mine') }}"
             class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('notes.mine') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
            My Notes
          </a>
          <a href="{{ route('notes.bookmarks') }}"
             class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('notes.bookmarks') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
            Saved
          </a>

          {{-- Profile dropdown --}}
          <x-dropdown align="right" width="48">
            <x-slot name="trigger">
              <button class="flex items-center gap-2 text-sm rounded-full focus:outline-none">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                  <img class="w-8 h-8 rounded-full object-cover ring-1 ring-gray-200"
                       src="{{ auth()->user()->profile_photo_url }}"
                       alt="{{ auth()->user()->name }}" />
                @else
                  <span class="px-3 py-2 rounded-md text-gray-700 hover:bg-gray-50">
                    {{ Str::limit(auth()->user()->name, 16) }}
                  </span>
                @endif
                <svg class="w-4 h-4 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 9l-7 7-7-7" />
                </svg>
              </button>
            </x-slot>
            <x-slot name="content">
              <div class="block px-4 py-2 text-xs text-gray-400">Manage Account</div>
              <x-dropdown-link href="{{ route('profile.show') }}">Profile</x-dropdown-link>
              <div class="border-t border-gray-200 my-1"></div>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-dropdown-link href="{{ route('logout') }}"
                  onclick="event.preventDefault(); this.closest('form').submit();">
                  Log Out
                </x-dropdown-link>
              </form>
            </x-slot>
          </x-dropdown>
        @endauth

        @guest
          <a href="{{ route('login') }}"
             class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
            Login
          </a>
          <a href="{{ route('register') }}"
             class="inline-flex items-center gap-2 px-3 py-2 rounded-md text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow">
            Register
          </a>
        @endguest
      </div>

      {{-- Mobile hamburger --}}
      <div class="md:hidden">
        <button @click="open = ! open"
                class="inline-flex items-center justify-center rounded-md p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                aria-controls="mobile-menu" :aria-expanded="open">
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16" />
            <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </div>

  {{-- Mobile panel (drops over content, that’s OK) --}}
  <div id="mobile-menu" x-show="open" x-transition.origin.top.left
       class="md:hidden border-t border-gray-200 bg-white">
    <div class="px-4 py-3 space-y-1">
      <a href="{{ route('home') }}"
         class="block px-3 py-2 rounded-md text-base {{ request()->routeIs('home') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }}">
        Home
      </a>
      <a href="{{ route('public.notes.index') }}"
         class="block px-3 py-2 rounded-md text-base {{ request()->routeIs('public.notes.*') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }}">
        Browse Notes
      </a>

      @auth
        <a href="{{ route('notes.create') }}"
           class="block px-3 py-2 rounded-md text-base {{ request()->routeIs('notes.create') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }}">
          Upload
        </a>
        <a href="{{ route('notes.recommended') }}"
           class="block px-3 py-2 rounded-md text-base {{ request()->routeIs('notes.recommended') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }}">
          Recommended
        </a>
        <a href="{{ route('notes.mine') }}"
           class="block px-3 py-2 rounded-md text-base {{ request()->routeIs('notes.mine') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }}">
          My Notes
        </a>
        <a href="{{ route('notes.bookmarks') }}"
           class="block px-3 py-2 rounded-md text-base {{ request()->routeIs('notes.bookmarks') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }}">
          Saved
        </a>
        <a href="{{ route('profile.show') }}"
           class="block px-3 py-2 rounded-md text-base {{ request()->routeIs('profile.show') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }}">
          Profile
        </a>
        <form method="POST" action="{{ route('logout') }}" class="px-3">
          @csrf
          <button class="w-full text-left px-0 py-2 text-base rounded-md text-gray-700 hover:bg-gray-50">
            Log Out
          </button>
        </form>
      @endauth

      @guest
        <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base text-gray-700 hover:bg-gray-50">
          Login
        </a>
        <a href="{{ route('register') }}" class="block px-3 py-2 rounded-md text-base text-indigo-600 hover:bg-indigo-50 font-semibold">
          Register
        </a>
      @endguest
    </div>

    @can('admin')
      <div class="px-4 pb-4">
        <a href="{{ route('admin.notes.index', ['status' => 'pending']) }}"
           class="inline-flex w-full items-center justify-center gap-2 px-3 py-2 rounded-md text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow">
          Admin · Pending Notes
        </a>
      </div>
    @endcan
  </div>
</nav>
