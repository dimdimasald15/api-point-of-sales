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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstname', 100)->nullable(false);
            $table->string('lastname', 100)->nullable(false);
            $table->string('phone_number', 30)->nullable(false);
            $table->string('email', 100)->nullable(false)->unique('users_email_unique');
            $table->string('address', 255)->nullable(false);
            $table->string('city', 100)->nullable(false);
            $table->string('province', 100)->nullable(false);
            $table->string('postal_code', 20)->nullable(false);
            $table->string('country', 100)->nullable(false);
            $table->enum('role', ['employee', 'customer', 'supplier'])->nullable(false);
            $table->text('comments');
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
        Schema::dropIfExists('users');
    }
};
