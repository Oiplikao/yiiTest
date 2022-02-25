<?php


namespace app\models;


use Yii;
use yii\web\UploadedFile;

class ReviewCreateForm extends \yii\base\Model
{
    public $title;
    public $text;
    public $rating;
    public $cityIDs;
    /** @var UploadedFile */
    public $img;

    public function rules()
    {
        return [
            [['title', 'text'], 'trim'],
            [['title', 'text', 'rating', 'cityIDs'], 'required'],
            ['title', 'string', 'max' => 100],
            ['text', 'string', 'max' => 255],
            ['rating', 'filter', 'filter' => 'intval'],
            ['rating', 'integer', 'min' => 1, 'max' => 5],
            ['cityIDs', 'exist', 'targetClass' => City::class, 'targetAttribute' => 'id', 'allowArray' => true],
            ['img', 'image']
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

    public function create()
    {
        if(!$this->validate())
        {
            return false;
        }
        $review = new Review();
        if($this->img)
        {
            $imageID = Yii::$app->security->generateRandomString(8).".".$this->img->extension;
            $imagePath = "@uploadroot/".$imageID;
            if($this->img->saveAs($imagePath)) {
                $review->img = Yii::getAlias($imageID);
            }
        }
        $review->attributes = $this->attributes;
        $review->user_id = Yii::$app->user->getId();
        if($review->save())
        {
            $cities = City::findAll($this->cityIDs);
            foreach($cities as $city) {
                $review->link('cities', $city);
            }
        }
        return true;
    }
}