<?php
namespace modules\appmodule\behaviors;

trait ReviewableTrait
{
    public function getReviewCount(): int
    {
        return $this->owner->getReviews()->count();
    }

    public function getReviewRatingAvg(): float
    {
        $ratings = $this->owner->getReviews()->all();

        return round(array_sum($ratings) / count($ratings), 1);
    }
}
