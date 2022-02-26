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
                'only' => ['create', 'index-by-user'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ]
                ],
                'denyCallback' => function() {
                    Url::remember();
                    $this->redirect(['user/login']);
                }
            ]
        ];
    }

    public function actionIndexByCity()
    {
        //todo cityID check can be moved to AccessControl
        $cityID = \Yii::$app->session->get('cityID');
        if(!$cityID || $cityID == City::getAllCityID())
        {
            //session ran out or direct access without chosen city
            //also dont show all city reviews
            $this->redirect(["city/choice"]);
        }
        $city = City::findOne($cityID);
        if(!$city) {
            //incorrect ID todo log this
            return $this->redirect(["city/choice"]);
        }
        $reviewsQuery = $city->getReviewsIncludingShared()->with('user');
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
            'provider' => $provider,
            'city' => $city,
            'isGuest' => \Yii::$app->user->isGuest,
            'showCity' => false
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
            return $this->redirect(['city/choice']);
        }
        $city = City::findOne($cityID);
        if(!$city)
        {
            return $this->redirect(['city/choice']);
        }
        return $this->render('create', [
            'model' => $model,
            'city' => $city,
            'citySearchUrl' => Url::to(['city/city-search']),
            'allCityID' => City::getAllCityID()
        ]);
    }
}