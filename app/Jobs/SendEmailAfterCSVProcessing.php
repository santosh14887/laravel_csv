<?php

namespace App\Jobs;

use App\Mail\CSVProcessingEmail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailAfterCSVProcessing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $processedData;

    /**
     * Create a new job instance.
     *
     * @param User   $user
     * @param string $processedData
     */
    public function __construct(User $user, string $processedData)
    {
        $this->user = $user;
        $this->processedData = $processedData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Process the CSV file
        // ...
        
        // Prepare email data
        $emailData = [
            'user' => $this->user,
            'processedData' => $this->processedData,
        ];
        
        // Send email
        Mail::to($this->user->email)->send(new CSVProcessingEmail($emailData));
    }
}
