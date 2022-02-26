<?php

/** @var yii\web\View $this */
/** @var \yii\data\ActiveDataProvider $provider */
/** @var string $title */
/** @var bool $isGuest */
/** @var bool $showCity */

use yii\bootstrap4\Html;
use yii\bootstrap4\Modal;

const CITY_CELL_LIST_LIMIT = 3;

$this->title = "Обзоры - {$title}";
?>



<div class="review-index">
    <?php

    $renderModalFunc = function($model, $aLabel, $modalTitle, $modalContentFunc)
    {
        static $i = 1;
        /** @var \app\models\Review $model */
        $user = $model->user;
        $modalID = "user-modal-{$i}";
        $i++;
        ob_start();
        echo Html::a(Html::encode($aLabel), '#', [
            'data-toggle' => 'modal',
            'data-target' => '#'.$modalID
        ]);
        Modal::begin([
            'centerVertical' => true,
            'title' => $modalTitle,
            'options' => [
                'id' => $modalID
            ]
        ]);
        $modalContentFunc($model, $user);
        Modal::end();
        return ob_get_clean();
    };

    echo \yii\grid\GridView::widget([
        'dataProvider' => $provider,
        'columns' => array_merge([
            'title',
            'text',
            'rating',
            [
                'attribute' => 'imageLink',
                'format' => 'image'
            ],
            [
                'format' => 'raw',
                'header' => \app\models\Review::instance()->getAttributeLabel('user'),
                'value' => function($model) use ($isGuest, $renderModalFunc) {
                    return $renderModalFunc($model, $model->user->fio, $model->user->fio, function($model, $user) use ($isGuest) {
                        if(!$isGuest)
                        {
                            echo \yii\widgets\DetailView::widget([
                                'model' => $user,
                                'attributes' => [
                                    'fio',
                                    'email',
                                    'phone'
                                ]
                            ]);
                            echo Html::a('Другие отзывы пользователя', \yii\helpers\Url::to(["/review/index-by-user", 'userID' => $user->id]));
                        } else {
                            echo Html::tag('p', "Авторизуйтесь чтобы увидеть контактные данные и другие обзоры пользователя.");
                        }
                    });
                }
            ]
        ],
        $showCity ? [ [
            'format' => 'raw',
            'header' => \app\models\Review::instance()->getAttributeLabel('cities'),
            'value' => function($model) use ($renderModalFunc) {
                /** @var \app\models\Review $model */
                $cities = $model->cities;
                $cityNames = array_map(fn($city) => $city->name, $cities);
                $cellCityNames = array_slice($cityNames, 0, CITY_CELL_LIST_LIMIT);
                if(count($cellCityNames) == count($cityNames)) {
                    return Html::tag('p', implode(', ', $cellCityNames));
                }
                $cellCityNames = array_merge($cellCityNames, ['...']);
                $cellCityNamesString = implode(', ', $cellCityNames);
                return $renderModalFunc($model, $cellCityNamesString, '', function($model, $user) use ($cityNames) {
                    echo Html::beginTag('ul');
                    foreach($cityNames as $cityName)
                    {
                        echo Html::tag('li', $cityName);
                    }
                    echo Html::endTag('ul');
                });
            }
        ] ] : [])
    ])

    ?>
</div>
