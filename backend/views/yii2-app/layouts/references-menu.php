<div class="panel panel-default" style="float: left; width: 20%; padding: 3px">
    <?php

    use yii\helpers\Html;

    echo Html::a("Статусы оборудования", ['../device-status'], ['class' => 'btn btn-info btn100']);
    echo Html::a("Типы оборудования", ['../device-type'], ['class' => 'btn btn-primary btn100']);
    echo Html::a("Типы измерений", ['../measure-type'], ['class' => 'btn btn-primary btn100']);
    ?>
</div>
