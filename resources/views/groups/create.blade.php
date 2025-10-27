{{-- resources/views/groups/create.blade.php --}}
<x-app-layout>
    <div class="max-w-lg mx-auto py-10">
      <h1 class="text-2xl font-bold mb-6">Create a group</h1>
  
      <form method="POST" action="{{ route('groups.store') }}" class="bg-white p-6 rounded-lg shadow space-y-4">
        @csrf
  
        <div>
          <label class="block text-sm font-medium text-gray-700">Group name</label>
          <input type="text" name="name" class="mt-1 w-full border-gray-300 rounded px-3 py-2" required>
          @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
  
        <div>
          <label class="block text-sm font-medium text-gray-700">Description</label>
          <textarea name="description" rows="4" class="mt-1 w-full border-gray-300 rounded px-3 py-2"></textarea>
          @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
  
        <div class="flex items-center justify-between">
          <a href="{{ route('groups.index') }}" class="text-gray-600 hover:underline">Back to groups</a>
          <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700">
            Create
          </button>
        </div>
      </form>
  
      <p class="text-xs text-gray-500 mt-3">
        New groups may require approval before members can join and chat.
      </p>
    </div>
  </x-app-layout>
  