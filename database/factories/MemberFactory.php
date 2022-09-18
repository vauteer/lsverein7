<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\Section;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Member>
 */
class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $gender = rand(0, 1) === 0 ? 'male' : 'female';
        return [
            'club_id' => 1,
            'first_name' => fake()->firstName($gender),
            'surname' => fake()->lastName(),
            'gender' => substr($gender, 0, 1),
            'birthday' => fake()->dateTimeBetween('-80 years', '-3 years'),
            'street' => fake()->streetAddress(),
            'zipcode' => fake()->postcode(),
            'city' => fake()->city(),
            'email' => fake()->email(),
            'phone' => fake()->phoneNumber(),
            'payment_method' => 'r',
        ];
    }

    public function club($clubId)
    {
        return $this->state(function (array $attributes) use ($clubId) {
            return [
                'club_id' => $clubId,
            ];
        });
    }

    public function configure()
    {
        return $this->afterCreating(function (Member $member) {
            $clubId = $member->club_id;
            $from = new Carbon(fake()->dateTimeBetween("-{$member->age} years", 'now'));
            $to = new Carbon(fake()->dateTimeBetween("-{$member->age} years", 'now'));
            if ($to < $from)
                $to = null;

            $member->memberships()->attach($clubId, [
                'from' => $from,
                'to' => $to,
            ]);

            $section = Section::withoutGlobalScopes()->where('club_id', $clubId)->inRandomOrder()->first();
            if ($section) {
                $member->sections()->attach($section->id, [
                    'from' => $from,
                    'to' => $to,
                ]);
            }
        });
    }
}
