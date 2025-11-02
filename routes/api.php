<?php declare(strict_types=1);

use App\Models\IotTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Exemplu pentru trimiterea de date de la un dispozitiv
Route::post('/device/data', function (Request $request) {
    $payload = $request->all();
    Log::info('Payload primit de la dispozitiv:', $payload);
    Cache::put('device:latest:value', $payload['value'] ?? null, now()->addMinutes(30));
    Cache::put('device:latest:ts', now()->toDateTimeString(), now()->addMinutes(30));

    return response()->json([
        'status' => 'success',
        'message' => 'Datele au fost primite și logate cu succes.',
        'received' => $payload,
    ]);
});

// Dispozitivul interogheaza dacă are task-uri cu status "pending" (simplu)
Route::get('/device/tasks', function () {
    $tasks = IotTask::query()
        ->where('status', 'pending')
        ->orderBy('id')
        ->get(['id', 'command', 'payload', 'status', 'created_at']);

    return response()->json([
        'count' => $tasks->count(),
        'tasks' => $tasks,
    ]);
});

// Dispozitivul actualizeaza statusul unui task (simplu)
Route::post('/device/task/status', function (Request $request) {
    $taskId = (int) $request->input('id');
    $status = (string) $request->input('status', 'done'); // ex: pending | in_progress | done | failed

    $task = IotTask::query()->find($taskId);

    if (! $task) {
        return response()->json(['message' => 'Task inexistent.'], 404);
    }

    $task->update(['status' => $status]);

    return response()->json([
        'message' => 'Status actualizat.',
        'task' => $task->only(['id', 'status']),
    ]);
});
