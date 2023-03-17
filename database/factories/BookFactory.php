<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Status;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => fake()->sentence,
            'content' => fake()->paragraphs(3, true),
            'location'=> fake()->sentence,
            'download_link' => fake()->url(),
            'description' => fake()->paragraphs(3, true),
            'image' => fake()->imageUrl(),
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'status_id' => Status::factory(),
        ];
    }
}
