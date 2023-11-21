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
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('company_id')->unsigned()->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->decimal('salary', 10, 2);
            $table->decimal('basic_salary', 10, 2);
            $table->tinyInteger('rewards_type')->nullable();
            $table->tinyInteger('adversaries_type')->nullable();
            $table->decimal('rewards', 10, 2);
            $table->decimal('adversaries', 10, 2);
            $table->decimal('housing_allowance', 10, 2);
            $table->decimal('transportation_allowance', 10, 2);
            $table->date('date');
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
        Schema::dropIfExists('salaries');
    }
};
