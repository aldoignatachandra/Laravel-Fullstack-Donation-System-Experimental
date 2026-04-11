<?php

namespace Tests\Unit\Helper;

use App\Helper\CampaignHelper;
use Tests\TestCase;

class CampaignHelperTest extends TestCase
{
    /**
     * Test getStatusText returns correct text for all statuses.
     */
    public function test_get_status_text_returns_correct_values(): void
    {
        $this->assertEquals('Draft', CampaignHelper::getStatusText(0));
        $this->assertEquals('Aktif', CampaignHelper::getStatusText(1));
        $this->assertEquals('Dijeda', CampaignHelper::getStatusText(2));
        $this->assertEquals('Selesai', CampaignHelper::getStatusText(3));
        $this->assertEquals('Dibatalkan', CampaignHelper::getStatusText(4));
        $this->assertEquals('Unknown', CampaignHelper::getStatusText(99));
    }

    /**
     * Test getStatusClass returns correct CSS classes.
     */
    public function test_get_status_class_returns_correct_classes(): void
    {
        $this->assertEquals('bg-gray-100 text-gray-800', CampaignHelper::getStatusClass(0));
        $this->assertEquals('bg-green-100 text-green-800', CampaignHelper::getStatusClass(1));
        $this->assertEquals('bg-yellow-100 text-yellow-800', CampaignHelper::getStatusClass(2));
        $this->assertEquals('bg-blue-100 text-blue-800', CampaignHelper::getStatusClass(3));
        $this->assertEquals('bg-red-100 text-red-800', CampaignHelper::getStatusClass(4));
        $this->assertEquals('bg-gray-100 text-gray-800', CampaignHelper::getStatusClass(99));
    }

    /**
     * Test getStatusIcon returns correct icon classes.
     */
    public function test_get_status_icon_returns_correct_icons(): void
    {
        $this->assertEquals('fas fa-edit', CampaignHelper::getStatusIcon(0));
        $this->assertEquals('fas fa-play-circle', CampaignHelper::getStatusIcon(1));
        $this->assertEquals('fas fa-pause-circle', CampaignHelper::getStatusIcon(2));
        $this->assertEquals('fas fa-check-circle', CampaignHelper::getStatusIcon(3));
        $this->assertEquals('fas fa-times-circle', CampaignHelper::getStatusIcon(4));
        $this->assertEquals('fas fa-circle', CampaignHelper::getStatusIcon(99));
    }

    /**
     * Test getProgressClass returns correct progress bar classes.
     */
    public function test_get_progress_class_returns_correct_classes(): void
    {
        $this->assertEquals('bg-gray-300', CampaignHelper::getProgressClass(0));
        $this->assertEquals('bg-green-500', CampaignHelper::getProgressClass(1));
        $this->assertEquals('bg-yellow-500', CampaignHelper::getProgressClass(2));
        $this->assertEquals('bg-blue-500', CampaignHelper::getProgressClass(3));
        $this->assertEquals('bg-red-500', CampaignHelper::getProgressClass(4));
        $this->assertEquals('bg-gray-300', CampaignHelper::getProgressClass(99));
    }

    /**
     * Test getStatusBadge returns complete badge data.
     */
    public function test_get_status_badge_returns_complete_data(): void
    {
        $badge = CampaignHelper::getStatusBadge(1);

        $this->assertIsArray($badge);
        $this->assertArrayHasKey('text', $badge);
        $this->assertArrayHasKey('class', $badge);
        $this->assertArrayHasKey('icon', $badge);
        $this->assertEquals('Aktif', $badge['text']);
        $this->assertEquals('bg-green-100 text-green-800', $badge['class']);
        $this->assertEquals('fas fa-play-circle', $badge['icon']);
    }

    /**
     * Test status checker methods return correct boolean values.
     */
    public function test_status_checker_methods(): void
    {
        // isActive
        $this->assertTrue(CampaignHelper::isActive(1));
        $this->assertFalse(CampaignHelper::isActive(0));
        $this->assertFalse(CampaignHelper::isActive(2));

        // isCompleted
        $this->assertTrue(CampaignHelper::isCompleted(3));
        $this->assertFalse(CampaignHelper::isCompleted(1));

        // isDraft
        $this->assertTrue(CampaignHelper::isDraft(0));
        $this->assertFalse(CampaignHelper::isDraft(1));

        // isPaused
        $this->assertTrue(CampaignHelper::isPaused(2));
        $this->assertFalse(CampaignHelper::isPaused(1));

        // isCancelled
        $this->assertTrue(CampaignHelper::isCancelled(4));
        $this->assertFalse(CampaignHelper::isCancelled(1));
    }

    /**
     * Test getProgressPercent calculates correctly.
     */
    public function test_get_progress_percent_calculates_correctly(): void
    {
        // Normal calculation
        $this->assertEquals(50.0, CampaignHelper::getProgressPercent(50000, 100000));
        $this->assertEquals(100.0, CampaignHelper::getProgressPercent(150000, 100000));
        $this->assertEquals(0.0, CampaignHelper::getProgressPercent(0, 100000));

        // Edge cases
        $this->assertEquals(0.0, CampaignHelper::getProgressPercent(100000, 0));
        $this->assertEquals(0.0, CampaignHelper::getProgressPercent(100000, -1000));
        $this->assertEquals(33.33, round(CampaignHelper::getProgressPercent(33333, 100000), 2));
    }
}
