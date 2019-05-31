<?php

/* @var $events */
/* @var $today_date */

$this->title = Yii::t('app', 'МТМ::События системы');

?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Лента событий
            <small>действия оператора, события системы</small>
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <ul class="timeline">
                    <li class="time-label">
                        <span class="bg-blue">
                             <?php echo $today_date ?>
                        </span>
                    </li>
                    <?php
                    if (count($events)) {
                        $date = $events[0]['date'];
                        foreach ($events as $event) {
                            if ($event['date'] != $date) {
                                $date = $event['date'];
                                echo '<li class="time-label"><span class="bg-aqua btn-xs">' .
                                    date("d-m-Y", strtotime($date)) . '</span></li>';
                            }
                            echo $event['event'];
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
    </section>
</div>
