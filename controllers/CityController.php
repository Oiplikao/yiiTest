<?php


namespace app\controllers;


use app\custom\cityFinder\CityFinderStub;
use app\models\City;
use app\models\CityChoiceForm;
use Yii;
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

    public function actionChoice()
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
        return $this->render('choice', [
            'form' => $form,
            'models' => $models,
            'cityFromIP' => $cityFromIP
        ]);
    }
}