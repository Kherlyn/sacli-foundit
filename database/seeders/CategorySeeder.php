<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'description' => 'Mobile phones, laptops, tablets, headphones, and other electronic devices',
                'icon' => 'device-mobile',
                'color' => '#10B981'
            ],
            [
                'name' => 'Clothing',
                'description' => 'Jackets, bags, shoes, accessories, and personal clothing items',
                'icon' => 'shirt',
                'color' => '#059669'
            ],
            [
                'name' => 'Documents',
                'description' => 'ID cards, passports, licenses, certificates, and important papers',
                'icon' => 'document-text',
                'color' => '#047857'
            ],
            [
                'name' => 'Keys',
                'description' => 'House keys, car keys, keychains, and access cards',
                'icon' => 'key',
                'color' => '#065F46'
            ],
            [
                'name' => 'Jewelry',
                'description' => 'Rings, necklaces, watches, bracelets, and valuable accessories',
                'icon' => 'sparkles',
                'color' => '#34D399'
            ],
            [
                'name' => 'Books & Media',
                'description' => 'Books, notebooks, CDs, DVDs, and educational materials',
                'icon' => 'book-open',
                'color' => '#6EE7B7'
            ],
            [
                'name' => 'Sports Equipment',
                'description' => 'Balls, rackets, gym equipment, and sporting accessories',
                'icon' => 'trophy',
                'color' => '#A7F3D0'
            ],
            [
                'name' => 'Personal Items',
                'description' => 'Wallets, purses, glasses, and other personal belongings',
                'icon' => 'user',
                'color' => '#D1FAE5'
            ],
            [
                'name' => 'Pets',
                'description' => 'Lost or found pets and pet accessories',
                'icon' => 'heart',
                'color' => '#ECFDF5'
            ],
            [
                'name' => 'Other',
                'description' => 'Items that don\'t fit into other categories',
                'icon' => 'question-mark-circle',
                'color' => '#F0FDF4'
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
