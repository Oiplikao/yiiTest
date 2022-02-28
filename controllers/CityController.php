<?php


namespace app\controllers;


use app\models\City;
use app\models\CityFindForm;
use Yii;

class CityController extends \yii\web\Controller
{
    public function actionCitySearch(string $name)
    {
        $form = new CityFindForm();
        $cities = $form->findByName($name);
        return $this->renderAjax('city-search-list', [
            'cities' => $cities
        ]);
    }

    public function actionChoice()
    {
        $form = new CityFindForm();
        if($this->request->isPost)
        {
            $cityID = $this->request->post('cityID');
            if($cityID) {
                $city = $form->findByID((int)$this->request->post('cityID'));
                if($city) {
                    Yii::$app->session->set('cityID', $city->id);
                    return $this->redirect(['review/index-by-city']);
                }
            }
        }
        $models = City::find()->all();
        $cityFromIP = $form->findByIP($this->request->getUserIP());
        return $this->render('choice', [
            'form' => $form,
            'models' => $models,
            'cityFromIP' => $cityFromIP
        ]);
    }
}