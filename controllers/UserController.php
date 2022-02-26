<?php


namespace app\controllers;


use app\models\LoginForm;
use app\models\SignupForm;
use Yii;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class UserController extends \yii\web\Controller
{
    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['city/index-by-city']);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionSignup()
    {
        $model = new SignupForm();
        if($this->request->isPost) {
            if(!ArrayHelper::getValue($this->request->post(), Html::getInputName($model, 'emailCode'))) {
                $model->scenario = $model::SCENARIO_INFO;
                if($model->load($this->request->post()))
                {
                    //ajax validation step 1
                    if($this->request->isAjax)
                    {
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        return ActiveForm::validate($model, ['email', 'phone']);
                    }
                    //check info and send email code
                    else if($model->signupStep1())
                    {
                        Yii::$app->session->set('emailCode', $model->serverEmailCode);
                        //separate step to verify email code
                        return $this->render('signup', [
                            'model' => $model,
                            'firstStep' => false
                        ]);
                    }

                }
            } else {
                //ajax validation of email and phone
                if($this->request->isAjax)
                {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ActiveForm::validate($model, ['email', 'phone']);
                }
                $model->scenario = $model::SCENARIO_EMAIL_VERIFY;
                $model->serverEmailCode = Yii::$app->session->get('emailCode');
                if($model->load($this->request->post()))
                {
                    //ajax validation of email code
                    if($this->request->isAjax) {
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        return ActiveForm::validate($model, ['emailCode']);
                    }
                    //full verification - info and email code
                    else if($model->signupStep2()) {
                        Yii::$app->user->login($model->user);
                        return $this->redirect(['review/index-by-city']);
                    }
                }
            }
        }
        return $this->render('signup', [
            'model' => $model,
            'firstStep' => true
        ]);
    }
}