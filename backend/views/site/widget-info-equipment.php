<?php
/* @var $equipmentsCount
 * @var $equipmentTypesCount */
?>
<div class="info-box bg-yellow">
    <span class="info-box-icon"><i class="fa fa-cogs"></i></span>

    <div class="info-box-content">
        <span class="info-box-text">Оборудование</span>
        <span class="info-box-number"><?= $equipmentsCount ?></span>

        <div class="progress">
            <div class="progress-bar" style="width: 50%"></div>
        </div>
        <span class="progress-description">
            По <?= $equipmentTypesCount ?> типам
        </span>
    </div>
</div>
