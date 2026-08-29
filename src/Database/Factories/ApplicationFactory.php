<?php

namespace Shaka\AppReleaseManager\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Shaka\AppReleaseManager\Models\Application;

class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'slug' => 'app-'.$this->uniqueSlug(),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }

    protected function uniqueSlug(): string
    {
        return fake()->unique()->regexify('[a-z]{4}[0-9]{4}');
    }
}
