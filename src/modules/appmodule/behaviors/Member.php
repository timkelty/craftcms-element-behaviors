<?php

namespace modules\appmodule\behaviors;

use yii\base\Behavior;

class Member extends Behavior
{
    public function events()
    {
        return [
            User::EVENT_DEFINE_RULES => function (DefineRulesEvent $event) {
                $event->rules[] = ['username', 'in', 'not' => true, 'range' => [
                    'system',
                    'admin',
                    'moderator',
                ]];
                $event->rules[] = ['username', 'string', 'length' => [3, 20]];
            },
      ];
    }
}
