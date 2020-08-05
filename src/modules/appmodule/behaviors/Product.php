<?php

namespace modules\appmodule\behaviors;

use craft\elements\db\EntryQuery;
use yii\base\Behavior;

class Product extends Behavior implements ReviewableInterface
{
    use ReviewableTrait;

    /**
     * Example: auto-expire entires 3 months from post date
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            Entry::EVENT_BEFORE_SAVE => function (ModelEvent $event) {
                $entry = $event->sender;

                if (ElementHelper::isDraftOrRevision($entry)) {
                    return;
                }

                $entry->expiryDate = (clone $entry->postDate)->modify('+3 months');
            }
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

    /**
     * Example: clobbering field getters
     * If we merely called `$this->owner->isDiscontinued`, we would not get the
     * field value as expected, but in fact this method, due to Craft's
     * overloading via `__get`. Therefore, if we want to name our method this, we
     * need to make sure and use the `getFieldValue` method, or we'd find
     * our selves in a recursive loop.
     *
     * @return boolean
     */
    public function getIsDiscontinued(): bool
    {
        return $this->owner->getFieldValue('isDiscontinued') || !$this->owner->manufacturer->exists();
    }
}
