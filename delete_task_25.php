<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Task;

$task = Task::find(25);
if ($task) {
    $task->delete();
    echo "Task #25 deleted successfully!\n";
} else {
    echo "Task #25 not found.\n";
}
