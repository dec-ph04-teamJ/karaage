<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User_group>
 */
class User_groupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            // 'user_id'=>$this->faker->unique()->numberBetween(1, 10),
            'user_id'=>$this->faker->numberBetween(1, 10),
            'group_id'=>$this->faker->numberBetween(1, 2),
        ];
    }
}
