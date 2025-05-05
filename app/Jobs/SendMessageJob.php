<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Services\MessageService;

class SendMessageJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private MessageService $sms;
    private Model $model;
    private $token;

    public function __construct(Model $model, $token) {
        $this->model = $model;
        $this->token = $token;
        $this->sms = new MessageService();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $this->sms->sendMessage($this->model->phone,
            $this->autofill($this->model->password, $this->token));
    }

    private function autofill($code, $token) {
        return "<#> Sizning tasdiqlash kodingiz: $code.\n$token";
    }
}
