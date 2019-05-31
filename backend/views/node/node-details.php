<?php

use common\components\MainFunctions;
use common\models\Device;
use common\models\Measure;
use common\models\Node;
use common\models\Photo;
use yii\helpers\Html;

/* @var $model Node */

$equipment = Device::find()
    ->where(['uuid' => $model['uuid']])
    ->one();
$models = Device::findOne($model['_id']);

?>
<div class="kv-expand-row kv-grid-demo">
    <div class="kv-expand-detail skip-export kv-grid-demo">
        <div class="skip-export kv-expanded-row kv-grid-demo" data-index="0" data-key="1">
            <div class="kv-detail-content">
                <h3><?php echo '' ?></h3>
                <div class="row">
                    <div class="col-sm-2">
                        <table class="table table-bordered table-condensed table-hover small kv-table">
                            <tr class="success">
                                <th colspan="2" class="text-center text-danger">Последние показания</th>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-4">
                        <table class="table table-bordered table-condensed table-hover small kv-table">
                            <tr class="danger">
                                <td class="text-center">
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
