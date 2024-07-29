<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticlesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $articles = [
            [
                'title' => 'First Article',
                'content' => 'This is the content of the first article.',
                'created_at' => Carbon::now(),
            ],
            [
                'title' => 'Second Article',
                'content' => 'This is the content of the second article.',
                'created_at' => Carbon::now(),
            ],
            [
                'title' => 'Third Article',
                'content' => 'This is the content of the third article.',
                'created_at' => Carbon::now(),
            ],
        ];

        // Upsert data: Specify columns that should be unique to prevent duplicates
        DB::table('articles')->upsert(
            $articles,
            ['title'], // Unique column(s) to check for duplicates
            ['content', 'created_at'] // Columns to update if a duplicate is found
        );
    }
}
