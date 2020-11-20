<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;

class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const CLIENT = 'client';
    const ADMIN = 'admin';

    public $password_new;
    public $password_confirm;

    public static function tableName()
    {
        return 'user';
    }

    public function rules()
    {
        return [
            [['username', 'email', 'role'], 'required'],
            [['role'], 'string'],
            [['username', 'verification_token', 'password_reset_token'], 'string', 'max' => 256],
            [['username'], 
                'match',
                'pattern' => '/^\\S*$/',
                'message' => Yii::t("app","Cannot use whitespace")
            ],
            [['created_date', 'updated_date'], 'safe'],
            ['username', 'unique', 'targetClass' => '\app\models\Users', 'message' => Yii::t("app","Username telah digunakan")],
            ['email', 'unique', 'targetClass' => '\app\models\Users', 'message' => Yii::t("app","Email telah digunakan")],
            ['email', 'email', 'message' => Yii::t("app","Email tidak sesuai format")],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'email' => 'Email',
            'password' => 'Password',
            'auth_key' => 'Auth Key',
            'role' => 'Role',
            'status' => 'Status',
            'verification_token' => 'Verification Token',
            'password_reset_token' => 'Password Reset Token',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'created_by_name' => 'Created By Name',
            'updated_date' => 'Updated Date',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::find()->where(['username' => $username, 'status' => self::STATUS_ACTIVE])->orWhere(['email' => $username, 'status' => self::STATUS_ACTIVE])->one();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
}
