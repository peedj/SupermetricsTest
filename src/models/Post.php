<?php

namespace ApiConnect\models;

class Post
{
    public string $id;
    public string $from_name;
    public string $from_id;
    public string $message;
    public string $type;
    public string $created_time;
    private ?\DateTime $time = null;

    /**
     * @param array $arrayData
     * @return self
     */
    public static function createFromArray(array $arrayData): self
    {
        $instance = new self();
        foreach ($arrayData as $key => $value) {
            if (property_exists($instance, $key)) {
                $instance->{$key} = $value;
            }
        }

        return $instance;
    }

    public function getCharLength(): int
    {
        return strlen($this->message);
    }

    public function getMonth(): string
    {
        return $this->getTime()->format("Y-m");
    }

    public function getWeekNumber(): int
    {
        return $this->getTime()->format("W");
    }

    private function getTime(): \DateTime
    {
        return $this->time ?? ($this->time = new \DateTime($this->created_time));
    }
}