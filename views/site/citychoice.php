<?php

/** @var yii\web\View $this */
/** @var \app\models\CityChoiceForm $model */

use yii\bootstrap4\Html;

$this->title = 'Выбор города';
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent">

        <?php if($model->cityFromIp) { ?>

        <p class="lead">Это ваш город?</p>

        <?=
            Html::beginForm(['/site/city-choice'], 'post')
                . Html::hiddenInput('cityName', $model->cityFromIp->name)
            . Html::submitButton(
                $model->getCityFromIp()->name,
                ['class' => 'btn btn-lg btn-success']
            )
            . Html::endForm();
        ?>

        <?php } ?>
    </div>
</div>