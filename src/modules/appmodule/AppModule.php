<?php

namespace modules\appmodule;

use craft\base\Element;
use craft\elements\Entry;
use craft\elements\User;
use craft\services\Elements;
use yii\base\Event;
use yii\base\InvalidConfigException;

class AppModule extends \yii\base\Module
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

    public function init(): void
    {
        parent::init();
        $this->registerEventListeners();
    }

    public function registerEventListeners(): void
    {
        Event::on(
            Entry::class,
            Entry::EVENT_INIT,
            function (Event $event) {
                $this->attachElementBehaviors($event->sender);
            }
        );

        // Explicitly attaching behaviors again here, as new entries won't yet
        // have a section defined when Entry::EVENT_INIT is fired, and thus the
        // behavior won't be attached. This allows us to listen for
        // Entry::EVENT_BEFORE_SAVE from within our behavior.
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

    private function attachElementBehaviors(Element $element): void
    {
        $behaviors = null;

        // Bail early if this element has no source (section, etc.)
        try {
            if ($element instanceof Entry) {
                $behaviors = self::SECTION_BEHAVIORS[$element->section->handle] ?? null;
            } elseif ($element instanceof User) {
                $behaviors = self::USER_GROUP_BEHAVIORS[$element->group->handle] ?? null;
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
