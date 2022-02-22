<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cities".
 *
 * @property int $id
 * @property string $name
 * @property string $date_create
 *
 * @property Cities2review[] $cities2reviews
 * @property Review[] $reviews
 */
class City extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cities';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['date_create'], 'safe'],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'date_create' => 'Date Create',
        ];
    }

    /**
     * Gets query for [[Reviews]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReviews()
    {
        return $this->hasMany(Review::className(), ['id' => 'review_id'])->viaTable('cities2reviews', ['city_id' => 'id']);
    }

    public static function getAllCity()
    {
        $city = City::findOne(self::getAllCityID());
        if(!$city) {
            throw new \Exception('"All cities" row is missing in DB');
        }
        return $city;
    }

    public static function getAllCityID()
    {
        $allCityID = Yii::$app->params['allCityID'];
        if(!$allCityID)
        {
            throw new \LogicException('AllCityID missing in application configuration');
        }
        $allCityID = (int)$allCityID;
        if(!$allCityID)
        {
            throw new \LogicException('Failed to convert AllCityID application parameter to int');
        }
        return $allCityID;
    }
}
