<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Translation;

class ExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_returns_json_structure()
    {
        $user = User::factory()->create();

        // Seed with fake translations
        Translation::factory()->count(50)->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/translations/export');

        $response->assertStatus(200)
                 ->assertJsonStructure(['en']);
    }
}