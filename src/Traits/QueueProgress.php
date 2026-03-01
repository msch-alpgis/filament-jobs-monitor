<?php

namespace Croustibat\FilamentJobsMonitor\Traits;

use Croustibat\FilamentJobsMonitor\Models\QueueMonitor;

trait QueueProgress
{
    /**
     * Update progress.
     */
    public function setProgress(int $progress): void
    {
        $progress = min(100, max(0, $progress));

        if (! $monitor = $this->getQueueMonitor()) {
            return;
        }

        $monitor->update([
            'progress' => $progress,
        ]);

        $this->progressLastUpdated = time();
    }

    /**
     * Update working on.
     */
    public function setWorkingOn(string $workingOn): void
    {
        if (! $monitor = $this->getQueueMonitor()) {
            return;
        }

        $monitor->update([
            'working_on' => $workingOn,
        ]);
    }

    /**
     * Update results
     */
    public function setResults(array $results): void
    {
        if (! $monitor = $this->getQueueMonitor()) {
            return;
        }

        $monitor->update([
            'results' => $results,
        ]);
    }

    /**
     * Return Queue Monitor Model.
     */
    protected function getQueueMonitor(): ?QueueMonitor
    {
        if (! property_exists($this, 'job')) {
            return null;
        }

        if (! $this->job) {
            return null;
        }

        if (! $jobId = QueueMonitor::getJobId($this->job)) {
            return null;
        }

        $model = QueueMonitor::getModel();

        return $model::whereJobId($jobId)
            ->orderBy('started_at', 'desc')
            ->first();
    }
}
