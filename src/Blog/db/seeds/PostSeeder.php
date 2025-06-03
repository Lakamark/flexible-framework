<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class PostSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run(): void
    {
        // Seeding categories
        $data = [];
        $faker = Faker\factory::create();

        for ($i = 0; $i < 5; $i++) {
            $data[] = [
                'name' => $faker->sentence,
                'slug' => $faker->slug,
            ];
        }

        $this->table('categories')->insert($data)->save();

        // seedin posts
        $data = [];
        for ($i = 0; $i < 100; $i++) {
            $date = date('Y-m-d H:i:s', $faker->unixTime('now'));
            $data[] = [
                'name' => $faker->sentence,
                'slug' => $faker->slug,
                'category_id' => rand(1, 5),
                'content' => $faker->text(3000),
                'created_at' => $date,
                'updated_at' => $date,
            ];
        }

        $this->table('posts')->insert($data)->save();
    }
}
