<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\frontend\models\Crl */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="crl-form">

    <?php $form=ActiveForm::begin();?>

    <?= $form->field($model, 'player_id')->textInput() ?>

    <?= $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'csr')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'crt')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'txtcrt')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'privkey')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'ts')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
