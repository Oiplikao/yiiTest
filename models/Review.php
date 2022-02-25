<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reviews".
 *
 * @property int $id
 * @property string $title
 * @property string $text
 * @property int $rating
 * @property resource|null $img
 * @property int $user_id
 * @property string $date_create
 *
 * @property City[] $cities
 * @property Cities2review[] $cities2reviews
 */
class Review extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reviews';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'text'], 'trim'],
            [['title', 'text', 'rating', 'user_id'], 'required'],
            ['user_id', 'integer'],
            ['title', 'string', 'max' => 100],
            ['text', 'string', 'max' => 255],
            ['rating', 'filter', 'filter' => 'intval'],
            ['rating', 'integer', 'min' => 1, 'max' => 5],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'text' => 'Text',
            'rating' => 'Rating',
            'img' => 'Img',
            'user_id' => 'User ID',
            'date_create' => 'Date Create',
        ];
    }

    /**
     * Gets query for [[Cities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCities()
    {
        return $this->hasMany(City::className(), ['id' => 'city_id'])->viaTable('cities2reviews', ['review_id' => 'id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['user_id' => 'id']);
    }

    public function getImageLink()
    {
        if(!$this->img)
        {
            return null;
        }
        return Yii::getAlias('@upload') . '/' . $this->img;
    }
}
