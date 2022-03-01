<?php


namespace app\controllers;


use app\models\City;
use app\models\Review;
use app\models\ReviewCreateForm;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\UploadedFile;

class ReviewController extends \yii\web\Controller
{

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
        $reviewsQuery = $city->getReviewsIncludingShared()->cache()->with('user');
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
            'title' => $city->name,
            'provider' => $provider,
            'isGuest' => \Yii::$app->user->isGuest,
            'showCity' => false
        ]);
    }

    public function actionIndexByUser(int $userID)
    {
        $user = User::findOne($userID);
        if(!$user) {
            return $this->redirect(['city/choice']);
        }
        $reviewsQuery = Review::find()->cache()->where(['user_id'=> $userID])->with('user', 'cities');
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
            'title' => $user->fio,
            'provider' => $provider,
            'isGuest' => \Yii::$app->user->isGuest,
            'showCity' => true
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

    public function actionEdit(int $id)
    {
        $model = Review::findOne($id);
        //todo implement auth manager
        if(!$model && $model->user_id !== \Yii::$app->user->getId()) {
            return $this->redirect(['review/index-by-city']);
        }
        $form = new ReviewCreateForm();
        if($this->request->isPost && $form->load($this->request->post())) {
            $form->img = UploadedFile::getInstance($form, 'img');
            if($form->edit($id)) {
                $this->redirect(['review/index-by-city']);
            }
        }
        $form->attributes = $model->attributes;
        $cities = $model->cities;
        return $this->render('create', [
            'model' => $form,
            'cities' => $cities,
            'citySearchUrl' => Url::to(['city/city-search']),
            'allCityID' => City::getAllCityID()
        ]);
    }
}