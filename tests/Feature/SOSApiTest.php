<?php

namespace Tests\Feature;

use Tests\TestCase;

class SOSApiTest extends TestCase
{
    /**
     * Test SOS trigger requires auth.
     */
    public function test_sos_trigger_requires_auth(): void
    {
        $response = $this->postJson('/api/sos/trigger', [
            'latitude' => -1.6974,
            'longitude' => 101.2642,
            'severity' => 'high',
        ]);
        $response->assertStatus(401);
    }

    /**
     * Test SOS active requires auth.
     */
    public function test_sos_active_requires_auth(): void
    {
        $response = $this->getJson('/api/sos/active');
        $response->assertStatus(401);
    }

    /**
     * Test SOS chat send requires auth.
     */
    public function test_sos_chat_send_requires_auth(): void
    {
        $response = $this->postJson('/api/sos/chat/1/send', [
            'type' => 'text',
            'content' => 'Help',
        ]);
        $response->assertStatus(401);
    }

    /**
     * Test SOS chat messages requires auth.
     */
    public function test_sos_chat_messages_requires_auth(): void
    {
        $response = $this->getJson('/api/sos/chat/1/messages');
        $response->assertStatus(401);
    }

    /**
     * Test SOS call-options requires auth.
     */
    public function test_sos_call_options_requires_auth(): void
    {
        $response = $this->getJson('/api/sos/call-options');
        $response->assertStatus(401);
    }

    /**
     * Test disaster report requires auth.
     */
    public function test_disaster_report_requires_auth(): void
    {
        $response = $this->postJson('/api/sos/disaster-report', [
            'potensi_bencana' => 'Longsor',
            'deskripsi' => 'Test',
            'lokasi' => 'Pos 2',
        ]);
        $response->assertStatus(401);
    }

    /**
     * Test my disaster reports requires auth.
     */
    public function test_my_disaster_reports_requires_auth(): void
    {
        $response = $this->getJson('/api/sos/disaster-reports');
        $response->assertStatus(401);
    }

    /**
     * Test SOS trigger validation.
     */
    public function test_sos_trigger_validates_input(): void
    {
        $user = \App\Models\User::first();
        if (!$user) $this->markTestSkipped('No user');

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/sos/trigger', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['latitude', 'longitude', 'severity']);
    }

    /**
     * Test SOS trigger invalid severity.
     */
    public function test_sos_trigger_validates_severity(): void
    {
        $user = \App\Models\User::first();
        if (!$user) $this->markTestSkipped('No user');

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/sos/trigger', [
            'latitude' => -1.6974,
            'longitude' => 101.2642,
            'severity' => 'extreme',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['severity']);
    }

    /**
     * Test SOS trigger returns 403 without active booking.
     */
    public function test_sos_trigger_requires_active_booking(): void
    {
        $user = \App\Models\User::where('role', 'user')->first();
        if (!$user) $this->markTestSkipped('No user');

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/sos/trigger', [
            'latitude' => -1.6974,
            'longitude' => 101.2642,
            'severity' => 'high',
        ]);
        $response->assertStatus(403);
        $response->assertJson(['success' => false, 'message' => 'Tidak ada pendakian aktif']);
    }

    /**
     * Test disaster report validation.
     */
    public function test_disaster_report_validates_input(): void
    {
        $user = \App\Models\User::first();
        if (!$user) $this->markTestSkipped('No user');

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/sos/disaster-report', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['potensi_bencana', 'deskripsi', 'lokasi']);
    }

    /**
     * Test call-options returns proper format (even if no settings configured).
     */
    public function test_call_options_returns_array(): void
    {
        $user = \App\Models\User::first();
        if (!$user) $this->markTestSkipped('No user');

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/sos/call-options');
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure(['success', 'message', 'data']);
    }
}
