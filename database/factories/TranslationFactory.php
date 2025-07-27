<?php
namespace Database\Factories;

use App\Models\Translation;
use Illuminate\Database\Eloquent\Factories\Factory;

class TranslationFactory extends Factory {
    protected $model = Translation::class;

    public function definition(): array {
        return [
            'key' => $this->faker->unique()->slug,
            'translations' => [
                'en' => $this->faker->sentence,
                'fr' => $this->faker->sentence,
                'es' => $this->faker->sentence,
            ],
            'tags' => $this->faker->randomElements(['web', 'mobile', 'desktop'], rand(1, 2)),
        ];
    }
}