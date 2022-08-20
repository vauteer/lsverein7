<?php

use App\Models\Event;
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
        Event::create(['id' => 1, 'name' => '25 Jahre']);
        Event::create(['id' => 2, 'name' => '30 Jahre']);
        Event::create(['id' => 3, 'name' => '40 Jahre']);
        Event::create(['id' => 4, 'name' => '50 Jahre']);
        Event::create(['id' => 5, 'name' => '60 Jahre']);
        Event::create(['id' => 6, 'name' => '70 Jahre']);
        Event::create(['id' => 7, 'name' => 'Ehrenvorstand']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Event::where('id', '<', 10)->delete();
    }
};
