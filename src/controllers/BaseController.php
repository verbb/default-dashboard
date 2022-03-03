<?php
namespace verbb\defaultdashboard\controllers;

use Craft;
use craft\web\Controller;

use verbb\defaultdashboard\DefaultDashboard;

class BaseController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionSettings(): void
    {
        $settings = DefaultDashboard::$plugin->getSettings();

        $this->renderTemplate('default-dashboard/settings', array(
            'settings' => $settings,
        ));
    }

}