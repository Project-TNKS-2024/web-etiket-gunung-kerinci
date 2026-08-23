<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmergencyTrackingApiTest extends TestCase
{
    /**
     * Test GPS upload requires authentication.
     */
    public function test_gps_upload_requires_auth(): void
    {
        $response = $this->postJson('/api/tracking/gps', [
            'latitude' => -1.6974,
            'longitude' => 101.2642,
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test GPS batch upload requires authentication.
     */
    public function test_gps_batch_requires_auth(): void
    {
        $response = $this->postJson('/api/tracking/gps/batch', [
            'positions' => [['latitude' => -1.6974, 'longitude' => 101.2642, 'recorded_at' => now()]],
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test my-position requires authentication.
     */
    public function test_my_position_requires_auth(): void
    {
        $response = $this->getJson('/api/tracking/my-position');
        $response->assertStatus(401);
    }

    /**
     * Test emergency trigger requires authentication.
     */
    public function test_emergency_trigger_requires_auth(): void
    {
        $response = $this->postJson('/api/emergency/trigger', [
            'title' => 'Test',
            'description' => 'Test emergency',
            'severity' => 'low',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test emergency active requires authentication.
     */
    public function test_emergency_active_requires_auth(): void
    {
        $response = $this->getJson('/api/emergency/active');
        $response->assertStatus(401);
    }

    /**
     * Test checkpoint QR requires authentication.
     */
    public function test_checkpoint_qr_requires_auth(): void
    {
        $response = $this->postJson('/api/tracking/checkpoint/qr', [
            'qr_code_value' => 'POST-TEST123',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test checkpoint GPS proximity requires authentication.
     */
    public function test_checkpoint_gps_requires_auth(): void
    {
        $response = $this->postJson('/api/tracking/checkpoint/gps', [
            'latitude' => -1.6974,
            'longitude' => 101.2642,
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test checkpoint manual requires authentication.
     */
    public function test_checkpoint_manual_requires_auth(): void
    {
        $response = $this->postJson('/api/tracking/checkpoint/manual', [
            'post_id' => 1,
            'latitude' => -1.6974,
            'longitude' => 101.2642,
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test progress requires authentication.
     */
    public function test_progress_requires_auth(): void
    {
        $response = $this->getJson('/api/tracking/progress/some-uuid');
        $response->assertStatus(401);
    }

    /**
     * Test posts listing requires authentication.
     */
    public function test_posts_listing_requires_auth(): void
    {
        $response = $this->getJson('/api/tracking/posts/1');
        $response->assertStatus(401);
    }

    /**
     * Test GPS upload validation - invalid latitude.
     */
    public function test_gps_upload_validates_coordinates(): void
    {
        $user = \App\Models\User::first();
        if (!$user) {
            $this->markTestSkipped('No user in database');
        }

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/tracking/gps', [
            'latitude' => 999,
            'longitude' => 101.2642,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['latitude']);
    }

    /**
     * Test emergency trigger validation.
     */
    public function test_emergency_trigger_validates_input(): void
    {
        $user = \App\Models\User::first();
        if (!$user) {
            $this->markTestSkipped('No user in database');
        }

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/emergency/trigger', [
            'title' => '',
            'severity' => 'invalid',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'description', 'severity']);
    }

    /**
     * Test API fallback returns 404 JSON.
     */
    public function test_api_fallback_returns_json_404(): void
    {
        $response = $this->getJson('/api/nonexistent-route');

        $response->assertStatus(404);
        $response->assertJson(['success' => false]);
    }
}
