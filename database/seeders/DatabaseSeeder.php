<?php

namespace Database\Seeders;

use App\Statuses\GenderStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(NationalitiesSeeder::class);

        $filename = 'avatar.jpg';
        $path = 'images/' . $filename;

        \App\Models\User::factory()->create([
            "name" => "Super Admin",
            "email" => "super_admin@admin.com",
            "password" => bcrypt('0123456789'),
            "departement" => "Admin System",
            "image" => $path,
            "gender" => GenderStatus::MALE,
            "type" => 1,
            "status" => 1,
            "company_id" => null,
            "phone" => "0969040382",
            "serial_number" => "000000",
            "nationalitie_id" => 1
        ]);
    }
}
