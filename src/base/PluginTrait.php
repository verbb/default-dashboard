<?php
namespace verbb\defaultdashboard\base;

use verbb\defaultdashboard\DefaultDashboard;
use verbb\defaultdashboard\models\Settings;
use verbb\defaultdashboard\services\Service;
use verbb\base\BaseHelper;

use Craft;

use yii\log\Logger;

trait PluginTrait
{
    // Properties
    // =========================================================================

    public static DefaultDashboard $plugin;


    // Static Methods
    // =========================================================================

    public static function log(string $message, array $params = []): void
    {
        $message = Craft::t('default-dashboard', $message, $params);

        Craft::getLogger()->log($message, Logger::LEVEL_INFO, 'default-dashboard');
    }

    public static function error(string $message, array $params = []): void
    {
        $message = Craft::t('default-dashboard', $message, $params);

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

    private function _registerComponents(): void
    {
        $this->setComponents([
            'service' => Service::class,
        ]);

        BaseHelper::registerModule();
    }

    private function _registerLogTarget(): void
    {
        BaseHelper::setFileLogging('default-dashboard');
    }

}