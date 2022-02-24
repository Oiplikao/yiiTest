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

    public $serverEmailCode;
    public $emailCode;

    public $user;

    const SCENARIO_INFO = 'info';
    const SCENARIO_EMAIL_VERIFY = 'email_verify';

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_INFO] = [
            'fio', 'email', 'phone', 'password', 'passwordRepeat', 'captchaCode'
        ];
        $scenarios[self::SCENARIO_EMAIL_VERIFY] = [
            'fio', 'email', 'phone', 'password', 'emailCode', '!serverEmailCode'
        ];
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [

            ['serverEmailCode', 'required'],
            ['emailCode', 'required'],
            ['emailCode', 'compare', 'compareAttribute' => 'serverEmailCode'],

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
            //todo phone format verification

            ['captchaCode', 'required'],
            ['captchaCode', 'captcha'],
        ];
    }

    public function signupStep1()
    {
        if(!$this->validate())
        {
            return null;
        }
        $this->serverEmailCode = Yii::$app->security->generateRandomString(5);
        if($this->sendEmail())
        {
            return true;
        }
        return null;
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     */
    public function signupStep2()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->fio = $this->fio;
        $user->email = $this->email;
        $user->phone = $this->phone;
        $user->setPasswordSecure($this->password);

        $this->user = $user;

        return $user->save();
    }

    /**
     * Sends confirmation email to user
     * @param string $fio
     * @param string $code
     * @return bool whether the email was sent
     */
    protected function sendEmail()
    {
        return Yii::$app
            ->mailer
            ->compose(
                'emailVerify-html',
                ['code' => $this->serverEmailCode, 'fio' => $this->fio]
            )
            ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }
}