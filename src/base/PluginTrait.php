<?php
namespace verbb\defaultdashboard\base;

use verbb\defaultdashboard\DefaultDashboard;
use verbb\defaultdashboard\services\Service;

use Craft;

use yii\log\Logger;

use verbb\base\BaseHelper;

trait PluginTrait
{
    // Properties
    // =========================================================================

    public static DefaultDashboard $plugin;


    // Public Methods
    // =========================================================================

    public function getService(): Service
    {
        return $this->get('service');
    }

    public static function log($message, $attributes = []): void
    {
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