<?php

use App\Statuses\CheckType;
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

    //  foreign_php artisan make:migration create_posts_tablekeys
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->longText('commercial_record')->nullable();
            $table->date('start_commercial_record')->nullable();
            $table->date('end_commercial_record')->nullable();
            $table->boolean('percentage')->default(false);
            $table->tinyInteger('check_type')->default(CheckType::MAC_ADDRESS);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
};