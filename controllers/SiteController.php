<?php

namespace app\controllers;

use app\custom\cityFinder\CityFinderStub;
use app\models\City;
use app\models\CityChoiceForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
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

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionCityChoice()
    {
        $form = new CityChoiceForm();
        if($this->request->isPost)
        {
            $cityFindType = $this->request->post('type');
            $findScenarios = [
                'name' => $form::SCENARIO_FIND_BY_NAME,
                'id' => $form::SCENARIO_FIND_BY_ID
            ];
            $scenario = $findScenarios[$cityFindType];
            if($scenario)
            {
                $form->scenario = $scenario;
                if($form->load(Yii::$app->request->post(), '') && $form->validate()) {
                    Yii::$app->session->set('cityID', $form->cityID);
                    return $this->redirect(['review/index-by-city']);
                }
            }
        }
        $models = City::find()->all();
        $cityFinder = new CityFinderStub();
        $cityFromIP = $cityFinder->findCityByIP($this->request->getUserIP());
        return $this->render('citychoice', [
            'form' => $form,
            'models' => $models,
            'cityFromIP' => $cityFromIP
        ]);
    }

    public function actionTest()
    {
        echo xdebug_info();
    }
}
