<?php

namespace App\Console\Commands;

use App\Models\Log;
use App\Services\CrmService;
use Illuminate\Console\Command;

class OrderGet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:order:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(CrmService $service)
    {
        $resp = $service->handle();
        Log::create([
            'request' => 'Crm query',
            'response' => $resp,
        ]);

    }
}
