<?php

use yii\helpers\Html;

$this->title = $name;
?>


<div class="wrapper-block">
    <div style="padding-top: 100px;">
        <div class="panel panel-default" style="width: 600px; margin: 0px auto; padding: 10px;">
            <div class="panel-body">
                <section class="content">

                    <div class="error-page">
                        <h2 class="headline text-info"><i class="fa fa-warning text-yellow"></i></h2>

                        <div class="error-content">
                            <h3><?= $name ?></h3>

                            <p>
                                <?= nl2br(Html::encode($message)) ?>
                            </p>

                            <p>
                                Возникла ошибка, когда веб-сервер обрабатывал ваш запрос.
                                <br/>Вернуться к <a href='<?= Yii::$app->homeUrl ?> '> главной странице</a>
                            </p>

                        </div>
                    </div>

                </section>

            </div>
        </div>
    </div>

</div>
