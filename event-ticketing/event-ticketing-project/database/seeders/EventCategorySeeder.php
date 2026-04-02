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
        ];

        foreach ($categories as $category) {
            EventCategories::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}