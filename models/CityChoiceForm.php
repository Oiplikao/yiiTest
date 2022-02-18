<?php


namespace app\models;


class CityChoiceForm extends \yii\base\Model
{
    public $cityName;

    public function rules()
    {
        return [
            [['cityId'], 'required'],
            [['cityId'], 'isRealCity']
        ];
    }

    public function getCityFromIp()
    {
        // todo: 3rd party maybe
        return new City([
            'id' => 2,
            'name' => 'Ижевск'
        ]);
    }

    public function isRealCity($cityName)
    {
        $city = City::findOne(['name' => $cityName]);
        if($city)
        {
            return true;
        }
        //$city = 3rd party
        //if $city return true
        return false;
    }

    public function save()
    {
        \Yii::$app->session->set('cityName', $this->cityName);
        return true;
    }
}