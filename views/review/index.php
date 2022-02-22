<?php

/** @var yii\web\View $this */
/** @var \yii\data\ActiveDataProvider $provider */
/** @var \app\models\City $city title */

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
            ]
        ]
    ])

    ?>
</div>
