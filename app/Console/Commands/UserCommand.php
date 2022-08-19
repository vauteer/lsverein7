<?php

namespace App\Console\Commands;

use App\Models\Club;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:user {name} {email} {--password=} {--club=} {--A|admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or edit a user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): void
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->option('password');

        $user = \App\Models\User::firstOrNew(
            ['name' => $name, 'email' => $email]
        );

        if ($user->created_by === null) {
            $admin = \App\Models\User::where('admin')->first();
            if ($admin)
                $user->created_by = $admin->id;
        }

        if ($password) {
            $user->password = Hash::make($password);
        } else if (!$user->password) {
            $password = Str::random(8);
            $user->password = Hash::make($password);
        }

        if ($this->option('admin'))
            $user->admin = 1;

        $isNew = !$user->exists;
        try {
            $user->save();
        } catch (\Exception $ex) {
            $this->info("User creation failed.");
            $this->info($ex->getMessage());
            return;
        }

        if ($isNew) {
            $clubId = $this->option('club');
            if ($clubId) {
                $user->clubs()->attach($clubId);
                $this->info("Created User {$name} for club {$clubId}. Password {$password}");
            }
        } else {
            $info = "Updated user {$name}";
            if ($password)
                $info .= ". The password is {$password}";
            $this->info($info);
        }
    }
}
