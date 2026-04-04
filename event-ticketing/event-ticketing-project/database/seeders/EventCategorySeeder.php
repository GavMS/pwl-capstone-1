<?php

namespace Database\Seeders;

use App\Models\EventCategories;
use Illuminate\Database\Seeder;

class EventCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Tech Workshop',
                'slug' => 'tech-workshop',
                'description' => 'Hands-on workshop to improve practical technology skills.',
            ],
            [
                'name' => 'Developer Conference',
                'slug' => 'developer-conference',
                'description' => 'Conference for developers to learn trends, tools, and best practices.',
            ],
            [
                'name' => 'Startup Networking',
                'slug' => 'startup-networking',
                'description' => 'Networking session for founders, students, and professionals.',
            ],
            [
                'name' => 'Music Concert',
                'slug' => 'music-concert',
                'description' => 'Live music performances by various artists and bands.',
            ],
            [
                'name' => 'Comedy Show',
                'slug' => 'comedy-show',
                'description' => 'Stand-up comedy events and improv shows.',
            ],
            [
                'name' => 'Art Exhibition',
                'slug' => 'art-exhibition',
                'description' => 'Showcases of paintings, sculptures, and contemporary art.',
            ],
            [
                'name' => 'Virtual Run',
                'slug' => 'virtual-run',
                'description' => 'Online marathon and fitness events you can do anywhere.',
            ],
            [
                'name' => 'Food Festival',
                'slug' => 'food-festival',
                'description' => 'Culinary events featuring local and international cuisines.',
            ],
            [
                'name' => 'E-Sports Tournament',
                'slug' => 'esports-tournament',
                'description' => 'Competitive gaming events with professional teams.',
            ],
            [
                'name' => 'Theater Performance',
                'slug' => 'theater-performance',
                'description' => 'Stage plays, musicals, and dramatic arts.',
            ],
        ];

        foreach ($categories as $category) {
            EventCategories::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}