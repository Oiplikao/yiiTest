<?php


namespace app\models;


use Yii;

/**
 * Форма регистрации.
 * Основа взята из Yii 2 Advanced Project Template
 * @url https://github.com/yiisoft/yii2-app-advanced/blob/master/frontend/models/SignupForm.php
 * @package app\models
 */
class SignupForm extends \yii\base\Model
{
    public $fio;
    public $email;
    public $phone;
    public $password;
    public $passwordRepeat;
    public $captchaCode;

    public $user;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['fio', 'trim'],
            ['fio', 'required'],
            ['fio', 'string', 'min' => 2, 'max' => 50],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 50],
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'Этот email уже используется'],

            ['password', 'required'],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength'], 'max' => 50],

            ['passwordRepeat', 'required'],
            ['passwordRepeat', 'compare', 'compareAttribute' => 'password'],

            ['phone', 'trim'],
            ['phone', 'required'],
            ['phone', 'unique', 'targetClass' => User::class, 'message' => 'Этот номер уже используется'],
            //todo phone format verification

            ['captchaCode', 'required'],
            ['captchaCode', 'captcha'],
        ];
    }

    public function signUp()
    {
        if(!$this->validate()) {
            return false;
        }

        return ($user = $this->createUser()) && $this->sendEmail($user);
    }

    protected function createUser()
    {
        $user = new User();
        $user->fio = $this->fio;
        $user->email = $this->email;
        $user->phone = $this->phone;
        $user->setPasswordSecure($this->password);
        return $user->save() ? $user : null;
    }

    public function verify(User $user, string $code)
    {
        if($code !== $this->getEmailHash($user))
        {
            return false;
        }
        $user->email_verified = true;
        return $user->save();
    }

    /**
     * Sends confirmation email to user
     * @param string $fio
     * @param string $code
     * @return bool whether the email was sent
     */
    protected function sendEmail(User $user)
    {
        return Yii::$app
            ->mailer
            ->compose(
                'emailVerify-html',
                ['url' => \yii\helpers\Url::to(['user/verify', 'hash' => $this->getEmailHash($user), 'id' => $user->id], true), 'fio' => $this->fio]
            )
            ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }

    protected function getCryptoKey()
    {
        //fixme testdata
        return '123123123123';
    }

    protected function getEmailHash(User $user)
    {
        return hash_hmac('sha1', $user->email, $this->getCryptoKey());
    }
}