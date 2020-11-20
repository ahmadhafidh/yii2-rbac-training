<?php

namespace app\modules\admin\controllers;

use yii\rbac\Item;
use app\components\rbac\ItemController;

class PermissionController extends ItemController
{
    public function labels()
    {
        return[
            'Item' => 'Permission',
            'Items' => 'Permissions',
        ];
    }

    public function getType()
    {
        return Item::TYPE_PERMISSION;
    }
}
