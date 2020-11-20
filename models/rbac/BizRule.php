<?php

namespace app\models\rbac;

use Yii;
use yii\rbac\Rule;
use app\components\rbac\Configs;

class BizRule extends \yii\base\Model
{
    public $name;

    public $createdAt;

    public $updatedAt;

    public $className;

    private $_item;

    public function __construct($item, $config = [])
    {
        $this->_item = $item;
        if ($item !== null) {
            $this->name = $item->name;
            $this->className = get_class($item);
        }
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['name', 'className'], 'required'],
            [['className'], 'string'],
            [['className'], 'classExists']
        ];
    }

    public function classExists()
    {
        if (!class_exists($this->className)) {
            $message = Yii::t('app', "Unknown class '{class}'", ['class' => $this->className]);
            $this->addError('className', $message);
            return;
        }
        if (!is_subclass_of($this->className, Rule::className())) {
            $message = Yii::t('app', "'{class}' must extend from 'yii\Rbac\Rule' or its child class", [
                    'class' => $this->className]);
            $this->addError('className', $message);
        }
    }

    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', 'Name'),
            'className' => Yii::t('app', 'Class Name'),
        ];
    }

    public function getIsNewRecord()
    {
        return $this->_item === null;
    }

    public static function find($id)
    {
        $item = Configs::authManager()->getRule($id);
        if ($item !== null) {
            return new static($item);
        }

        return null;
    }

    public function save()
    {
        if ($this->validate()) {
            $manager = Configs::authManager();
            $class = $this->className;
            if ($this->_item === null) {
                $this->_item = new $class();
                $isNew = true;
            } else {
                $isNew = false;
                $oldName = $this->_item->name;
            }
            $this->_item->name = $this->name;

            if ($isNew) {
                $manager->add($this->_item);
            } else {
                $manager->update($oldName, $this->_item);
            }

            return true;
        } else {
            return false;
        }
    }

    public function getItem()
    {
        return $this->_item;
    }
}
