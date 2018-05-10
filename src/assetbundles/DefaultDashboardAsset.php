<?php
namespace verbb\defaultdashboard\assetbundles;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class DefaultDashboardAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init()
    {
        $this->sourcePath = "@verbb/defaultdashboard/resources/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->css = [
            'css/default-dashboard.css',
        ];

        parent::init();
    }
}
