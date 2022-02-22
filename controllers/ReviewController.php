<?php


namespace app\controllers;


use app\models\City;
use app\models\Review;
use yii\data\ActiveDataProvider;

class ReviewController extends \yii\web\Controller
{
    const VIEWTYPE_CITY = 'city';
    const VIEWTYPE_ALL_CITY = 'all_city';
    const VIEWTYPE_USER = 'user';

    public function actionIndexByCity()
    {
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
            $reviewsQuery = $city->getReviews();
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
            'city' => $city
        ]);
    }
}