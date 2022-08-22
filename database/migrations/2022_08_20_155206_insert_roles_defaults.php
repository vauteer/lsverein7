<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Role::create(['id' => 1, 'name' => '1. Vorstand']);
        Role::create(['id' => 2, 'name' => '2. Vorstand']);
        Role::create(['id' => 3, 'name' => 'Kassier']);
        Role::create(['id' => 7, 'name' => 'Schriftführer']);
        Role::create(['id' => 8, 'name' => 'Ehrenamtsbeauftragter']);
        Role::create(['id' => 9, 'name' => 'Beisitzer']);
        Role::create(['id' => 10, 'name' => 'Kassenprüfer']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Role::where('id', '<', 10)->delete();
    }
};
