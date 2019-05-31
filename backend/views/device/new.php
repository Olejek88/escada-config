<?php

use common\models\Device;
use yii\helpers\Html;

/* @var $model Device */
/* @var $equipments Device[] */

$this->title = 'Добавляем оборудование';
?>
<div class="order-status-view box-padding">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?php echo Html::encode($this->title) ?>
            </h3>
        </div>
        <div class="panel-body">
            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">
                    <table id="w0" class="table table-striped table-bordered detail-view">
                        <tbody>
                        <tr>
                            <th>uuid</th>
                            <th>адрес</th>
                        </tr>
                        <?php
                        foreach ($equipments as $equipment) {
                            echo '<tr><td>' . $equipment['uuid'] . '</td><td>' .
                                'ул.' . $equipment['house']['street']->title .
                                ', д.' . $equipment['house']->number .
                                ', кв.' . $equipment['flat']->number
                                . '</td></tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
