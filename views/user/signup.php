<?php

/** @var \app\models\SignupForm $model */
/** @var bool $firstStep */

use yii\bootstrap4\ActiveForm;

$form = ActiveForm::begin([
    'id' => 'signup-form',
    'options' => ['class' => 'form'],
    'validateOnSubmit' => false
]);

?>
<div class="user-signup">
<?php
echo $form->field($model, 'fio')
    . $form->field($model, 'email', ['enableAjaxValidation' => true])
    . $form->field($model, 'phone', ['enableAjaxValidation' => true])
    . $form->field($model, 'password')->passwordInput()
    . $form->field($model, 'passwordRepeat')->passwordInput()
    . $form->field($model, 'captchaCode')->widget(\yii\captcha\Captcha::class, [
        'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
    ])
    . \yii\bootstrap4\Html::submitButton('Отправить');
$form::end();
?>

</div>