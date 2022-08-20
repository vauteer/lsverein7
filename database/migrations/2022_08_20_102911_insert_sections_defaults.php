<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Section;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Section::create(['id' => 1, 'name' => 'Badminton']);
        Section::create(['id' => 9, 'name' => 'Fussball']);
        Section::create(['id' => 30, 'name' => 'Skisport']);
        Section::create(['id' => 32, 'name' => 'Tennis']);
        Section::create(['id' => 33, 'name' => 'Tischtennis']);
        Section::create(['id' => 99, 'name' => 'Sonstige']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Section::where('id', '<', 100)->delete();
    }
};
