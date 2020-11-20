<?php

namespace app\modules\admin;

use Yii;
use yii\helpers\Inflector;

class Module extends \yii\base\Module
{
    public $defaultRoute = 'assignment';
    
    public $navbar;

    public $mainLayout = '@app/modules/admin/views/layouts/main.php';

    private $_menus = [];
    
    private $_coreItems = [
        'user' => 'Users',
        'assignment' => 'Assignments',
        'role' => 'Roles',
        'permission' => 'Permissions',
        'route' => 'Routes',
        'rule' => 'Rules',
        'menu' => 'Menus',
    ];
    
    private $_normalizeMenus;

    public $defaultUrl;

    public $defaultUrlLabel;

    public function init()
    {
        parent::init();

        if ($this->navbar === null && Yii::$app instanceof \yii\web\Application) {
            $this->navbar = [
                ['label' => Yii::t('app', 'Help'), 'url' => ['default/index']],
                ['label' => Yii::t('app', 'Application'), 'url' => Yii::$app->homeUrl],
            ];
        }
        if (class_exists('yii\jui\JuiAsset')) {
            Yii::$container->set('mdm\admin\AutocompleteAsset', 'yii\jui\JuiAsset');
        }
    }

    public function getMenus()
    {
        if ($this->_normalizeMenus === null) {
            $mid = '/' . $this->getUniqueId() . '/';
            
            $this->_normalizeMenus = [];

            $config = \app\components\rbac\Configs::instance();
            $conditions = [
                'user' => $config->db && $config->db->schema->getTableSchema($config->userTable),
                'assignment' => ($userClass = Yii::$app->getUser()->identityClass) && is_subclass_of($userClass, 'yii\db\BaseActiveRecord'),
                'menu' => $config->db && $config->db->schema->getTableSchema($config->menuTable),
            ];
            foreach ($this->_coreItems as $id => $lable) {
                if (!isset($conditions[$id]) || $conditions[$id]) {
                    $this->_normalizeMenus[$id] = ['label' => Yii::t('app', $lable), 'url' => [$mid . $id]];
                }
            }
            foreach (array_keys($this->controllerMap) as $id) {
                $this->_normalizeMenus[$id] = ['label' => Yii::t('app', Inflector::humanize($id)), 'url' => [$mid . $id]];
            }

            foreach ($this->_menus as $id => $value) {
                if (empty($value)) {
                    unset($this->_normalizeMenus[$id]);
                    continue;
                }
                if (is_string($value)) {
                    $value = ['label' => $value];
                }
                $this->_normalizeMenus[$id] = isset($this->_normalizeMenus[$id]) ? array_merge($this->_normalizeMenus[$id], $value)
                : $value;
                if (!isset($this->_normalizeMenus[$id]['url'])) {
                    $this->_normalizeMenus[$id]['url'] = [$mid . $id];
                }
            }
        }
        return $this->_normalizeMenus;
    }

    public function setMenus($menus)
    {
        $this->_menus = array_merge($this->_menus, $menus);
        $this->_normalizeMenus = null;
    }

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $view = $action->controller->getView();

            // $view->params['breadcrumbs'][] = [
            //     'label' => ($this->defaultUrlLabel ?: Yii::t('app', 'Admin')),
            //     'url' => ['/' . ($this->defaultUrl ?: $this->uniqueId)],
            // ];
            return true;
        }
        return false;
    }
}