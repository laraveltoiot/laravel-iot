<div class="max-w-5xl mx-auto p-6" wire:poll.5s>
    <h1 class="text-2xl font-semibold">IoT Tasks</h1>

    <div class="mt-4 overflow-x-auto rounded border bg-white/60 dark:bg-black/20">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-gray-100/70 dark:bg-white/10 text-gray-700 dark:text-gray-200">
                <tr>
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Command</th>
                    <th class="px-4 py-2">Payload</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Created</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200/60 dark:divide-white/10">
                @forelse($tasks as $task)
                    <tr>
                        <td class="px-4 py-2 font-mono text-xs">{{ $task->id }}</td>
                        <td class="px-4 py-2">{{ $task->command }}</td>
                        <td class="px-4 py-2 font-mono text-xs whitespace-pre-wrap">@json($task->payload)</td>
                        <td class="px-4 py-2">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs capitalize
                                {{ match($task->status) {
                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-300/20 dark:text-yellow-300',
                                    'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-300/20 dark:text-blue-300',
                                    'done', 'completed' => 'bg-green-100 text-green-800 dark:bg-green-300/20 dark:text-green-300',
                                    'failed' => 'bg-red-100 text-red-800 dark:bg-red-300/20 dark:text-red-300',
                                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-300/20 dark:text-gray-300'
                                } }}">
                                {{ $task->status }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-gray-600 dark:text-gray-300">{{ $task->created_at }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">No tasks found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
