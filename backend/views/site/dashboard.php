<?php
/* @var $categories
 * @var $values
 * @var $stat_categories
 * @var $stat_values
 * @var $stat_values2
 * @var $measures
 * @var $stats
 * @var $devices
 * @var $threads
 * @var $info
 * @var $measureDataProvider
 * @var $threadDataProvider
 */

use yii\helpers\Html;

$this->title = Yii::t('app', 'Сводная');
?>

<br/>
<!-- Main row -->
<div class="row">
    <!-- Left col -->
    <div class="col-md-6">
        <div class="row">
            <div class="col-md-12">
            <?= $this->render('widget-power', ['categories' => $categories, 'values' => $values]); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
            <?= $this->render('widget-archive', ['measures' => $measures, 'measureDataProvider' => $measureDataProvider]); ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="row">
            <div class="col-md-12">
            <?= $this->render('widget-stats', ['categories' => $stat_categories, 'values' => $stat_values, 'values2' => $stat_values2]); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
            <?= $this->render('widget-equipment-tree', ['devices' => $devices]); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
            <?= $this->render('widget-thread', ['threads' => $threads, 'threadDataProvider' => $threadDataProvider]); ?>
            </div>
        </div>
    </div>
</div>
<!-- /.content-wrapper -->

<footer class="main-footer" style="margin-left: 0 !important;">
    <div class="pull-right hidden-xs" style="vertical-align: middle; text-align: center;">
        <b>Version</b> 0.0.2
    </div>
    <?php echo Html::a('<img src="images/mtm.png">', 'http://www.mtm-smart.com'); ?>
    <strong>Copyright &copy; 2019 <a href="http://www.mtm-smart.com">MTM Смарт</a>.</strong> Все права на
    программный продукт защищены.
</footer>
