<?php

use common\components\MainFunctions;
use common\models\ObjectType;
use common\models\House;
use common\models\User;
use dosamigos\leaflet\layers\Marker;
use dosamigos\leaflet\layers\TileLayer;
use dosamigos\leaflet\LeafLet;
use dosamigos\leaflet\plugins\geocoder\GeoCoder;
use dosamigos\leaflet\plugins\geocoder\ServiceNominatim;
use dosamigos\leaflet\types\LatLng;
use dosamigos\leaflet\widgets\Map;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$latDefault = 55.160374;
$lngDefault = 61.402738;

/* @var $this yii\web\View */
/* @var $model common\models\Objects */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    if (!$model->isNewRecord) {
        echo $form->field($model, 'uuid')->hiddenInput()->label(false);
    } else {
        echo $form->field($model, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
    }
    ?>

    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    <?php echo $form->field($model, 'oid')->hiddenInput(['value' => User::ORGANISATION_UUID])->label(false); ?>

    <?php
    $houses = House::find()->all();
    $items = ArrayHelper::map($houses, 'uuid', function ($model) {
        return $model['street']['city']['title'].','.$model->getFullTitle();
    });
    echo $form->field($model, 'houseUuid')->widget(Select2::class,
        [
            'data' => $items,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите объект..'
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    ?>

    <?php
    $types = ObjectType::find()->all();
    $items = ArrayHelper::map($types, 'uuid', 'title');
    echo $form->field($model, 'objectTypeUuid')->widget(Select2::class,
        [
            'data' => $items,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите тип..'
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    ?>

    <?php echo $form->field($model, 'latitude')->hiddenInput(['maxlength' => true, 'value' => $latDefault])->label(false) ?>
    <?php echo $form->field($model, 'longitude')->hiddenInput(['maxlength' => true, 'value' => $lngDefault])->label(false) ?>

    <?php
    // lets use nominating service
    $nominatim = new ServiceNominatim();

    // create geocoder plugin and attach the service
    $geoCoderPlugin = new GeoCoder([
        'service' => $nominatim,
        'clientOptions' => [
            // we could leave it to allocate a marker automatically
            // but I want to have some fun
            'showMarker' => false,
        ]
    ]);

    // first lets setup the center of our map
    $center = new LatLng(['lat' => $latDefault, 'lng' => $lngDefault]);

    // now lets create a marker that we are going to place on our map
    $marker = new Marker([
        'latLng' => $center,
//        'popupContent' => 'Hi!',
        'name' => 'geoMarker',
        'clientOptions' => ['draggable' => true],
        'clientEvents' => [
            'dragend' => 'function(e){
//                console.log(e.target._latlng.lat, e.target._latlng.lng);
                $("#objects-latitude").val(e.target._latlng.lat);
                $("#objects-longitude").val(e.target._latlng.lng);
            }'
        ],
    ]);
    // The Tile Layer (very important)
    $tileLayer = new TileLayer([
//        'urlTemplate' => 'http://a.tile.openstreetmap.org/{z}/{x}/{y}.png',
        'urlTemplate' => 'http://{s}.tiles.mapbox.com/v4/mapquest.streets-mb/{z}/{x}/{y}.{ext}?access_token=pk.eyJ1IjoibWFwcXVlc3QiLCJhIjoiY2Q2N2RlMmNhY2NiZTRkMzlmZjJmZDk0NWU0ZGJlNTMifQ.mPRiEubbajc6a5y9ISgydg',
        'clientOptions' => [
            'attribution' => 'Tiles &copy; <a href="http://www.osm.org/copyright" target="_blank">OpenStreetMap contributors</a> />',
            'subdomains' => '1234',
//            'id' => 'mapbox.streets',
            'type' => 'osm',
            's' => 'a',
            'ext' => 'png',

        ]
    ]);

    // now our component and we are going to configure it
    $leafLet = new LeafLet([
        'name' => 'geoMap',
        'center' => $center,
        'tileLayer' => $tileLayer,
        'clientEvents' => [
            'geocoder_showresult' => 'function(e){
                // set markers position
                geoMarker.setLatLng(e.Result.center);
                $("#objects-latitude").val(e.Result.center.lat);
                $("#objects-longitude").val(e.Result.center.lng);
            }'
        ],
    ]);
    // Different layers can be added to our map using the `addLayer` function.
    $leafLet->addLayer($marker);      // add the marker
    //    $leafLet->addLayer($tileLayer);  // add the tile layer

    // install the plugin
    $leafLet->installPlugin($geoCoderPlugin);

    // finally render the widget
    try {
        echo Map::widget(['leafLet' => $leafLet]);
    } catch (Exception $exception) {
        echo '<div id="map"/>';
    }

    // echo $leaflet->widget();
    ?>

    <div class="form-group text-center">
        <?php
        echo Html::submitButton(
            $model->isNewRecord ? Yii::t('app', 'Создать') : Yii::t('app', 'Обновить'),
            [
                'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
            ]
        );
        ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
