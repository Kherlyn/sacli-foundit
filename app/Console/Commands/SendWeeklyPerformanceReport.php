<?php

namespace App\Console\Commands;

use App\Services\AdminNotificationService;
use App\Services\StatisticsService;
use Illuminate\Console\Command;

class SendWeeklyPerformanceReport extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'admin:send-weekly-report';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Send weekly performance report to administrators';

  /**
   * Execute the console command.
   */
  public function handle(AdminNotificationService $adminNotificationService, StatisticsService $statisticsService)
  {
    $this->info('Generating weekly performance report...');

    try {
      // Get performance data for the last 7 days
      $performanceData = [
        'week_ending' => now()->format('Y-m-d'),
        'total_submissions' => $statisticsService->getSubmissionCount(7),
        'verified_items' => $statisticsService->getVerifiedCount(7),
        'rejected_items' => $statisticsService->getRejectedCount(7),
        'resolved_items' => $statisticsService->getResolvedCount(7),
        'avg_verification_time' => $statisticsService->getAverageVerificationTime(7),
        'top_categories' => $statisticsService->getTopCategories(5, 7),
        'user_activity' => $statisticsService->getUserActivityStats(7),
        'success_rate' => $statisticsService->getSuccessRateMetrics(7),
      ];

      // Send the weekly report
      $adminNotificationService->sendWeeklyPerformanceReport($performanceData);

      $this->info('Weekly performance report sent successfully.');
      $this->line("Report summary:");
      $this->line("- Total submissions: {$performanceData['total_submissions']}");
      $this->line("- Verified items: {$performanceData['verified_items']}");
      $this->line("- Rejected items: {$performanceData['rejected_items']}");
      $this->line("- Resolved items: {$performanceData['resolved_items']}");
    } catch (\Exception $e) {
      $this->error("Error sending weekly performance report: " . $e->getMessage());
      return Command::FAILURE;
    }

    return Command::SUCCESS;
  }
}
