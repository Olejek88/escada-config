<?php
/* @var $contragents */

use wbraganca\fancytree\FancytreeWidget;
use yii\web\JsExpression;

$this->title = 'Дерево абонентов системы';

?>
<table id="tree" style="width: 100%; font-weight: normal">
    <colgroup>
        <col width="*">
        <col width="160px">
        <col width="120px">
        <col width="130px">
        <col width="130px">
        <col width="160px">
        <col width="130px">
        <col width="120px">
        <col width="*">
    </colgroup>
    <thead style="background-color: #337ab7; color: white">
    <tr>
        <th align="center" colspan="12" style="background-color: #3c8dbc; color: whitesmoke">Абоненты</th>
    </tr>
    <tr style="background-color: #3c8dbc; color: whitesmoke; font-weight: normal">
        <th align="center">Адрес</th>
        <th align="center">Ответственный</th>
        <th>Статус</th>
        <th>Дата обхода</th>
        <th>Показания</th>
        <th>Пользователь</th>
        <th>Дата фото</th>
        <th>Фото</th>
        <th>Сообщение</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td></td>
        <td class="alt"></td>
        <td class="center"></td>
        <td class="alt"></td>
        <td class="center"></td>
        <td class="alt"></td>
        <td class="center"></td>
        <td class="alt"></td>
        <td class="center"></td>
    </tr>
    </tbody>
</table>
<?php echo FancytreeWidget::widget([
    'options' => [
        'id' => 'tree',
        'source' => $contragents,
        'extensions' => ['dnd', "glyph", "table"],
        'glyph' => 'glyph_opts',
        'edit' => [
            'triggerStart' => ["clickActive", "dblclick", "f2", "mac+enter", "shift+click"],
            'save' => new JsExpression('function(event, data) {
                            setTimeout(function(){
                                $(data.node.span).removeClass("pending");
                                data.node.setTitle(data.node.title);
                            }, 2000);
                            return true;
                        }'),
            'close' => new JsExpression('function(event, data) {
                            if(data.save) {
                                 $(data.node.span).addClass("pending");
                                 $.ajax({
                                    url: "rename",
                                    type: "post",
                                    data: {
                                      uuid: data.node.key,
                                      param: data.node.title                                            
                                    },
                                    success: function (data) {
                                       }
                                 });
                            }
                        }')
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
                        if (node.folder==false) {
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
                        }                        
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
            "userColumnIdx" => "2",
            "statusColumnIdx" => "3",
            "dateMeasureColumnIdx" => "4",
            "valueColumnIdx" => "5",
            "userMeasureColumnIdx" => "6",
            "datePhotoColumnIdx" => "7",
            "photoColumnIdx" => "8",
            "messageColumnIdx" => "9"
        ],
        'renderColumns' => new JsExpression('function(event, data) {
            var node = data.node;
            $tdList = $(node.tr).find(">td");
            $tdList.eq(1).html(node.data.user);
            $tdList.eq(2).html(node.data.status);
            $tdList.eq(3).html(node.data.measure_date);
            $tdList.eq(4).text(node.data.measure_value);
            $tdList.eq(5).html(node.data.measure_user);
            $tdList.eq(6).html(node.data.photo_date);
            $tdList.eq(7).html(node.data.photo);
            $tdList.eq(8).html(node.data.message);
        }')
    ]
]);
?>


<div class="modal remote fade" id="modalRegister">
    <div class="modal-dialog">
        <div class="modal-content loader-lg">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title text-center">Показания</h4>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-hover text-left">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Время</th>
                        <th>Значение</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!--                    <?php /*foreach ($registers as $register): */ ?>
                        <tr>
                            <td><? /*= $register['uuid'] */ ?></td>
                            <td><? /*= $register['user'] */ ?></td>
                            <td><? /*= $register['type'] */ ?></td>
                            <td><? /*= $register['date'] */ ?></td>
                        </tr>
                    --><?php /*endforeach; */ ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>