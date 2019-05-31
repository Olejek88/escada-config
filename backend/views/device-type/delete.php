<?php
use yii\helpers\Html;

/* @var $message string */

$this->title = Yii::t('app', 'Типы оборудования');
?>


    <h3><?php echo $message ?></h3>
    <br/>
<?php echo Html::a('Типы оборудования', '/equipment-type/index'); ?>