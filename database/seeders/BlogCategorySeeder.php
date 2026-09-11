<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Content\Models\BlogCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Career Development', 'Corporate Training', 'SAP & ERP',
            'Finance & Accounting', 'Data Analytics', 'Leadership',
            'Digital Transformation', 'Learning Tips',
        ];

        foreach ($categories as $order => $name) {
            BlogCategory::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'sort_order' => $order]
            );
        }
    }
}
