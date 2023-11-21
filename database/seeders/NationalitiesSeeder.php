<?php

namespace Database\Seeders;

use App\Models\Nationalitie;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NationalitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('nationalities')->delete();


        $nationals = [
            ['name' => 'Saudi Arabian', 'name_arabic' => 'السعودية'],
            ['name' => 'Syrian', 'name_arabic' => 'سوريا'],
            ['name' => 'Afghan', 'name_arabic' => 'أفغانستاني'],
            ['name' => 'Albanian', 'name_arabic' => 'ألباني'],
            ['name' => 'Algerian', 'name_arabic' => 'الجزائري'],
            ['name' => 'Argentinian', 'name_arabic' => 'الأرجنتيني'],
            ['name' => 'Bahraini', 'name_arabic' => 'بحريني'],
            ['name' => 'Bangladeshi', 'name_arabic' => 'بنغلادشي'],
            ['name' => 'Belarusian', 'name_arabic' => 'بيلاروسي'],
            ['name' => 'Belgian', 'name_arabic' => 'بلجيكي'],
            ['name' => 'Egyptian', 'name_arabic' => 'مصري'],
            ['name' => 'Indian', 'name_arabic' => 'هندي'],
            ['name' => 'Iraqi', 'name_arabic' => 'عراقي'],
            ['name' => 'Irish', 'name_arabic' => 'أيرلندي'],
            ['name' => 'Italian', 'name_arabic' => 'إيطالي'],
            ['name' => 'Jordanian', 'name_arabic' => 'أردني'],
            ['name' => 'Kuwaiti', 'name_arabic' => 'كويتي'],
            ['name' => 'Libyan', 'name_arabic' => 'ليبي'],
            ['name' => 'Moroccan', 'name_arabic' => 'مغربي'],
            ['name' => 'Pakistani', 'name_arabic' => 'باكستاني'],
            ['name' => 'Palestinian', 'name_arabic' => 'فلسطيني'],
            ['name' => 'Filipino', 'name_arabic' => 'فلبيني'],
            ['name' => 'Polish', 'name_arabic' => 'بولندي'],
            ['name' => 'Portuguese', 'name_arabic' => 'برتغالي'],
            ['name' => 'Romanian', 'name_arabic' => 'روماني'],
            ['name' => 'Qatari', 'name_arabic' => 'قطري'],
            ['name' => 'Russian', 'name_arabic' => 'روسي'],
            ['name' => 'Singaporean', 'name_arabic' => 'سنغافوري'],
            ['name' => 'Somali', 'name_arabic' => 'صومالي'],
            ['name' => 'Sudanese', 'name_arabic' => 'سوداني'],
            ['name' => 'Swedish', 'name_arabic' => 'سويدي'],
            ['name' => 'Swiss', 'name_arabic' => 'سويسري'],
            ['name' => 'Taiwanese', 'name_arabic' => 'تايواني'],
            ['name' => 'Tajikistani', 'name_arabic' => 'طاجيكي'],
            ['name' => 'Thai', 'name_arabic' => 'تايلاندي'],
            ['name' => 'Tunisian', 'name_arabic' => 'تونسي'],
            ['name' => 'Turkish', 'name_arabic' => 'تركي'],
            ['name' => 'Ukrainian', 'name_arabic' => 'أوكراني'],
            ['name' => 'Emirati', 'name_arabic' => 'إماراتي'],
            ['name' => 'British', 'name_arabic' => 'بريطاني'],
            ['name' => 'American', 'name_arabic' => 'أمريكي'],
            ['name' => 'Uruguayan', 'name_arabic' => 'أوروغواي'],
            ['name' => 'Uzbek', 'name_arabic' => 'أوزبكي'],
            ['name' => 'Venezuelan', 'name_arabic' => 'فنزويلي'],
            ['name' => 'Yemeni', 'name_arabic' => 'يمني'],
            ['name' => 'South African', 'name_arabic' => 'جنوب أفريقي'],
            ['name' => 'Serbian', 'name_arabic' => 'صربي'],
            ['name' => 'Panamanian', 'name_arabic' => 'بنمي'],
            ['name' => 'Omani', 'name_arabic' => 'عماني'],
            ['name' => 'Norwegian', 'name_arabic' => 'نرويجي'],
            ['name' => 'Nigerian', 'name_arabic' => 'نيجيري'],
            ['name' => 'Dutch', 'name_arabic' => 'هولندي'],
            ['name' => 'Mexican', 'name_arabic' => 'مكسيكي'],
            ['name' => 'Mauritanian', 'name_arabic' => 'موريتاني'],
            ['name' => 'Malaysian', 'name_arabic' => 'ماليزي'],
            ['name' => 'Japanese', 'name_arabic' => 'ياباني'],
        ];


        foreach ($nationals as $nationality) {
            Nationalitie::create($nationality);
        }
    }
}
