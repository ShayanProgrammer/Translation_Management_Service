<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class TranslationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_translation()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/translations', [
            'key' => 'welcome',
            'translations' => [
                'en' => 'Welcome',
                'fr' => 'Bienvenue'
            ],
            'tags' => ['web']
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['key' => 'welcome']);
    }

    public function test_guest_cannot_create_translation()
    {
        $response = $this->postJson('/api/translations', [
            'key' => 'unauth',
            'translations' => [
                'en' => 'Test'
            ]
        ]);

        $response->assertStatus(401);
    }
}