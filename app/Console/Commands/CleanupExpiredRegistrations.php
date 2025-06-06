<?php

namespace App\Console\Commands;

use App\Models\RegistrationProgress;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupExpiredRegistrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'registrations:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired registration records and their associated files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredRegistrations = RegistrationProgress::where('expires_at', '<', Carbon::now())
            ->where('is_completed', false)
            ->get();

        foreach ($expiredRegistrations as $registration) {
            // Delete profile picture if exists
            if (isset($registration->step_data['step1']['profile_picture'])) {
                Storage::disk('public')->delete($registration->step_data['step1']['profile_picture']);
            }

            // Delete registration record
            $registration->delete();
        }

        $this->info('Cleaned up ' . $expiredRegistrations->count() . ' expired registration records.');
    }
}
