<?php
namespace verbb\defaultdashboard\base;

use verbb\defaultdashboard\DefaultDashboard;
use verbb\defaultdashboard\models\Settings;
use verbb\defaultdashboard\services\Service;

use Craft;

use yii\log\Logger;

use verbb\base\BaseHelper;

trait PluginTrait
{
    // Properties
    // =========================================================================

    public static DefaultDashboard $plugin;


    // Static Methods
    // =========================================================================

    public static function log($message, $attributes = []): void
    {
        /* @var Settings $settings */
        $settings = DefaultDashboard::$plugin->getSettings();

        if ($attributes) {
            $message = Craft::t('default-dashboard', $message, $attributes);
        }

        // Check if we should log
        if (!$settings->logInfo) {
            return;
        }

        Craft::getLogger()->log($message, Logger::LEVEL_INFO, 'default-dashboard');
    }

    public static function error($message, $attributes = []): void
    {
        /* @var Settings $settings */
        $settings = DefaultDashboard::$plugin->getSettings();

        if ($attributes) {
            $message = Craft::t('default-dashboard', $message, $attributes);
        }

        // Check if we should log
        if (!$settings->logErrors) {
            return;
        }

        Craft::getLogger()->log($message, Logger::LEVEL_ERROR, 'default-dashboard');
    }


    // Public Methods
    // =========================================================================

    public function getService(): Service
    {
        return $this->get('service');
    }


    // Private Methods
    // =========================================================================

    private function _setPluginComponents(): void
    {
        $this->setComponents([
            'service' => Service::class,
        ]);

        BaseHelper::registerModule();
    }

    private function _setLogging(): void
    {
        BaseHelper::setFileLogging('default-dashboard');
    }

}