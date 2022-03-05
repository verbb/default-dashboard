<?php
namespace verbb\defaultdashboard\controllers;

use verbb\defaultdashboard\DefaultDashboard;

use craft\web\Controller;

use yii\web\Response;

class BaseController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionSettings(): Response
    {
        $settings = DefaultDashboard::$plugin->getSettings();

        return $this->renderTemplate('default-dashboard/settings', array(
            'settings' => $settings,
        ));
    }

}