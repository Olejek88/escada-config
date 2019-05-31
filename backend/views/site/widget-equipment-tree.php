<?php
use wbraganca\fancytree\FancytreeWidget;
use yii\helpers\Html;
use yii\web\JsExpression;

/* @var $device */
?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Устройства системы</h3>
        <div class="box-tools pull-right">
            <div class="btn-group">
                <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-bars"></i></button>
                <ul class="dropdown-menu pull-right" role="menu">
                    <li><?php echo Html::a("Все оборудование","/device/table") ?></li>
                    <li><?php echo Html::a("Деревом","/device/tree") ?></li>
                </ul>
            </div>
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
            </button>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <table id="tree" cellspacing="1">
            <colgroup>
                <col width="*">
                <col width="*">
                <col width="100px">
                <col width="140px">
            </colgroup>
            <thead style="background-color: #337ab7; color: white">
            <tr style="background-color: #3c8dbc; color: whitesmoke">
                <th align="center">Оборудование</th>
                <th>Расположение</th>
                <th>Статус</th>
                <th>Дата</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td></td>
                <td class="alt"></td>
                <td class="center"></td>
                <td class="alt"></td>
            </tr>
            </tbody>
        </table>
        <?php
            echo FancytreeWidget::widget([
            'options' => [
                'id' => 'tree',
                'source' => $device,
                'extensions' => ["glyph", "table"],
                'glyph' => 'glyph_opts',
                'table' => [
                    'indentation' => 20,
                    "titleColumnIdx" => "1",
                    "locationColumnIdx" => "2",
                    "statusColumnIdx" => "3",
                    "userColumnIdx" => "4"
                ],
                'renderColumns' => new JsExpression('function(event, data) {
                        var node = data.node;
                        $tdList = $(node.tr).find(">td");
                        $tdList.eq(1).text(node.data.location);
                        $tdList.eq(2).html(node.data.status);
                        $tdList.eq(3).html(node.data.date); 
                   }')
            ]
        ]);
        ?>
    </div>
</div>
