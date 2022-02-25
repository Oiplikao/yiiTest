<?php


namespace app\controllers;


use app\custom\cityFinder\CityFinderStub;
use app\models\City;
use yii\helpers\ArrayHelper;

class CityController extends \yii\web\Controller
{
    public function actionCitySearch(string $name)
    {
        $cityFinder = new CityFinderStub();
        $citiesData = $cityFinder->findCitiesByName($name);
        $cities = City::find()
            ->indexBy('name')
            ->where(['name' => ArrayHelper::getColumn($citiesData, 'name')])
            ->all();
        foreach($citiesData as $cityData)
        {
            if(isset($cities[$cityData->name])) {
                continue;
            }
            $city = new City();
            $city->name = $cityData->name;
            $city->save();
            $cities[] = $city;
        }
        return $this->renderAjax('city-search-list', [
            'cities' => $cities
        ]);
    }
}