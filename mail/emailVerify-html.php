<?php

use yii\bootstrap4\Html;

/** @var string $fio */
/** @var string $code */

?>
<div class="verify-email">
    <p>Hello <?= Html::encode($fio) ?>,</p>

    <p><?= Html::encode($code) ?></p>
</div>