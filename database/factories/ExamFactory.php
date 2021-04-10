<?php

namespace Database\Factories;

use App\Models\EducationBase;
use App\Models\Exam;
use App\Models\Field;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Exam::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $fields = Field::all('id');
        $educationBases = EducationBase::all('id');
        return [
            'name' => $this->faker->unique()->firstName . ' exam',
            'number_of_phases' => $this->faker->numberBetween(4, 12),
            'day_of_holding' => $this->faker->randomElement(['شنبه', 'یک شنبه', 'دو شنبه', 'سه شنبه', 'چهارشنبه', 'پنج شنبه', 'جمعه']),
            'field_id' => $fields[$this->faker->numberBetween(1, sizeof($fields) - 1)]->id,
            'education_base_id' => $educationBases[$this->faker->numberBetween(1, sizeof($educationBases) - 1)]->id,
            'price' => $this->faker->numberBetween(15, 75) * 10000,
            'description' => $this->faker->text(125),
        ];
    }
}
