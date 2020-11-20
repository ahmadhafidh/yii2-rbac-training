<?php

namespace app\models\rbac\form;

use Yii;
use mdm\admin\models\User;
use yii\base\Model;

class ChangePassword extends Model
{
    public $oldPassword;
    public $newPassword;
    public $retypePassword;

    public function rules()
    {
        return [
            [['oldPassword', 'newPassword', 'retypePassword'], 'required'],
            [['oldPassword'], 'validatePassword'],
            [['newPassword'], 'string', 'min' => 8],
            [['newPassword'], 'match', 'pattern' => '/^.*(?=.*\d)(?=.*[\W])(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).*$/', 
            'message' => Yii::t("app","Password must fullfill following criteria : One Uppercase, One Symbol and One Number")],
            [['retypePassword'], 'match', 'pattern' => '/^.*(?=.*\d)(?=.*[\W])(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).*$/', 
            'message' => Yii::t("app","Retype Password must fullfill following criteria : One Uppercase, One Symbol and One Number")],
            [['retypePassword'], 'compare', 'compareAttribute' => 'newPassword'],
        ];
    }

    public function validatePassword()
    {
        $user = Yii::$app->user->identity;
        if (!$user || !$user->validatePassword($this->oldPassword)) {
            $this->addError('oldPassword', Yii::t("app","Incorrect old password"));
        }
    }

    public function change()
    {
        if ($this->validate()) {
            $user = Yii::$app->user->identity;
            $user->setPassword($this->newPassword);
            $user->generateAuthKey();
            if ($user->save()) {
                return true;
            }
        }

        return false;
    }
}
