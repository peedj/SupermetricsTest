<?php

namespace ApiConnect\collections;

use ApiConnect\models\Post;

class StatsCollection
{
    public const CHAR_LENGTH = 'char_length';
    public const MAX_POST_LENGTH = 'max_char_length';
    public const MAX_POST = 'max_post';
    public const POSTS_PER_USER = 'posts_per_user';

    private array $monthData = [
        self::MAX_POST => [],
        self::MAX_POST_LENGTH => [],
        self::POSTS_PER_USER => [],
        self::CHAR_LENGTH => [],
    ];

    private array $weekNumberData = [];

    /**
     * @param Post[] $posts
     */
    public function appendPostsData(array $posts)
    {
        foreach ($posts as $post) {
            $charLength = $post->getCharLength();
            $month = $post->getMonth();

            $this->collectMonthlyCharLength($charLength, $month);
            $this->collectMonthlyMaxLengthPost($charLength, $month, $post);
            $this->collectPostsPerUserPerMonth($month, $post);
            $this->collectPostsByWeekNumber($post);

        }
    }

    public function getInfo(): array
    {
        return [
            'Average character length of posts per month' => $this->getAveragePostsPerMonth(),
            'Longest post by character length per month' => $this->getLongestPostPerMonth(),
            'Total posts split by week number' => $this->getTotalPostsByWeekNumber(),
            'Average number of posts per user per month' => $this->getAverageNumberOfPostsPerUserPerMonth(),
        ];
    }

    /**
     * @return array
     */
    private function getAveragePostsPerMonth(): array
    {
        $data = [];
        foreach ($this->monthData[self::CHAR_LENGTH] as $month => $monthData) {
            $data[$month] = round(array_sum($monthData) / count($monthData), 2);
        }

        return $data;
    }

    /**
     * @return array
     */
    private function getLongestPostPerMonth(): array
    {
        return $this->monthData[self::MAX_POST];
    }

    /**
     * @return array
     */
    private function getTotalPostsByWeekNumber(): array
    {
        return $this->weekNumberData;
    }

    /**
     * @return array
     */
    private function getAverageNumberOfPostsPerUserPerMonth(): array
    {
        $data = [];
        foreach ($this->monthData[self::POSTS_PER_USER] as $month => $monthData) {
            $data[$month] = round(array_sum($monthData) / count($monthData), 2);
        }

        return $data;
    }

    /**
     * @param int $charLength
     * @param string $month
     */
    private function collectMonthlyCharLength(int $charLength, string $month): void
    {
        $this->monthData[self::CHAR_LENGTH][$month][] = $charLength;
    }

    /**
     * @param int $charLength
     * @param string $month
     * @param Post $post
     */
    private function collectMonthlyMaxLengthPost(int $charLength, string $month, Post $post): void
    {
        if ($charLength > ($this->monthData[self::MAX_POST_LENGTH][$month] ?? 0)) {
            $this->monthData[self::MAX_POST_LENGTH][$month] = $charLength;
            $this->monthData[self::MAX_POST][$month] = $post;
        }
    }

    /**
     * @param string $month
     * @param Post $post
     */
    private function collectPostsPerUserPerMonth(string $month, Post $post): void
    {
        $this->monthData[self::POSTS_PER_USER][$month][$post->from_id] = ($this->monthData[self::POSTS_PER_USER][$month][$post->from_id] ?? 0) + 1;
    }

    /**
     * @param Post $post
     */
    private function collectPostsByWeekNumber(Post $post): void
    {
        $this->weekNumberData[$post->getWeekNumber()] = ($this->weekNumberData[$post->getWeekNumber()] ?? 0) + 1;
    }
}
