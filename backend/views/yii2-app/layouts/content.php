<?php

use dmstr\widgets\Alert;
use yii\helpers\Inflector;
use yii\widgets\Breadcrumbs;

/* @var $content string */

?>

<div class="content-wrapper" style="height: 100%; min-height: 782px">
    <section class="content-header" style="padding: 0;">
        <?php if (isset($this->blocks['content-header'])) { ?>
            <h1><?= $this->blocks['content-header'] ?></h1>
        <?php } else { ?>
            <h1>
                <?php
                if ($this->title !== null) {
                    // echo \yii\helpers\Html::encode($this->title); // My Yii Application
                } else {
                    echo Inflector::camel2words(Inflector::id2camel($this->context->module->id));
                    echo ($this->context->module->id !== \Yii::$app->id) ? '<small>Module</small>' : '';
                } ?>
            </h1>
        <?php } ?>

        <?=
        Breadcrumbs::widget(
            [
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]
        ) ?>
    </section>

    <section class="content" style="padding: 3px;">
        <?= Alert::widget() ?>
        <?= $content ?>
    </section>
</div>

<!-- <div class='control-sidebar-bg'></div> -->
