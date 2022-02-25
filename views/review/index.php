<?php

/** @var yii\web\View $this */
/** @var \yii\data\ActiveDataProvider $provider */
/** @var \app\models\City $city */
/** @var bool $isGuest */

use yii\bootstrap4\Html;
use yii\bootstrap4\Modal;

$this->title = "Обзоры - {$city->name}";
?>



<div class="review-index">
    <?php

    echo \yii\grid\GridView::widget([
        'dataProvider' => $provider,
        'columns' => [
            'title',
            'text',
            'rating',
            [
                'attribute' => 'imageLink',
                'format' => 'image'
            ],
            [
                'format' => 'raw',
                'value' => function($model) use ($isGuest) {
                    /** @var \app\models\Review $model */
                    $user = $model->user;
                    $modalID = "user-modal-{$model->id}";
                    ob_start();
                    echo Html::a(Html::encode($user->fio), '#', [
                        'data-toggle' => 'modal',
                        'data-target' => '#'.$modalID
                    ]);
                    Modal::begin([
                        'centerVertical' => true,
                        'title' => $user->fio,
                        'options' => [
                            'id' => $modalID
                        ]
                    ]);
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
                        echo Html::a('Другие отзывы пользователя', \yii\helpers\Url::to(["/review/index-by-user/{$user->id}"]));
                    } else {
                        echo Html::tag('p', "Авторизуйтесь чтобы увидеть контактные данные и другие обзоры пользователя.");
                    }
                    Modal::end();
                    return ob_get_clean();
                }
            ]
        ]
    ])

    ?>
</div>
