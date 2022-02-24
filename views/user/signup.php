<?php

/** @var \app\models\SignupForm $model */
/** @var bool $firstStep */

use yii\bootstrap4\ActiveForm;

$form = ActiveForm::begin([
    'id' => 'signup-form',
    'options' => ['class' => 'form']
]);

?>
<div class="user-signup">
<?php if($firstStep) {
    echo $form->field($model, 'fio')
        . $form->field($model, 'email')
        . $form->field($model, 'phone')
        . $form->field($model, 'password')->passwordInput()
        . $form->field($model, 'passwordRepeat')->passwordInput()
        . $form->field($model, 'captchaCode')->widget(\yii\captcha\Captcha::class, [
            'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
        ]);
 } else {
    foreach(
        ['fio',
        'email',
        'phone',
        'password'
        ] as $attribute) {
        echo \yii\bootstrap4\Html::activeHiddenInput($model, $attribute);
    }

    echo $form->field($model, 'emailCode'/*, ['enableAjaxValidation' => true] //TODO ajax*/);
}
echo \yii\bootstrap4\Html::submitButton('Отправить');
$form::end();
?>

</div>