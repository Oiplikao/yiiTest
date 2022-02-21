<?php

namespace app\custom\cityFinder;
/**
 * Класс для поиска города вне ДБ, по имени или по IP
 */
abstract class BaseCityFinder
{
    abstract function findCityByIP($ip) : CityData;

    /**
     * @param $name
     * @return CityData[]
     */
    abstract function findCitiesByName($name, $limit = 5) : array;
}