<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="text-2xl font-extrabold text-white">Moderate Notes</h2>
      <div class="flex gap-2">
        @foreach (['pending','approved','rejected'] as $s)
          <a href="{{ route('admin.notes.index',['status'=>$s]) }}"
             class="btn-secondary {{ $status===$s ? 'bg-gray-200' : '' }}">{{ ucfirst($s) }}</a>
        @endforeach
      </div>
    </div>
  </x-slot>

  <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
    @forelse($notes as $note)
      <div class="flex items-start justify-between py-4 border-b last:border-0">
        <div class="pr-4">
          <div class="font-semibold text-gray-900">{{ $note->title }}</div>
          <div class="text-sm text-gray-500">
            by {{ $note->user?->name ?? 'Unknown' }}
            @if($note->subject) · {{ $note->subject->name }} @endif
          </div>
          @if($note->status==='rejected' && $note->reject_reason)
            <div class="text-sm text-rose-600 mt-1">Reason: {{ $note->reject_reason }}</div>
          @endif
        </div>

        <div class="flex items-center gap-2">
          <a class="btn-secondary" href="{{ route('public.notes.show',$note) }}" target="_blank">Preview</a>

          @if($status==='pending')
            <form method="POST" action="{{ route('admin.notes.approve',$note) }}">
              @csrf <button class="btn-primary">Approve</button>
            </form>

            <form method="POST" action="{{ route('admin.notes.reject',$note) }}"
                  onsubmit="return confirm('Reject this note?');">
              @csrf
              <input type="hidden" name="reason" value="Not suitable / needs revision">
              <button class="btn-secondary">Reject</button>
            </form>
          @endif
        </div>
      </div>
    @empty
      <p class="text-gray-600">No notes in this state.</p>
    @endforelse

    <div class="mt-6">{{ $notes->links() }}</div>
  </div>
</x-app-layout>
