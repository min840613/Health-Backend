<?php

namespace Database\Factories\Articles;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class ArticleModelFactory
 * @package Database\Factories
 */
class ArticleModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'articles_status' => fake()->randomElement([0, 1]),
            'publish' => fake()->dateTime,
            'title' => fake()->name,
            'og_title' => fake()->name,
            'seo_title' => fake()->name,
            'author' => fake()->randomNumber(),
            'author_type' => fake()->randomElement([1, 2]),
            'medicine_article_category_id' => fake()->randomNumber(),
            'talent_category_id' => fake()->randomNumber(),
            'adult_flag' => fake()->randomElement([0, 1]),
            'image' => fake()->imageUrl,
            'image_alt' => fake()->text(100),
            'ogimage' => fake()->imageUrl,
            'video_type' => fake()->randomElement(['youtube']),
            'video_id' => '',
            'fb_ia_video' => '',
            'tag' => '',
            'match_searchs' => fake()->text,
            'extended_article' => '',
            'article_content' => fake()->realText,
            'is_line_rss' => fake()->randomElement([0, 1]),
            'video_file_name' => fake()->filePath(),
            'is_zimedia' => fake()->randomElement([1, 2]),
            'is_yahoo_rss' => fake()->randomElement([0, 1]),
            'created_user' => fake()->name,
            'updated_user' => fake()->name,
            'match_url' => fake()->text(100),
            'master_id' => fake()->randomNumber(),
            'is_mixerbox_article' => fake()->randomElement([0, 1]),
        ];
    }

    public function published(): Factory
    {
        return $this->state(function () {
            return [
                'articles_status' => 1,
                'publish' => now()->subDay()->toDateTimeString(),
            ];
        });
    }

    public function unpublished(): Factory
    {
        return $this->state(function () {
            return [
                'articles_status' => 0,
            ];
        });
    }

    public function notAdult(): Factory
    {
        return $this->state(function () {
            return [
                'adult_flag' => 0,
            ];
        });
    }

    public function isAdult(): Factory
    {
        return $this->state(function () {
            return [
                'adult_flag' => 1,
            ];
        });
    }
}
