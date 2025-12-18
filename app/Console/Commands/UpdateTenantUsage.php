<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UpdateTenantUsage extends Command
{
    protected $signature = 'tenants:calculate-usage';

    protected $description = 'Empty livewire-tmp files, then calculate S3 + DB storage usage';

    public function handle()
    {
        $this->info('Starting tenant usage calculation (with file cleanup)...');

        $subscriptions = Subscription::where('status', Subscription::STATUS_ACTIVE)->get();

        if ($subscriptions->isEmpty()) {
            $this->warn('No active subscriptions found.');
            return;
        }

        $bar = $this->output->createProgressBar($subscriptions->count());
        $bar->start();

        $results = [];

        foreach ($subscriptions as $subscription) {
            try {
                // --- STEP 1: CLEANUP ---
                if ($subscription->tenant) {
                    $subscription->tenant->run(function () {
                        // Check if S3 disk is used
                        $disk = Storage::disk('s3');

                        // 1. Get all files inside the folder recursively
                        $tempFiles = $disk->allFiles('livewire-tmp');

                        // 2. Delete the files only (Batch delete)
                        if (!empty($tempFiles)) {
                            $disk->delete($tempFiles);
                        }
                    });
                }

                // --- STEP 2: CALCULATION ---
                // Calculate remaining usage
                $bytesUsed = $subscription->getUsedStorageInBytes();
                $percentage = $subscription->getStorageUsagePercentage();
                $formatted = $subscription->formatBytes($bytesUsed);

                // --- STEP 3: SAVE TO DATABASE ---
                $metadata = $subscription->metadata ?? [];
                $metadata['usage_stats'] = [
                    'bytes' => $bytesUsed,
                    'formatted' => $formatted,
                    'percentage' => $percentage,
                    'last_updated' => now()->toDateTimeString(),
                ];

                $subscription->update(['metadata' => $metadata]);

                $results[] = [
                    'tenant_id' => $subscription->tenant_id ?? 'N/A',
                    'plan' => $subscription->plan,
                    'usage' => $formatted,
                    'percent' => $percentage . '%',
                    'status' => 'OK'
                ];

            } catch (\Exception $e) {
                Log::error("Usage calc failed for Sub ID {$subscription->id}: " . $e->getMessage());

                $results[] = [
                    'tenant_id' => $subscription->tenant_id ?? 'N/A',
                    'plan' => $subscription->plan,
                    'usage' => 'ERROR',
                    'percent' => '0%',
                    'status' => 'FAILED'
                ];
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('Cleanup and Calculation Complete. Summary:');
        $this->table(
            ['Tenant ID', 'Plan', 'Usage', 'Percent', 'Status'],
            $results
        );
    }
}
