<?php
use dosamigos\leaflet\LeafLetAsset;
/* @var $coordinates
 * @var $devicesGroup
 * @var $devicesList
 */

LeafLetAsset::register($this);

?>
<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">Карта объектов и устройств</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
            </button>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body no-padding">
        <div class="row">
            <div class="col-md-9 col-sm-8" style="width: 100%">
                <div class="pad" style="padding: 1px">
                    <div id="mapid" style="width: 100%; height: 360px"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.box-body -->
</div>
<script>
    var houseIcon = L.icon({
        iconUrl: '/images/house_marker2.png',
        iconSize: [35, 35],
        iconAnchor: [22, 94],
        popupAnchor: [-3, -76]
    });

    <?php
    echo $devicesList;
    echo $devicesGroup;
    ?>

    var overlayMapsA = {};
    var overlayMapsB = {
        "Устройства":  devices
    };
    var map = L.map('mapid', {zoomControl: false, layers: [devices]}).setView(<?= $coordinates ?>, 13);
    L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
        maxZoom: 18,
        id: 'mapbox.streets'
    }).addTo(map);

    L.control.layers(overlayMapsA, overlayMapsB, {
        position: 'bottomleft'
    }).addTo(map);

    L.control.zoom({
        position: 'bottomleft'
    }).addTo(map);

</script>
