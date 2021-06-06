<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;

class ForceDeleteTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forcedelete:tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'force delete tasks after 30 days';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info("Checking soft-deleted tasks.");

        $task = Task::onlyTrashed()
                ->where('deleted_at', '<=', now()->subDays(30));

        $task->forceDelete();

        $this->info('forcedelete:tasks success');
    }
}
