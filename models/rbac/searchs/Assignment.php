<?php

namespace app\models\rbac\searchs;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;

class Assignment extends Model
{
    public $id;
    public $username;
    public $email;

    public function rules()
    {
        return [
            [['id', 'username','email'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'Username'),
            'name' => Yii::t('app', 'Name'),
        ];
    }
    
    public function search($params, $class, $usernameField)
    {
        $query = User::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', $usernameField, $this->username]);

        return $dataProvider;
    }
}
