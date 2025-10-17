<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'fhir_id' => Str::uuid()->toString(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'date_of_birth' => $this->faker->date(),
            'gender' => $this->faker->randomElement(['female', 'male', 'other', 'unknown']),
            'contact_phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->safeEmail(),
            'blood_type' => $this->faker->randomElement(['A', 'B', 'AB', 'O']),
            'rh_factor' => $this->faker->randomElement(['positive', 'negative']),
            'allergies' => $this->faker->randomElements(['penicillin', 'pollen', 'latex'], rand(0, 3)),
            'address' => [
                'line' => [$this->faker->streetAddress()],
                'city' => $this->faker->city(),
                'state' => $this->faker->stateAbbr(),
                'postalCode' => $this->faker->postcode(),
            ],
            'gravidity' => (string) $this->faker->numberBetween(1, 3),
            'parity' => (string) $this->faker->numberBetween(0, 2),
            'estimated_due_date' => $this->faker->dateTimeBetween('+1 week', '+6 months'),
            'last_menstrual_period' => $this->faker->dateTimeBetween('-6 months', '-1 month'),
        ];
    }
}

