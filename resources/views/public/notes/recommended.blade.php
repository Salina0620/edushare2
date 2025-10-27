<x-app-layout>

    <style>
  .zigzag{ display:none !important; }
</style>

    <x-slot name="header">
        <h2 class="text-2xl md:text-3xl font-extrabold grow">
            Recommended for you
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @if($notes->isEmpty())
            <p class="text-gray-500">
                We need more interactions to personalize. Start by liking, bookmarking, and downloading notes you enjoy.
            </p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($notes as $note)
                    @include('public.notes.partials.card', ['note' => $note])
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
