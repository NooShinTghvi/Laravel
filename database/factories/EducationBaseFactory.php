<?php

namespace Database\Factories;

use App\Models\EducationBase;
use Illuminate\Database\Eloquent\Factories\Factory;

class EducationBaseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EducationBase::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws \Exception
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->firstName . ' edctnbs',
            'description' => $this->faker->text(125),
        ];
    }
}
