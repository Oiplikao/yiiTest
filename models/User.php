<?php

namespace app\models;

use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class User
 * @property int $id
 * @property string $fio
 * @property string $email
 * @property string $phone
 * @property \DateTime $date_create
 * @property string $password
 */
class User extends ActiveRecord implements \yii\web\IdentityInterface
{

    public static function tableName()
    {
        return 'users';
    }

    public function rules()
    {
        return [
            [['name', 'email', 'phone', 'password'], 'required'],
            [['date_create'], 'safe'],
            [['password'], 'validatePassword'],
            [['name', 'email', 'phone', 'password'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fio' => 'Fio',
            'email' => 'Email',
            'phone' => 'Phone',
            'password' => 'Password',
            'date_create' => 'Date Create',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException();
    }

    /**
     * @param $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        $user = static::findOne(['email' => $email]);
        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === $password; //Yii::$app->security->validatePassword($password, $this->password);
    }

    public function setPasswordSecure($password)
    {
        $this->password = \Yii::$app->security->generatePasswordHash($password);
    }

    public function getAuthKey()
    {
        //not used but required by interface
    }

    public function validateAuthKey($authKey)
    {
        //not used but required by interface
    }
}
