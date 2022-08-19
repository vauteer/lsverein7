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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained()->onDelete('cascade');
            $table->string('surname')->unique();
            $table->string('first_name')->unique();
            $table->tinyInteger('gender');
            $table->timestamp('birthday');
            $table->timestamp('death_day')->nullable();
            $table->string('street');
            $table->string('zipcode', 10);
            $table->string('city');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->tinyInteger('payment_method');
            $table->string('bank_name')->nullable();
            $table->string('account_owner')->nullable();
            $table->string('iban')->nullable();
            $table->string('bic')->nullable();
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
        Schema::dropIfExists('members');
    }
};
