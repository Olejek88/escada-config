<?php

$this->title = Yii::t('app', 'Помощь');
?>
<div class="orders-index box-padding">

    <div class="panel panel-default">
        <div class="panel-body" style="padding: 15px;">
            <!-- <input type="text" class="form-control" id="inputEmail" placeholder="Введите Ваш вопрос" style="padding: 0px;"> -->
            <div class="input-group">
                <span class="input-group-addon" id="basic-addon1" style="border: 1px solid #fff;">
                    <i class="fa fa-search" aria-hidden="true"></i>
                </span>
                <input type="text" class="form-control" placeholder="Введите Ваш вопрос"
                       aria-describedby="basic-addon1">
            </div>
        </div>
    </div>


    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">

            <ul class="nav nav-tabs">
                <li class="active"><a href="#help" data-toggle="tab">Помощь</a></li>
                <li><a href="#listquestion" data-toggle="tab">Список вопросов</a></li>
                <li><a href="#">Задать вопрос</a></li>
            </ul>

            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="help">
                    <h6 class="text-center">
                        Данный раздел находится в разработке..
                    </h6>
                </div>

                <div class="tab-pane fade" id="listquestion">
                    <h6 class="text-center">
                        Данный раздел находится в разработке..
                    </h6>
                </div>
            </div>


        </div>

        <!-- <div class="panel-body">

            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">
                    <h6 class="text-center">

                    </h6>
                </div>
            </div>

        </div> -->

    </div>
</div>
