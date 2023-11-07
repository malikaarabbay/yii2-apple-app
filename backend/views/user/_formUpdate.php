<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\User;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\User $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="user-form">

    <div class="row">
        <div class="col-md-8 col-xs-12">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'status')->dropDownList(User::getStatusList()) ?>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>

        <div class="col-md-4 col-xs-12">
            <?php $form = ActiveForm::begin(['action' =>  Url::toRoute('user/change-password'), 'id' => 'change_password-form']); ?>

                <?= $form->field($passwordChangeForm, 'userid')->hiddenInput(['value' => $user->id])->label(false) ?>

                <?= $form->field($passwordChangeForm, 'password')->passwordInput(['maxlength' => 255]) ?>

                <?= $form->field($passwordChangeForm, 'passwordRepeat')->passwordInput(['maxlength' => 255])?>

                <?= Html::submitButton('<span class="glyphicon glyphicon-refresh"></span> Изменить',['class' => 'btn btn-primary']) ?>
        
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
