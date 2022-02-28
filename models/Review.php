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
 * @property City[] $cities
 * @property User $user
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
            'user' => 'User',
            'cities' => 'Cities',
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
        return $this->hasMany(City::class, ['id' => 'city_id'])->viaTable('cities2reviews', ['review_id' => 'id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getImageLink()
    {
        if(!$this->img)
        {
            return null;
        }
        return Yii::getAlias('@upload') . '/' . $this->img;
    }

    public function unlinkImage($save = true)
    {
        if(!$this->img) {
            return true;
        }
        $this->unlinkImageByID($this->img);
        $this->img = null;
        return $save ? $this->save() : true;
    }

    protected function unlinkImageByID($id)
    {
        $imagePath = Yii::getAlias("@uploadroot/{$id}");
        if(file_exists($imagePath) && !unlink($imagePath)) {
            //file exists but cant be deleted
            return false;
        }
        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        //deletes old image
        if(!$insert && isset($changedAttributes['img'])) {
            $oldImageID = $changedAttributes['img'];
            if($oldImageID) {
                $this->unlinkImageByID($oldImageID);
            }
        }

        parent::afterSave($insert, $changedAttributes);
    }
}
