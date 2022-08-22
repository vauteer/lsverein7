<?php

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
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('street');
            $table->string('zipcode', 10);
            $table->string('city');
            $table->boolean('blsv_member');
            $table->string('bank');
            $table->string('account_owner');
            $table->string('iban');
            $table->string('bic');
            $table->string('sepa')->nullable();
            $table->date('sepa_date')->nullable();
            $table->string('logo')->nullable();
            $table->tinyInteger('display')->default(1);
            $table->string('locale', 5)->default('de');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clubs');
    }
};
