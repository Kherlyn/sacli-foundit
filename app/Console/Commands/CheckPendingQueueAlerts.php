<?php

namespace App\Console\Commands;

use App\Services\AdminNotificationService;
use App\Services\StatisticsService;
use Illuminate\Console\Command;

class CheckPendingQueueAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:check-pending-alerts {--threshold=24 : Hours threshold for pending items}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for pending items and send admin alerts if thresholds are exceeded';

    /**
     * Execute the console command.
     */
    public function handle(AdminNotificationService $adminNotificationService, StatisticsService $statisticsService)
    {
        $threshold = (int) $this->option('threshold');

        $this->info("Checking pending queue alerts with {$threshold} hour threshold...");

        try {
            // Check and send pending queue alerts
            $adminNotificationService->checkPendingQueueAlerts();

            // Get current statistics for reporting
            $stats = $statisticsService->getOverviewStats();

            $this->info("Queue check completed:");
            $this->line("- Total pending items: {$stats['pending_items']}");
            $this->line("- Total verified items: {$stats['verified_items']}");
            $this->line("- Total rejected items: {$stats['rejected_items']}");

            // Send daily statistics if it's the right time (can be configured)
            if (now()->hour === 9 && now()->minute < 5) { // 9 AM daily
                $dailyStats = [
                    'total_items' => $stats['total_items'],
                    'pending_items' => $stats['pending_items'],
                    'verified_items' => $stats['verified_items'],
                    'rejected_items' => $stats['rejected_items'],
                    'resolved_items' => $stats['resolved_items'],
                    'date' => now()->format('Y-m-d'),
                ];

                $adminNotificationService->sendDailyStatisticsSummary($dailyStats);
                $this->info("Daily statistics summary sent to admins.");
            }

            $this->info("Pending queue alerts check completed successfully.");
        } catch (\Exception $e) {
            $this->error("Error checking pending queue alerts: " . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
