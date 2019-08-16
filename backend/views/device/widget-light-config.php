<?php
/* @var $device
 * @var $parameters
 */

use kartik\slider\Slider;
use yii\helpers\Html;
use yii\widgets\Pjax;

?>
<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Параметры программы освещения</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <?php Pjax::begin(['id' => 'options']); ?>
            <?= Html::beginForm(['device/dashboard','uuid' => $device['uuid']], 'post',
                ['data-pjax' => '', 'class' => 'form-inline']); ?>
            <div class="col-md-12">
                <?php
                echo '<label class="control-label">Время установки уровня освещения (в секундах с начала суток)</label>';
                ?>
            </div>
            <div class="col-md-6">
                <?php
                echo Html::hiddenInput('type', 'config');

                echo '<label class="control-label">Уровень #0</label>';
                echo '<span></br></br></span>';
                echo '<div style="margin: 5px; width: 100%">';
                echo Html::hiddenInput('device', $device['uuid']);
                echo Slider::widget([
                    'name' => 'time0',
                    'sliderColor' => Slider::TYPE_INFO,
                    'handleColor' => Slider::TYPE_INFO,
                    'options' => [
                        'width' => '250px'
                    ],
                    'pluginOptions' => [
                        'orientation' => 'horizontal',
                        'handle' => 'square',
                        'min' => 0,
                        'max' => 1440,
                        'step' => 10,
                        'tooltip' => 'always'
                    ],
                ]);
                echo '</div>';
                echo '<span></br></span>';
                echo '<div style="margin: 5px; width: 100%">';
                echo Slider::widget([
                    'name' => 'level0',
                    'sliderColor' => Slider::TYPE_INFO,
                    'handleColor' => Slider::TYPE_INFO,
                    'pluginOptions' => [
                        'orientation' => 'horizontal',
                        'handle' => 'square',
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                        'tooltip' => 'always'
                    ],
                ]);
                echo '</div>';
                echo '<label class="control-label">Уровень #1</label>';
                echo '<span></br></br></span>';
                echo '<div style="margin: 5px; width: 100%">';
                echo Slider::widget([
                    'name' => 'time1',
                    'sliderColor' => Slider::TYPE_INFO,
                    'handleColor' => Slider::TYPE_INFO,
                    'options' => [
                        'width' => '250px'
                    ],
                    'pluginOptions' => [
                        'orientation' => 'horizontal',
                        'handle' => 'square',
                        'min' => 0,
                        'max' => 1440,
                        'step' => 10,
                        'tooltip' => 'always'
                    ],
                ]);
                echo '</div>';
                echo '<span></br></span>';
                echo '<div style="margin: 5px; width: 100%">';
                echo Slider::widget([
                    'name' => 'level1',
                    'sliderColor' => Slider::TYPE_INFO,
                    'handleColor' => Slider::TYPE_INFO,
                    'pluginOptions' => [
                        'orientation' => 'horizontal',
                        'handle' => 'square',
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                        'tooltip' => 'always'
                    ],
                ]);
                echo '</div>';
                ?>
            </div>
            <div class="col-md-6">
                <?php
                echo '<label class="control-label">Уровень #2</label>';
                echo '<span></br></br></span>';
                echo '<div style="margin: 5px; width: 100%">';
                echo Html::hiddenInput('device', $device['uuid']);
                echo Slider::widget([
                    'name' => 'time2',
                    'sliderColor' => Slider::TYPE_INFO,
                    'handleColor' => Slider::TYPE_INFO,
                    'options' => [
                        'width' => '250px'
                    ],
                    'pluginOptions' => [
                        'orientation' => 'horizontal',
                        'handle' => 'square',
                        'min' => 0,
                        'max' => 1440,
                        'step' => 10,
                        'tooltip' => 'always'
                    ],
                ]);
                echo '</div>';
                echo '<span></br></span>';
                echo '<div style="margin: 5px; width: 100%">';
                echo Slider::widget([
                    'name' => 'level2',
                    'sliderColor' => Slider::TYPE_INFO,
                    'handleColor' => Slider::TYPE_INFO,
                    'pluginOptions' => [
                        'orientation' => 'horizontal',
                        'handle' => 'square',
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                        'tooltip' => 'always'
                    ],
                ]);
                echo '</div>';
                echo '<label class="control-label">Уровень #3</label>';
                echo '<span></br></br></span>';
                echo '<div style="margin: 5px; width: 100%">';
                echo Slider::widget([
                    'name' => 'time3',
                    'sliderColor' => Slider::TYPE_INFO,
                    'handleColor' => Slider::TYPE_INFO,
                    'options' => [
                        'width' => '250px'
                    ],
                    'pluginOptions' => [
                        'orientation' => 'horizontal',
                        'handle' => 'square',
                        'min' => 0,
                        'max' => 1440,
                        'step' => 10,
                        'tooltip' => 'always'
                    ],
                ]);
                echo '</div>';
                echo '<span></br></span>';
                echo '<div style="margin: 5px; width: 100%">';
                echo Slider::widget([
                    'name' => 'level3',
                    'sliderColor' => Slider::TYPE_INFO,
                    'handleColor' => Slider::TYPE_INFO,
                    'pluginOptions' => [
                        'orientation' => 'horizontal',
                        'handle' => 'square',
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                        'tooltip' => 'always'
                    ],
                ]);
                echo '</div>';
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php
                echo '<div class="modal-footer">';
                echo Html::submitButton('Задать', ['class' => 'btn btn-success', 'name' => 'button']);
                echo Html::endForm();
                echo '</div>';
                Pjax::end();
                ?>
            </div>
        </div>
    </div>
</div>
