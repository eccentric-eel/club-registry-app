<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Record;

class RecordFactory extends Factory
{
    protected $model = Record::class;

    public function definition()
    {
        return [
            'firstname'                  => fake()->firstName(),
            'surname'                    => fake()->lastName(),
            'address'                    => fake()->address(),
            'guest_type'                 => fake()->numberBetween(1, 2),
            'accompanying_member_name'   => fake()->name(),
            'accompanying_member_number' => fake()->randomNumber(8, true)
        ];
    }
}
