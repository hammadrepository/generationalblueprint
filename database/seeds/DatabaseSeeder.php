<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        DB::table('question_options')->insert([
            [
                'question_id'=> 1,
                'option'=> 'Bachelor +',
                'is_last'=> false,
                'next_question_id'=> 2,
                'group_id'=> null,
            ],
            [
                'question_id'=> 1,
                'option'=> 'High School',
                'is_last'=> false,
                'next_question_id'=> 3,
                'group_id'=> null,
            ],
            [
                'question_id'=> 2,
                'option'=> 'Yes',
                'is_last'=> false,
                'next_question_id'=> 4,
                'group_id'=> null,
            ],
            [
                'question_id'=> 2,
                'option'=> 'No',
                'is_last'=> false,
                'next_question_id'=> 3,
                'group_id'=> null,
            ],
            [
                'question_id'=> 3,
                'option'=> '>$250k',
                'is_last'=> false,
                'next_question_id'=> 7,
                'group_id'=> null,
            ],
            [
                'question_id'=> 3,
                'option'=> '$60k-$250K',
                'is_last'=> false,
                'next_question_id'=> 6,
                'group_id'=> null,
            ],
            [
                'question_id'=> 3,
                'option'=> '<$60k',
                'is_last'=> false,
                'next_question_id'=> 5,
                'group_id'=> null,
            ],
            [
                'question_id'=> 4,
                'option'=> 'No',
                'is_last'=> false,
                'next_question_id'=> 3,
                'group_id'=> null,
            ],
            [
                'question_id'=> 4,
                'option'=> 'Yes',
                'is_last'=> false,
                'next_question_id'=> 7,
                'group_id'=> null,
            ],
            [
                'question_id'=> 5,
                'option'=> 'Job',
                'is_last'=> true,
                'next_question_id'=> null,
                'group_id'=> 1,
            ],
            [
                'question_id'=> 5,
                'option'=> 'Career',
                'is_last'=> true,
                'next_question_id'=> null,
                'group_id'=> 2,
            ],
            [
                'question_id'=> 6,
                'option'=> 'Yes',
                'is_last'=> false,
                'next_question_id'=> 7,
                'group_id'=> null,
            ],
            [
                'question_id'=> 6,
                'option'=> 'No',
                'is_last'=> true,
                'next_question_id'=> null,
                'group_id'=> 2,
            ],
            [
                'question_id'=> 7,
                'option'=> 'Yes',
                'is_last'=> false,
                'next_question_id'=> 8,
                'group_id'=> null,
            ],
            [
                'question_id'=> 7,
                'option'=> 'No',
                'is_last'=> false,
                'next_question_id'=> 9,
                'group_id'=> null,
            ],
            [
                'question_id'=> 8,
                'option'=> 'Yes',
                'is_last'=> true,
                'next_question_id'=> null,
                'group_id'=> 5,
            ],
            [
                'question_id'=> 8,
                'option'=> 'No',
                'is_last'=> true,
                'next_question_id'=> null,
                'group_id'=> 4,
            ],
            [
                'question_id'=> 9,
                'option'=> 'Grad. Degree',
                'is_last'=> false,
                'next_question_id'=> 10,
                'group_id'=> null,
            ],
            [
                'question_id'=> 9,
                'option'=> 'Prof. Degree',
                'is_last'=> true,
                'next_question_id'=> null,
                'group_id'=> 4,
            ],
            [
                'question_id'=> 10,
                'option'=> 'No',
                'is_last'=> true,
                'next_question_id'=> null,
                'group_id'=> 2,
            ],
            [
                'question_id'=> 10,
                'option'=> 'Yes',
                'is_last'=> true,
                'next_question_id'=> null,
                'group_id'=> 3,
            ],

        ]);
    }
}
