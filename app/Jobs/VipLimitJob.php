<?php

namespace App\Jobs;

use App\Models\Driver;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class VipLimitJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $driver_id;
    public function __construct($driver_id) {
        $this->driver_id = $driver_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $driver = Driver::query()->findOrFail($this->driver_id);
        $driver->update(['vip' => 0]);
    }
}
