<?php

use kartik\select2\Select2;
use wbraganca\fancytree\FancytreeWidget;
use yii\web\JsExpression;

/* @var $deviceTypes */

$this->title = 'Дерево оборудования';

?>

    <table id="tree" style="width: 100%">
        <colgroup>
            <col width="*">
            <col width="120px">
            <col width="140px">
            <col width="120px">
            <col width="150px">
        </colgroup>
        <thead style="background-color: #337ab7; color: white">
        <tr>
            <th colspan="1">
                <?php
                try {
                    echo Select2::widget([
                        'id' => 'type_select',
                        'name' => 'type_select',
                        'language' => 'ru',
                        'data' => $deviceTypes,
                        'options' => ['placeholder' => 'Выберите тип устройств...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                } catch (Exception $e) {

                }
                ?>
            </th>
            <th align="center" colspan="1" style="background-color: #3c8dbc; color: whitesmoke">
                <button class="btn btn-success" type="button" id="addButton" style="padding: 5px 10px">
                    Выбрать
                </button>
            </th>
            <th align="center" colspan="4" style="background-color: #3c8dbc; color: whitesmoke">Оборудование системы
            </th>
        </tr>
        <tr style="background-color: #3c8dbc; color: whitesmoke">
            <th align="center">Оборудование</th>
            <th>Статус</th>
            <th>Дата</th>
            <th>Показания</th>
            <th>Регистр</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td></td>
            <td class="alt"></td>
            <td class="center"></td>
            <td class="alt"></td>
            <td class="center"></td>
        </tr>
        </tbody>
    </table>
<?php
$this->registerJsFile('/js/custom/modules/list/jquery.fancytree.contextMenu.js',
    ['depends' => ['wbraganca\fancytree\FancytreeAsset']]);
$this->registerJsFile('/js/custom/modules/list/jquery.contextMenu.min.js',
    ['depends' => ['yii\jui\JuiAsset']]);
$this->registerCssFile('/css/custom/modules/list/ui.fancytree.css');
$this->registerCssFile('/css/custom/modules/list/jquery.contextMenu.min.css');

echo FancytreeWidget::widget([
    'options' => [
        'id' => 'tree',
        'source' => $device,
        'checkbox' => true,
        'selectMode' => 3,
        'extensions' => ['dnd', "glyph", "table", "contextMenu"],
        'glyph' => 'glyph_opts',
        'dnd' => [
            'preventVoidMoves' => true,
            'preventRecursiveMoves' => true,
            'autoExpandMS' => 400,
            'dragStart' => new JsExpression('function(node, data) {
				return true;
			}'),
            'dragEnter' => new JsExpression('function(node, data) {
				return true;
			}'),
            'dragDrop' => new JsExpression('function(node, data) {
				data.otherNode.moveTo(node, data.hitMode);
			}'),
        ],
        'contextMenu' => [
            'menu' => [
                'new' => [
                    'name' => 'Добавить новое',
                    'icon' => 'add',
                    'callback' => new JsExpression('function(key, opt) {
                        var node = $.ui.fancytree.getNode(opt.$trigger);
                        if (node.folder==true) {
                            $.ajax({
                                url: "new",
                                type: "post",
                                data: {
                                    selected_node: node.key,
                                    folder: node.folder,
                                    uuid: node.data.uuid,
                                    type: node.type,
                                    model_uuid: node.data.model_uuid,
                                    type_uuid: node.data.type_uuid                                                                        
                                },
                                success: function (data) { 
                                    $(\'#modalAddEquipment\').modal(\'show\');
                                    $(\'#modalContentEquipment\').html(data);
                                }
                           }); 
                        }                        
                    }')
                ],
                'edit' => [
                    'name' => 'Редактировать',
                    'icon' => 'edit',
                    'callback' => new JsExpression('function(key, opt) {
                        var node = $.ui.fancytree.getNode(opt.$trigger);
                            $.ajax({
                                url: "edit",
                                type: "post",
                                data: {
                                    selected_node: node.key,
                                    folder: node.folder,
                                    uuid: node.data.uuid,
                                    type: node.type,
                                    model_uuid: node.data.model_uuid,
                                    type_uuid: node.data.type_uuid,
                                    reference: "equipment"                                                                        
                                },
                                success: function (data) { 
                                    $(\'#modalAddEquipment\').modal(\'show\');
                                    $(\'#modalContentEquipment\').html(data);
                                }
                           }); 
                    }')
                ],
                'doc' => [
                    'name' => 'Добавить документацию',
                    'icon' => 'add',
                    'callback' => new JsExpression('function(key, opt) {
                            var node = $.ui.fancytree.getNode(opt.$trigger);
                            $.ajax({
                                url: "../documentation/add",
                                type: "post",
                                data: {
                                    selected_node: node.key,
                                    folder: node.folder,
                                    uuid: node.data.uuid,
                                    model_uuid: node.data.model_uuid                                    
                                },
                                success: function (data) { 
                                    $(\'#modalAddDocumentation\').modal(\'show\');
                                    $(\'#modalContent\').html(data);
                                }
                            });
                    }')
                ],
                'defect' => [
                    'name' => 'Добавить дефект',
                    'icon' => 'add',
                    'callback' => new JsExpression('function(key, opt) {
                            var node = $.ui.fancytree.getNode(opt.$trigger);
                            $.ajax({
                                url: "../defect/add",
                                type: "post",
                                data: {
                                    selected_node: node.key,
                                    folder: node.folder,
                                    uuid: node.data.uuid,
                                    model_uuid: node.data.model_uuid                                    
                                },
                                success: function (data) { 
                                    $(\'#modalAddDefect\').modal(\'show\');
                                    $(\'#modalContentDefect\').html(data);
                                }
                            });
                    }')
                ]
            ]
        ],
        'table' => [
            'indentation' => 20,
            "titleColumnIdx" => "1",
            "statusColumnIdx" => "2",
            "dateColumnIdx" => "3",
            "measureColumnIdx" => "4",
            "registerColumnIdx" => "5"
        ],
        'renderColumns' => new JsExpression('function(event, data) {
            var node = data.node;
            $tdList = $(node.tr).find(">td");
            $tdList.eq(1).html(node.data.status);
            $tdList.eq(2).html(node.data.date);
            $tdList.eq(3).html(node.data.measure);
            $tdList.eq(4).html(node.data.register);
        }')
    ]
]);

$this->registerJs('$("#addButton").on("click",function() {
        var e = document.getElementById("type_select");
        var strType = e.options[e.selectedIndex].value;
        window.location.replace("tree?type="+strType);             
    })');
?>