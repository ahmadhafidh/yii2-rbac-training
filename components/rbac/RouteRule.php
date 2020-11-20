<?php

namespace app\components\rbac;

use Yii;
use yii\rbac\Rule;

class RouteRule extends Rule
{
    const RULE_NAME = 'route_rule';

    public $name = self::RULE_NAME;

    public function execute($user, $item, $params)
    {
        $routeParams = isset($item->data['params']) ? $item->data['params'] : [];
        foreach ($routeParams as $key => $value) {
            if (!array_key_exists($key, $params) || $params[$key] != $value) {
                return false;
            }
        }
        return true;
    }
}
