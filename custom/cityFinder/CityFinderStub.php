<?php

namespace app\custom\cityFinder;

use Fuse\Fuse;

/**
 * Тестовый класс поиска города по IP и по имени
 */
class CityFinderStub extends BaseCityFinder
{
    private $_cityList = [
        'Ижевск',
        'Москва',
        'Санкт-Петербург',
        'Город1',
        'Город2',
        'Город3'
    ];

    function findCityByIP($ip) : CityData
    {
        //Всегда возвращает один и тот же город
        //\Yii::$app->request->getUserIP();
        return new CityData(['name' => $this->_cityList[0]]);
    }

    /**
     * @param $name
     * @param $limit
     * @return CityData[]
     */
    function findCitiesByName($name, $limit = 5) : array
    {
        $searcher = new Fuse($this->_cityList, [
            'findAllMatches' => true
        ]);
        $searchResult = $searcher->search($name, [
            'limit' => $limit
        ]);
        $items = array_column($searchResult, 'item');
        return array_map(function($item) { return new CityData(['name' => $item]); }, $items);
    }
}