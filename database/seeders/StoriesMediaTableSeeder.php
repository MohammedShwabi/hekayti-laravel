<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoriesMediaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $texts = [
            'الأَشْكَالُ الْمُلَوَّنَةُ',
            'دَائِرَةٌ حَمْرَاءُ',
            'مُرَبَّعٌ أَزْرَقُ',
            'مُثَلَّثٌ أَخْضَرُ',
            'بَيْضَاوِيٌّ بُرْتُقَالِيٌّ',
            'مُسْتَطِيْلٌ زَهْرِيٌّ',
            'نَجْمَةٌ صَفْرَاءُ', 
            'مَعِيْنٌ بَنَفْسَجِيُّ',
            'هِلَالٌ رَمَادِيٌّ',
            'خُمَاسِيٌّ بُنِّيٌّ',
        ];
        $text_no_desc =[
            'الاشكال الملونة',
            'دائرة حمراء',
            'مربع ازرق',
            'مثلث اخضر', 
            'بيضاوي برتقالي',
            'مستطيل زهري',
            'نجمة صفراء',
            'معين بنفسجي',
            'خماسي بني',
            'هلال رمادي',

        ];

        // Insert your data for 'stories_media' table
        for ($i = 0; $i <= 9; $i++) {
            DB::table('stories_media')->insert([
                'page_no' => $i,
                'story_id' => 1,
                'image' => '1_5_' . $i . '.jpg',
                'audio' => '1_5_' . $i . '.mp3',
                'text' => $texts[$i], // Use the corresponding text for this page
                'text_no_desc' => $text_no_desc[$i], // Use the corresponding text_no_desc for this page
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
