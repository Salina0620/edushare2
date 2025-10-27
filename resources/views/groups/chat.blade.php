{{-- resources/views/groups/chat.blade.php --}}
<x-app-layout>
    <div class="max-w-3xl mx-auto py-8">
        <div class="flex items-center justify-between mb-4" x-data="{ open:false }">
            <h2 class="text-2xl font-bold">{{ $group->name }} chat</h2>

            <div class="flex items-center gap-3">
                <a href="{{ route('groups.index') }}" class="text-sm text-gray-600 hover:underline">All groups</a>

                <!-- Leave trigger -->
                <button type="button"
                        @click="open = true"
                        class="text-sm px-3 py-1.5 rounded bg-gray-100 text-gray-700 hover:bg-gray-200">
                    Leave group
                </button>

                <!-- Modal -->
                <div x-cloak x-show="open" x-transition.opacity class="fixed inset-0 z-[70] flex items-center justify-center">
                    <div class="absolute inset-0 bg-black/40" @click="open=false"></div>

                    <div class="relative bg-white w-full max-w-md rounded-xl shadow-lg p-5">
                        <h3 class="text-lg font-semibold">Leave “{{ $group->name }}”?</h3>
                        <p class="mt-2 text-sm text-gray-600">
                            You’ll be removed from members and won’t receive new messages. You can rejoin later.
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
            </div>
        </div>

        <div id="chat-box" class="h-96 overflow-y-auto bg-white border rounded-lg p-4 mb-3 space-y-2">
            @foreach ($messages as $m)
                <div class="flex {{ $m->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                    <div
                        class="max-w-[80%] rounded-2xl px-3 py-2 text-sm
                        {{ $m->user_id === auth()->id() ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-900' }}">
                        <div class="text-xs opacity-70 mb-0.5">{{ $m->user->name }}</div>
                        <div>{{ $m->content }}</div>
                        <div class="text-[10px] opacity-70 mt-1">{{ $m->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <form id="chat-form" action="{{ route('groups.chat.store', $group) }}" method="POST" class="flex gap-2">
            @csrf
            <input type="text" name="content" id="message-input" autocomplete="off"
                class="flex-1 border rounded px-3 py-2" placeholder="Type a message…" required>
            <button id="send-btn" type="submit"
                class="px-4 py-2 rounded bg-emerald-600 text-white opacity-50 cursor-not-allowed" disabled>
                Send
            </button>
        </form>

        {{-- Enable/disable Send based on input --}}
        <script>
            const input = document.getElementById('message-input');
            const sendBtn = document.getElementById('send-btn');
            input.addEventListener('input', () => {
                const hasText = input.value.trim().length > 0;
                sendBtn.disabled = !hasText;
                sendBtn.classList.toggle('opacity-50', !hasText);
                sendBtn.classList.toggle('cursor-not-allowed', !hasText);
            });
        </script>

        <div id="meta-row" class="mt-2 flex items-center gap-3">
            <div id="typing" class="text-xs text-gray-500 hidden">Someone is typing…</div>
            <div id="error" class="text-xs text-red-600 hidden">Failed to send. Try again.</div>
        </div>
    </div>

    <script>
        const box = document.getElementById('chat-box');
        const form = document.getElementById('chat-form');
        const input2 = document.getElementById('message-input');
        const sendBtn2 = document.getElementById('send-btn');
        const typingEl = document.getElementById('typing');
        const errorEl = document.getElementById('error');
        const meId = {{ auth()->id() }};
        const groupId = {{ $group->id }};

        box.scrollTop = box.scrollHeight;

        function appendBubble({ userId, name, content, time }) {
            const wrapper = document.createElement('div');
            wrapper.className = 'flex ' + (userId === meId ? 'justify-end' : 'justify-start');

            const bubble = document.createElement('div');
            bubble.className = 'max-w-[80%] rounded-2xl px-3 py-2 text-sm ' +
                (userId === meId ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-900');

            bubble.innerHTML = `
                <div class="text-xs opacity-70 mb-0.5">${name}</div>
                <div>${content}</div>
                <div class="text-[10px] opacity-70 mt-1">${time}</div>
            `;

            wrapper.appendChild(bubble);
            box.appendChild(wrapper);
            box.scrollTop = box.scrollHeight;
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            errorEl.classList.add('hidden');

            const text = input2.value.trim();
            if (!text) return;

            appendBubble({ userId: meId, name: @json(auth()->user()->name), content: text, time: 'just now' });

            input2.value = '';
            sendBtn2.disabled = true;
            sendBtn2.classList.add('opacity-70', 'cursor-not-allowed');

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ content: text }),
                });

                if (!res.ok) throw new Error('Bad response');

                await res.json();
            } catch (err) {
                errorEl.classList.remove('hidden');
            } finally {
                sendBtn2.disabled = false;
                sendBtn2.classList.remove('opacity-70', 'cursor-not-allowed');
                input2.focus();
            }
        });
    </script>

    <script type="module">
        import '../js/bootstrap.js';

        const groupId = {{ $group->id }};
        const meId = {{ auth()->id() }};
        const typingEl = document.getElementById('typing');

        if (!window.Echo) {
            console.error('Echo not initialized. Check Vite and .env VITE_* variables.');
        }

        const channel = window.Echo?.join(`presence-group.${groupId}`)
            .here(() => {})
            .joining(() => {})
            .leaving(() => {})
            .listen('.message.sent', (e) => {
                appendMessage(e.user.id, e.user.name, e.content, e.created_at);
            });

        function appendMessage(userId, name, content, iso) {
            const time = new Date(iso).toLocaleTimeString();
            window.appendBubble({ userId, name, content, time });
        }

        let hideTimer;
        const input = document.getElementById('message-input');
        input.addEventListener('input', () => {
            channel?.whisper('typing', { id: meId });
        });
        channel?.listenForWhisper('typing', () => {
            typingEl.classList.remove('hidden');
            clearTimeout(hideTimer);
            hideTimer = setTimeout(() => typingEl.classList.add('hidden'), 1000);
        });

        window.appendBubble = (payload) => {
            const box = document.getElementById('chat-box');
            const meId = {{ auth()->id() }};
            const wrapper = document.createElement('div');
            wrapper.className = 'flex ' + (payload.userId === meId ? 'justify-end' : 'justify-start');

            const bubble = document.createElement('div');
            bubble.className = 'max-w-[80%] rounded-2xl px-3 py-2 text-sm ' +
                (payload.userId === meId ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-900');

            bubble.innerHTML = `
                <div class="text-xs opacity-70 mb-0.5">${payload.name}</div>
                <div>${payload.content}</div>
                <div class="text-[10px] opacity-70 mt-1">${payload.time ?? new Date(payload.created_at ?? Date.now()).toLocaleTimeString()}</div>
            `;

            wrapper.appendChild(bubble);
            box.appendChild(wrapper);
            box.scrollTop = box.scrollHeight;
        };
    </script>
</x-app-layout>
