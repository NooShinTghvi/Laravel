<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\EducationBase;
use App\Models\Field;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

class LessonFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Lesson::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $fields = Field::all('id');
        $educationBases = EducationBase::all('id');
        $categories = Category::all('id');
        return [
            'name' => $this->faker->unique()->firstName . ' lesson',
            'number_of_questions' => $this->faker->randomElement([10, 15, 20, 25, 30]),
            'coefficient' => $this->faker->randomElement([1, 2, 3, 4, 5, 6]),
            'field_id' => $fields[$this->faker->numberBetween(1, sizeof($fields) - 1)]->id,
            'education_base_id' => $educationBases[$this->faker->numberBetween(1, sizeof($educationBases) - 1)]->id,
            'category_id' => $categories[$this->faker->numberBetween(1, sizeof($categories) - 1)]->id,
            'description' => $this->faker->paragraph(2)
        ];
    }
}
