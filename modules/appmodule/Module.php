<?php

namespace modules\appmodule;

use craft\elements\Entry;
use craft\services\Elements;
use yii\base\Event;
use yii\base\InvalidConfigException;

class Module extends \yii\base\Module
{
    const SECTION_BEHAVIORS = [
        'products' => [
            \modules\appmodule\behaviors\Product::class,
        ],
    ];

    const USER_GROUP_BEHAVIORS = [
        'members' => [
            \modules\appmodule\behaviors\Member::class,
        ],
    ];

    public function init()
    {
        parent::init();
        $this->registerEventListeners();
    }

    public function registerEventListeners()
    {
        Event::on(
            Entry::class,
            Entry::EVENT_INIT,
            function (Event $event) {
                $this->attachElementBehaviors($event->sender);
            }
        );

        // Explicitly attaching behaviors here, as prior to this (EVENT_INIT),
        // the sectionId will not have been set, and thus the behavior not attached.
        // This allows us to listen for Entry::EVENT_BEFORE_SAVE from within our behaviors.
        Event::on(
            Elements::class,
            Elements::EVENT_BEFORE_SAVE_ELEMENT,
            function (Event $event) {
                if ($event->isNew) {
                    $this->attachElementBehaviors($event->element);
                }
            }
        );
    }

    private function attachElementBehaviors(Element $element)
    {
        $behaviors = null;

        // Bail early if this element has no source (section, etc.)
        try {
            if ($event->element instanceof Entry) {
                $behaviors = self::SECTION_BEHAVIORS[$entry->section->handle];
            } elseif ($event->element instanceof User) {
                $behaviors = self::USER_GROUP_BEHAVIORS[$entry->group->handle];
            }
        } catch (InvalidConfigException $e) {
            return;
        }

        if (!$behaviors) {
            return;
        }

        $element->attachBehaviors($behaviors);
    }
}
