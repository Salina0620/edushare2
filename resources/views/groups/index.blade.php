<x-app-layout>
    <div class="max-w-4xl mx-auto py-10">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold tracking-tight">Groups</h1>
        <a href="{{ route('groups.create') }}"
           class="inline-flex items-center h-9 px-3 rounded-lg text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm">
          + Create group
        </a>
      </div>
  
      <div class="space-y-3">
        @forelse ($groups as $group)
          @php
            $isMember = $group->members()->where('users.id', auth()->id())->exists();
            $membersCount = $group->members()->count();
          @endphp
  
          <div class="bg-white border border-gray-200 rounded-xl p-4 md:p-5 shadow-sm hover:shadow-md transition">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
              {{-- Left: title, description, meta --}}
              <div class="min-w-0">
                <div class="flex items-center gap-2 mb-1">
                  <h2 class="text-lg md:text-xl font-semibold text-gray-900 truncate">
                    {{ $group->name }}
                  </h2>
  
                  @if($group->status !== 'approved')
                    <span class="shrink-0 inline-flex items-center rounded-full bg-yellow-50 text-yellow-700 ring-1 ring-yellow-200 px-2 py-0.5 text-[11px] font-semibold">
                      {{ ucfirst($group->status) }}
                    </span>
                  @endif
                </div>
  
                @if($group->description)
                  <p class="text-sm text-gray-600 line-clamp-1">
                    {{ $group->description }}
                  </p>
                @endif
  
                <div class="mt-2 flex items-center gap-3 text-xs text-gray-500">
                  <span class="inline-flex items-center gap-1">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 3-1.34 3-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 3-1.34 3-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5C15 14.17 10.33 13 8 13zm8 0c-.29 0-.62.02-.97.04 1.16.84 1.97 1.93 1.97 3.46V19a1 1 0 0 1-1 1h6a1 1 0 0 0 1-1v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                    {{ $membersCount }} members
                  </span>
                  <span class="hidden md:inline text-gray-300">•</span>
                  <span>Created {{ $group->created_at->diffForHumans() }}</span>
                </div>
              </div>
  
              {{-- Right: actions (with modal for Leave) --}}
              <div class="flex items-center gap-2 md:shrink-0" x-data="{ open:false }">
                @if ($isMember)
                  <a href="{{ route('groups.chat', $group) }}"
                     class="inline-flex items-center justify-center h-9 px-3 rounded-lg text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700">
                    Open chat
                  </a>
  
                  <button type="button"
                          @click="open = true"
                          class="inline-flex items-center justify-center h-9 px-3 rounded-lg text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200">
                    Leave
                  </button>
  
                  <!-- Modal -->
                  <div x-cloak x-show="open" x-transition.opacity class="fixed inset-0 z-[70] flex items-center justify-center">
                    <div class="absolute inset-0 bg-black/40" @click="open=false"></div>
  
                    <div class="relative bg-white w-full max-w-md rounded-xl shadow-lg p-5">
                      <h3 class="text-lg font-semibold">Leave “{{ $group->name }}”?</h3>
                      <p class="mt-2 text-sm text-gray-600">
                        You’ll stop receiving new messages from this group. You can rejoin later.
                      </p>
  
                      <div class="mt-5 flex items-center justify-end gap-2">
                        <button type="button"
                                class="px-3 py-1.5 rounded bg-gray-100 text-gray-700 hover:bg-gray-200"
                                @click="open=false">
                          Cancel
                        </button>
  
                        <form method="POST" action="{{ route('groups.leave', $group) }}">
                          @csrf
                          <button type="submit" class="px-3 py-1.5 rounded bg-red-600 text-white hover:bg-red-700">
                            Leave group
                          </button>
                        </form>
                      </div>
                    </div>
                  </div>
                  <!-- /Modal -->
                @else
                  @if($group->status === 'approved')
                    <form method="POST" action="{{ route('groups.join', $group) }}">
                      @csrf
                      <button
                        class="inline-flex items-center justify-center h-9 px-3 rounded-lg text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700">
                        Join
                      </button>
                    </form>
                  @else
                    <span class="inline-flex items-center h-9 px-3 rounded-lg text-sm font-medium text-gray-500 bg-gray-100 cursor-not-allowed">
                      Pending approval
                    </span>
                  @endif
                @endif
              </div>
            </div>
          </div>
        @empty
          <div class="bg-white border border-gray-200 rounded-xl p-6 text-center text-gray-600">
            No groups yet. Create one to get started.
          </div>
        @endforelse
      </div>
    </div>
  </x-app-layout>
  