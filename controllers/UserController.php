<?php


namespace app\controllers;


use app\models\SignupForm;
use Yii;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class UserController extends \yii\web\Controller
{
    public function actionSignup()
    {
        $model = new SignupForm();
        if($this->request->isPost) {
            if(!ArrayHelper::getValue($this->request->post(), $model->formName() . '.emailCode') ) {
                $model->scenario = $model::SCENARIO_INFO;
                if($model->load($this->request->post())
                    //check info and send email code
                    && $model->signupStep1())
                {
                    Yii::$app->session->set('emailCode', $model->serverEmailCode);
                    //separate step to verify email code
                    return $this->render('signup', [
                        'model' => $model,
                        'firstStep' => false
                    ]);
                }
            } else {
                $model->scenario = $model::SCENARIO_EMAIL_VERIFY;
                $model->serverEmailCode = Yii::$app->session->get('emailCode');
                if($model->load($this->request->post())
                    //full verification - info and email code
                    && $model->signupStep2())
                {
                    Yii::$app->user->login($model->user);
                    return $this->redirect(['review/index-by-city']);
                }/* else if($this->request->isAjax) //todo ajax
                {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    $result[Html::getInputId($model, 'emailCode')] = $model->getErrors('emailCode');
                    return $result;
                }*/
            }
        }
        return $this->render('signup', [
            'model' => $model,
            'firstStep' => true
        ]);
    }
}