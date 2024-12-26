<?php

namespace Database\Factories;

use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'module_id' => Module::inRandomOrder()->first()->id,
            'status_id' => fake()->numberBetween(1, 3),
            'designated_id' => User::inRandomOrder()->first()->id,
            'date' => fake()->dateTimeBetween('-1 week', '+1 month'),
            'name' => fake()->sentence,
            'phrase' => fake()->sentence,
            'description' => fake()->paragraph,
            'created_by' => 1,
        ];
    }
}
