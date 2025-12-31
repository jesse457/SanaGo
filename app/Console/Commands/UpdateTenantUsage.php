<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UpdateTenantUsage extends Command
{
    protected $signature = 'tenants:calculate-usage 
                            {--force : Force cleanup even if not in production}
                            {--skip-cleanup : Skip livewire-tmp cleanup}
                            {--tenant= : Process only specific tenant ID}';

    protected $description = 'Clean up temporary files and calculate storage usage for all active tenants';

    private int $successCount = 0;
    private int $failureCount = 0;
    private int $cleanedFiles = 0;

    public function handle(): int
    {
        $this->info('🚀 Starting tenant usage calculation...');
        $this->newLine();

        // Get subscriptions to process
        $subscriptions = $this->getSubscriptionsToProcess();

        if ($subscriptions->isEmpty()) {
            $this->warn('⚠️  No active subscriptions found.');
            return self::FAILURE;
        }

        $this->info("📊 Processing {$subscriptions->count()} tenant(s)...");
        $this->newLine();

        $bar = $this->output->createProgressBar($subscriptions->count());
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% - %message%');
        $bar->setMessage('Initializing...');
        $bar->start();

        $results = [];

        foreach ($subscriptions as $subscription) {
            $tenantId = $subscription->tenant_id ?? 'N/A';
            $bar->setMessage("Processing tenant: {$tenantId}");

            try {
                // Step 1: Cleanup temporary files
                if (!$this->option('skip-cleanup')) {
                    $cleaned = $this->cleanupTempFiles($subscription);
                    $this->cleanedFiles += $cleaned;
                }

                // Step 2: Calculate storage usage
                $usageData = $this->calculateUsage($subscription);

                // Step 3: Save to database
                $this->saveUsageMetadata($subscription, $usageData);

                $results[] = [
                    'tenant_id' => $tenantId,
                    'plan' => $subscription->plan ?? 'N/A',
                    'usage' => $usageData['formatted'],
                    'percent' => $usageData['percentage'] . '%',
                    'status' => '✅ OK'
                ];

                $this->successCount++;

            } catch (\Exception $e) {
                $this->handleError($subscription, $e);

                $results[] = [
                    'tenant_id' => $tenantId,
                    'plan' => $subscription->plan ?? 'N/A',
                    'usage' => 'ERROR',
                    'percent' => '0%',
                    'status' => '❌ FAILED'
                ];

                $this->failureCount++;
            }

            $bar->advance();
        }

        $bar->setMessage('Complete!');
        $bar->finish();
        $this->newLine(2);

        // Display summary
        $this->displaySummary($results);

        return $this->failureCount > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function getSubscriptionsToProcess()
    {
        $query = Subscription::with('tenant')
            ->where('status', Subscription::STATUS_ACTIVE);

        if ($tenantId = $this->option('tenant')) {
            $query->where('tenant_id', $tenantId);
        }

        return $query->get();
    }

    private function cleanupTempFiles(Subscription $subscription): int
    {
        if (!$subscription->tenant) {
            return 0;
        }

        $cleanedCount = 0;

        $subscription->tenant->run(function () use (&$cleanedCount) {
            try {
                $disk = Storage::disk('s3');

                // Get all temporary files
                $tempFiles = $disk->allFiles('livewire-tmp');

                if (!empty($tempFiles)) {
                    // Delete in batches to avoid memory issues
                    $chunks = array_chunk($tempFiles, 100);
                    
                    foreach ($chunks as $chunk) {
                        $disk->delete($chunk);
                        $cleanedCount += count($chunk);
                    }

                    Log::info("Cleaned {$cleanedCount} temporary files for tenant", [
                        'tenant_id' => tenant('id'),
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning("Failed to cleanup temp files for tenant: " . $e->getMessage(), [
                    'tenant_id' => tenant('id'),
                ]);
            }
        });

        return $cleanedCount;
    }

    private function calculateUsage(Subscription $subscription): array
    {
        $bytesUsed = $subscription->getUsedStorageInBytes();
        $percentage = $subscription->getStorageUsagePercentage();
        $formatted = $subscription->formatBytes($bytesUsed);

        return [
            'bytes' => $bytesUsed,
            'formatted' => $formatted,
            'percentage' => $percentage,
            'last_updated' => now()->toDateTimeString(),
        ];
    }

    private function saveUsageMetadata(Subscription $subscription, array $usageData): void
    {
        $metadata = $subscription->metadata ?? [];
        $metadata['usage_stats'] = $usageData;

        // Use DB transaction for safety
        DB::transaction(function () use ($subscription, $metadata) {
            $subscription->update(['metadata' => $metadata]);
        });
    }

    private function handleError(Subscription $subscription, \Exception $e): void
    {
        $context = [
            'subscription_id' => $subscription->id,
            'tenant_id' => $subscription->tenant_id,
            'plan' => $subscription->plan,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ];

        Log::error("Tenant usage calculation failed", $context);

        // In production, you might want to send alerts here
        if (app()->environment('production')) {
            // TODO: Send notification to admin
            // Notification::route('mail', config('app.admin_email'))
            //     ->notify(new TenantUsageCalculationFailed($context));
        }
    }

    private function displaySummary(array $results): void
    {
        $this->info('✨ Cleanup and Calculation Complete!');
        $this->newLine();

        // Display statistics
        $this->components->twoColumnDetail('Total Processed', (string) count($results));
        $this->components->twoColumnDetail('Successful', "<fg=green>{$this->successCount}</>");
        $this->components->twoColumnDetail('Failed', $this->failureCount > 0 ? "<fg=red>{$this->failureCount}</>" : '0');
        $this->components->twoColumnDetail('Files Cleaned', (string) $this->cleanedFiles);
        $this->newLine();

        // Display detailed table
        if (!empty($results)) {
            $this->table(
                ['Tenant ID', 'Plan', 'Usage', 'Percent', 'Status'],
                $results
            );
        }

        // Display warnings if any failures
        if ($this->failureCount > 0) {
            $this->newLine();
            $this->warn("⚠️  {$this->failureCount} tenant(s) failed. Check logs for details.");
        }
    }
}
