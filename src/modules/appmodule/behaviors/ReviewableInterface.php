<?php
namespace modules\appmodule\behaviors;

use craft\elements\db\EntryQuery;

interface ReviewableInterface
{
    public function getReviews(): EntryQuery;
    public function getReviewCount(): int;
    public function getReviewRatingAvg(): float;
}
