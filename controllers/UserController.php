<?php


namespace app\controllers;


use app\models\LoginForm;
use app\models\SignupForm;
use app\models\User;
use Yii;
use yii\bootstrap4\ActiveForm;
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
        if($this->request->isPost && $model->load($this->request->post())) {
            if($this->request->isAjax)
            {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model, ['email', 'phone']);
            }
            if($model->signup()) {
                Yii::$app->session->setFlash('info', 'На ваш e-mail было отправлено письмо с ссылкой на подтверждение');
                return $this->redirect(['user/login']);
            }
        }
        return $this->render('signup', [
            'model' => $model,
            'firstStep' => true
        ]);
    }

    public function actionVerify(string $hash, int $id)
    {
        $model = new SignupForm();
        $user = User::findOne($id);
        if($model->verify($user, $hash)) {
            Yii::$app->session->setFlash('success', 'E-mail подтвержден.');
            return $this->redirect(['user/login']);
        }
        return $this->goHome();
    }
}