<?php

namespace modules\appmodule\behaviors;

use craft\elements\db\EntryQuery;
use yii\base\Behavior;

class Product extends Behavior implements ReviewableInterface
{
    use ReviewableTrait;

    public function rules()
    {
        return [
            Entry::EVENT_BEFORE_SAVE => 'autoExpireEntries'
        ];
    }

    public function getReviews(): EntryQuery
    {
        return Entry::find()
            ->section('reviews')
            ->relatedTo([
                'targetElement' => $this->owner,
                'field' => 'product',
            ]);
    }

    public function autoExpireEntries(ModelEvent $event)
    {
        $entry = $event->sender;

        if (ElementHelper::isDraftOrRevision($entry)) {
            return;
        }

        $entry->expiryDate = (clone $entry->postDate)->modify('+3 months');
    }
}
