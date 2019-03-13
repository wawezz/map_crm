<?php

namespace backend\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class AppAssets extends AssetBundle
{
    public $sourcePath = __DIR__ . '/sources';

    public $css = [
    ];

    public $js = [

    ];

    public $depends = [
        JqueryAsset::class,
    ];
}
