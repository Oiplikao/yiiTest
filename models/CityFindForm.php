<?php


namespace app\models;


use app\custom\cityFinder\BaseCityFinder;
use app\custom\cityFinder\CityData;
use app\custom\cityFinder\CityFinderStub;
use yii\helpers\ArrayHelper;

class CityFindForm extends \yii\base\Model
{
    /** @var BaseCityFinder */
    static private $_cityFinder;

    //TODO move to container as singleton?
    static public function getCityFinder()
    {
        if(!self::$_cityFinder) {
            self::$_cityFinder = new CityFinderStub();
        }
        return self::$_cityFinder;
    }

    public function findByName(string $name) : array
    {
        $citiesData = self::getCityFinder()->findCitiesByName($name);
        $cities = City::find()
            ->where(['name' => ArrayHelper::getColumn($citiesData, 'name')])
            ->indexBy('name')
            ->all();
        foreach($citiesData as $cityData) {
            if(!isset($cities[$cityData->name])) {
                $cities[] = $this->insertToDB($cityData);
            }
        }
        //todo sort by name
        return $cities;
    }

    /**
     * @param string $ip
     * @return City|null
     */
    public function findByIP(string $ip)
    {
        $cityData = self::getCityFinder()->findCityByIP($ip);
        if(!$cityData) {
            return null;
        }
        $city = City::findOne(['name' => $cityData->name]);
        if(!$city) {
            return $this->insertToDB($cityData);
        }
        return $city;
    }

    public function findByID(int $id)
    {
        return City::findOne(['id' => $id]);
    }

    protected function insertToDB(CityData $cityData)
    {
        $city = new City([
            'name' => $cityData->name
        ]);
        if(!$city->save()) {
            return null;
        }
        return $city;
    }
}