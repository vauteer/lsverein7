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
            $table->string('surname');
            $table->string('first_name');
            $table->char('gender', 1);
            $table->date('birthday');
            $table->date('death_day')->nullable();
            $table->string('street');
            $table->string('zipcode', 10);
            $table->string('city');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->char('payment_method', 1);
            $table->string('bank')->nullable();
            $table->string('account_owner')->nullable();
            $table->string('iban')->nullable();
            $table->string('bic')->nullable();
            $table->string('memo')->nullable();
            $table->timestamps();

            $table->index(['surname', 'first_name']);
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
