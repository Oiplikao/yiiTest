<?php
/** @var \yii\web\View $this */
/** @var \app\models\Review $model */
/** @var \app\models\City $city */
/** @var string $citySearchUrl */
/** @var int $allCityID */

?>

<div class="review-create">

    <?php $form = \yii\bootstrap4\ActiveForm::begin([
        'id' => 'review-create-form'
    ]);
    echo $form->field($model, 'title')
      . $form->field($model, 'text')->textarea()
      . $form->field($model, 'rating')->dropDownList([
          1 => 1,
          2 => 2,
          3 => 3,
          4 => 4,
          5 => 5])
      . $form->field($model, 'img')->fileInput()
    ;
    ?>

    <div class="container p-3">
        <div class="row">
            <?= \yii\bootstrap4\Html::textInput('citySearch', '', [
                    'id' => 'city-search-input'
            ]); ?>
        </div>
        <div class="list-group" id="city-search-list">
            <?php

            $this->registerJs(<<<JS
                var allCityID = {$allCityID};

                function appendCity(cityID, cityName) {
                    if($('#city-item-'+cityID).length > 0)
                    {
                        return;
                    }
                    let item = $('#city-item-template').clone();
                    item.removeClass('d-none');
                    item.attr('id', 'city-item-'+cityID);
                    item.find('input').val(cityID);
                    item.find('button').text(cityName);
                    item.appendTo('#city-item-list');
                }
                    
                $('#city-search-input').on('input', function(e) {
                    e.preventDefault();
                    let cityName = $(this).val();
                    if(cityName.length > 2) {
                        $('#city-search-list').load("{$citySearchUrl}", 'name='+cityName);
                    } else {
                        $('#city-search-list').empty();
                    }
                });

                $('#city-search-list').on('click', 'button', function (e) {
                    e.preventDefault();
                    let jthis = $(this);
                    appendCity(jthis.val(), jthis.text());
                    $('#city-item-'+allCityID).remove();
                });

                $('#city-item-list').on('click', 'button', function (e) {
                    e.preventDefault();
                    let jthis = $(this);
                    jthis.closest('.city-item').remove();
                    if($('#city-item-list').children().length < 1) { //template included
                        appendCity(allCityID, 'Все города');
                        $('#city-item-'+allCityID)
                            .find('button').removeClass('btn-outline-primary').addClass('btn-outline-secondary')
                            .attr('disabled', 'disabled');
                    }
                 })
JS)

            ?>
        </div>
        <div class="row mt-3" id="city-item-list">
            <div class="city-item" id="city-item-<?= $city->id ?>">
                <input type="hidden" name="<?= \yii\helpers\Html::getInputName($model, 'cityIDs') ?>[]" value="<?= $city->id ?>">
                <button type="button" class="btn btn-outline-primary"><?= $city->name ?></button>
            </div>
        </div>
    </div>
    <?php
    echo \yii\bootstrap4\Html::submitButton();
    \yii\bootstrap4\ActiveForm::end();
    ?>

    <div class="city-item d-none" id="city-item-template">
        <input type="hidden" name="<?= \yii\helpers\Html::getInputName($model, 'cityIDs') ?>[]" value="">
        <button type="button" class="btn btn-outline-primary"></button>
    </div>

</div>
