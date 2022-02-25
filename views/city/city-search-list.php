<?php

/** @var \yii\web\View $this */
/** @var \app\models\City[] $cities */

foreach($cities as $city) {
    echo yii\bootstrap4\Button::widget([
        'options' => [
            'id' => "city-search-item-{$city->id}",
            'class' => ['widget' => 'list-group-item list-group-item-action city-search-item'],
            'value' => $city->id
        ],
        'label' => $city->name
    ]);
}
