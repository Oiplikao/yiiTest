<?php


namespace app\models;


use app\custom\cityFinder\CityFinderStub;

class CityChoiceForm extends \yii\base\Model
{
    public $cityName;
    public $cityID;

    const SCENARIO_FIND_BY_ID = 'id';
    const SCENARIO_FIND_BY_NAME = 'name';

    public function rules()
    {
        return [
            [['cityID'], 'checkIfIDExistsInDB', 'on' => self::SCENARIO_FIND_BY_ID],
            [['cityName'], 'confirmAndAddByName', 'on' => self::SCENARIO_FIND_BY_NAME]
        ];
    }

    public function confirmAndAddByName($attributeName)
    {
        /** @var City|null $city */
        $name = $this->$attributeName;
        $city = City::find()->where(['name' => $name])->one();
        if(!$city) {
            $cityFinder = new CityFinderStub();
            $cityData = current($cityFinder->findCitiesByName($name, 1));
            if($cityData)
            {
                $city = new City([
                    'name' => $cityData->name
                ]);
                $city->save();
            }
        }
        if(!$city) {
            $this->addError($attributeName);
            return;
        }
        $this->cityID = $city->id;
    }

    public function checkIfIDExistsInDB($attributeName)
    {
        $city = City::findOne($this->$attributeName);
        if(!$city)
        {
            $this->addError($attributeName);
        }
    }
}