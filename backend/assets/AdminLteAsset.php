<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AdminLteAsset extends AssetBundle
{
    public $basePath = '@vendor/almasaeed2010/adminlte';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/vendor/lib/bootstrap.theme.min.css',
    ];
    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
