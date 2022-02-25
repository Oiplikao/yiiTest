<?php


namespace app\controllers;


use app\models\City;
use app\models\Review;
use app\models\ReviewCreateForm;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\UploadedFile;

class ReviewController extends \yii\web\Controller
{
    const VIEWTYPE_CITY = 'city';
    const VIEWTYPE_ALL_CITY = 'all_city';
    const VIEWTYPE_USER = 'user';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['@']
                    ]
                ],
                'denyCallback' => function() {
                    Url::remember();
                    $this->redirect(['site/login']);
                }
            ]
        ];
    }

    public function actionIndexByCity()
    {
        //todo cityID check can be moved to AccessControl
        $cityID = \Yii::$app->session->get('cityID');
        if(!$cityID)
        {
            //session ran out or direct access without chosen city
            $this->redirect(["site/city-choice"]);
        }
        $city = City::findOne($cityID);
        if(!$city) {
            //incorrect ID, should log
            return $this->redirect(["site/city-choice"]);
        }
        if($cityID == City::getAllCityID()) {
            $type = self::VIEWTYPE_ALL_CITY;
            $reviewsQuery = Review::find(); //all reviews
        } else {
            $type = self::VIEWTYPE_CITY;
            $reviewsQuery = $city->getReviews()->with('user');
        }
        $provider = new ActiveDataProvider([
            'query' => $reviewsQuery,
            'pagination' => [
                'pageSize' => 4
            ],
            'sort' => [
                'defaultOrder' => [
                    'date_create' => SORT_DESC,
                    'title' => SORT_ASC
                ]
            ]
        ]);
        return $this->render('index', [
            //TODO instead of types add booleans like "showUsername => true"
            'type' => $type,
            'provider' => $provider,
            'city' => $city,
            'isGuest' => \Yii::$app->user->isGuest
        ]);
    }

    public function actionCreate()
    {
        $model = new ReviewCreateForm();
        if($this->request->isPost) {
            if($model->load($this->request->post()))
            {
                $model->img = UploadedFile::getInstance($model, 'img');
                if($model->create()) {
                    \Yii::$app->session->setFlash('success', 'Review created successfully');
                    return $this->redirect(['review/index-by-city']);
                }
            }
        }
        $cityID = \Yii::$app->session->get('cityID');
        if(!$cityID)
        {
            return $this->redirect(['site/city-choice']);
        }
        $city = City::findOne($cityID);
        if(!$city)
        {
            return $this->redirect(['site/city-choice']);
        }
        return $this->render('create', [
            'model' => $model,
            'city' => $city,
            'citySearchUrl' => Url::to(['city/city-search']),
            'allCityID' => City::getAllCityID()
        ]);
    }
}