<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chatinput>
 */
class ChatinputFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            //
            'user_id' => $this->faker->numberBetween(1, 10),
            'group_id' => $this->faker->numberBetween(1, 5),
            'sentence' => $this->faker->realText(20),
        ];
    }
}
