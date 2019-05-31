<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/vendor/lib/bootstrap.theme.min.css',
        'css/vendor/lib/site.css',
        'css/custom/modules/map/load.css',
        'css/custom/modules/map/map.css',
        'css/custom/modules/list/list.css',
        'css/custom/modules/list/prediction.css',
        'css/custom/modules/map/history.css',
        'css/custom/modules/map/journal.css',
        'css/custom/modules/map/track.css',
        'css/custom/modules/map/leaflet.css',
        'css/custom/modules/profile/profile.css',
        'css/custom/modules/profile/settings.css',
        'css/custom/modules/actions/app.css',
        'css/custom/modules/list/tree.css'
    ];
    public $js = [
        'js/custom/modules/profile/profile.js',
        'js/custom/modules/map/journal.js',
        'js/custom/modules/map/load.js',
        'js/custom/modules/list/list.js',
        'js/custom/modules/list/preducation.js',
        'js/custom/modules/list/list.js',
        'js/custom/modules/map/leaflet.js',
        'js/custom/modules/actions/result.js',
        'js/custom/modules/actions/entities.js',
        'js/custom/modules/actions/app.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
