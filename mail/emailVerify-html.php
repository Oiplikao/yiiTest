<?php

use yii\bootstrap4\Html;

/** @var string $fio */
/** @var string $url */

?>
<div class="verify-email">
    <p>Hello <?= Html::encode($fio) ?>,</p>

    <a href="<?= $url ?>">Ссылка для подтверждения регистрации</a>
</div>