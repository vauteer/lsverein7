<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\InvokableRule;

class UniqueUser implements InvokableRule
{
    private int $ignoreId;

    public function __construct($ignoreId)
    {
        $this->ignoreId = $ignoreId;
    }

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        $user = User::where('email', $value)->first();

        if ($user && $user->id !== $this->ignoreId && $user->clubs->contains(currentClubId())) {
            $fail("Den Benutzer gibt es schon.");
        }
    }
}
