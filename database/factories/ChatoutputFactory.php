<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chatoutput>
 */
class ChatoutputFactory extends Factory
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
            'score'=>$this->faker->numberBetween(1, 99),
            'input_id' =>$this->faker->unique()->numberBetween(1,40),
        ];
    }
}
