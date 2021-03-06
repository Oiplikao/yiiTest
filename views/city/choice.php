<?php

/** @var yii\web\View $this */
/** @var \app\models\CityFindForm $form */
/** @var \app\models\City[] $models */
/** @var \app\models\City|null $cityFromIP */

use yii\bootstrap4\Html;

$this->title = 'Выбор город';
?>
<div class="site-index">

    <?php if($cityFromIP) { ?>

    <div class="text-center">

        <p class="lead">Это ваш город?</p>

        <?= Html::beginForm()
            . Html::submitButton(
                Html::encode($cityFromIP->name),
                [
                    'class' => 'btn btn-lg btn-success m-3',
                    'name' => 'cityID',
                    'value' => Html::encode($cityFromIP->id)
                ]
            )
            . Html::endForm()
        ?>

        <p class="lead">или</p>

    </div>

    <?php } ?>
    <?= Html::beginForm() ?>
    <p class="lead text-center">Выберите город</p>
    <div class="list-group">
        <?php

        echo Html::hiddenInput('type', 'id');

        foreach($models as $model) { ?>

         <?= yii\bootstrap4\Button::widget([
            'options' => [
                'class' => ['widget' => 'list-group-item list-group-item-action'],
                'name' => 'cityID',
                'value' => $model->id
            ],
            'label' => $model->name
        ]) ?>

        <?php } ?>

        <?= yii\bootstrap4\Button::widget([
            'options' => [
                'class' => ['widget' => 'list-group-item list-group-item-action'],
                'name' => 'cityID',
                'value' => '999'
            ],
            'label' => 'Фальшивый город - проверка валидации ID'
        ]) ?>
    </div>
    <?= Html::endForm() ?>
</div>