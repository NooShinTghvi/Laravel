<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\EducationBase;
use App\Models\Field;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws \Exception
     */
    public function definition(): array
    {
        $permitted_chars = '0123456789012345678901234567890123456789';
        $fields = Field::all();
        $educationBases = EducationBase::all();
        $cities = City::all();
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'melli_code' => substr(str_shuffle($permitted_chars), 0, 10),
            'mobile' => '09' . substr(str_shuffle($permitted_chars), 0, 9),
            'field_id' => $fields[random_int(1, sizeof($fields) - 1)]->id,
            'education_base_id' => $educationBases[random_int(1, sizeof($educationBases) - 1)]->id,
            'city_id' => $cities[random_int(1, sizeof($cities) - 1)]->id,
            'melli_image_path' => $this->faker->imageUrl(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
