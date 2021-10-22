<?php

use Illuminate\Database\Seeder;

class QuestionTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('question_types')->insert([
            [
                'id' => 1,
                'alias' => 'text',
                'name' => 'Single-line text box',
                'has_options' => 0,
                'allow_multiple' => 0,
            ],
            [
                'id' => 2,
                'alias' => 'textarea',
                'name' => 'Multi-line text box',
                'has_options' => 0,
                'allow_multiple' => 0,
            ],
            [
                'id' => 3,
                'alias' => 'dropdown',
                'name' => 'Dropdown (single selection)',
                'has_options' => 1,
                'allow_multiple' => 0,
            ],
            [
                'id' => 4,
                'alias' => 'dropdown_multiple',
                'name' => 'Dropdown (multiple selection)',
                'has_options' => 1,
                'allow_multiple' => 1,
            ],
            [
                'id' => 5,
                'alias' => 'checkbox',
                'name' => 'Checkbox',
                'has_options' => 1,
                'allow_multiple' => 1,
            ],
            [
                'id' => 6,
                'alias' => 'radio',
                'name' => 'Radio input',
                'has_options' => 1,
                'allow_multiple' => 0,
            ],
        ]);
    }
}
