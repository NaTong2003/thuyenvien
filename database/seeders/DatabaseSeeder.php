<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            PositionsSeeder::class,
            ShipTypesSeeder::class,
            AdminUserSeeder::class,
            SeafarerUserSeeder::class,
            QuestionsSeeder::class,
            AnswersSeeder::class,
            TestsSeeder::class,
            TestQuestionsSeeder::class,
            TestAttemptsSeeder::class,
            CategorySeeder::class,
        ]);
    }
}
