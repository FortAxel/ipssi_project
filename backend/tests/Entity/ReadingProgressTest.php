<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\ReadingProgress;
use PHPUnit\Framework\TestCase;

final class ReadingProgressTest extends TestCase
{
    public function testUpdateProgressMarksCompletedOnLastPage(): void
    {
        $progress = new ReadingProgress();

        $progress->updateProgress(14, 14);

        $this->assertSame(14, $progress->getLastPageNumber());
        $this->assertTrue($progress->getIsCompleted());
    }

    public function testUpdateProgressClampsPageNumber(): void
    {
        $progress = new ReadingProgress();

        $progress->updateProgress(99, 10);

        $this->assertSame(10, $progress->getLastPageNumber());
        $this->assertTrue($progress->getIsCompleted());
    }

    public function testUpdateProgressRefreshesLastReadAt(): void
    {
        $progress = new ReadingProgress();
        $before = $progress->getLastReadAt();

        usleep(1000);
        $progress->updateProgress(2, 10);

        $this->assertGreaterThanOrEqual($before, $progress->getLastReadAt());
        $this->assertFalse($progress->getIsCompleted());
    }
}
