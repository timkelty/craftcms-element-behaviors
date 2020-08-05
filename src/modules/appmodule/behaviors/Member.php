<?php

namespace modules\appmodule\behaviors;

use yii\base\Behavior;

class Member extends Behavior
{
    public function events()
    {
        return [
            User::EVENT_DEFINE_RULES => function (DefineRulesEvent $event) {
                $event->rules[] = ['username', 'string', 'length' => [3, 20]];
            },
      ];
    }

    public function getProfileUri()
    {
        return 'members/' . $this->owner->id;
    }
}
