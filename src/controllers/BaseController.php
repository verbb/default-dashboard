<?php
namespace verbb\defaultdashboard\controllers;

use verbb\defaultdashboard\DefaultDashboard;
use verbb\defaultdashboard\models\Settings;

use craft\web\Controller;

use yii\web\Response;

class BaseController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionSettings(): Response
    {
        /* @var Settings $settings */
        $settings = DefaultDashboard::$plugin->getSettings();

        return $this->renderTemplate('default-dashboard/settings', [
            'settings' => $settings,
        ]);
    }

}