<?php

namespace app\modules\admin\controllers;

use yii\rbac\Item;
use app\components\rbac\ItemController;

class RoleController extends ItemController
{
    public function labels()
    {
        return[
            'Item' => 'Role',
            'Items' => 'Roles',
        ];
    }

    public function getType()
    {
        return Item::TYPE_ROLE;
    }
}
