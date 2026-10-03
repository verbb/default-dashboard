<?php
namespace verbb\defaultdashboard\base;

use verbb\defaultdashboard\DefaultDashboard;
use verbb\defaultdashboard\models\Settings;
use verbb\defaultdashboard\services\Service;

use verbb\base\LogTrait;
use verbb\base\helpers\Plugin;

trait PluginTrait
{
    // Properties
    // =========================================================================

    public static ?DefaultDashboard $plugin = null;


    // Traits
    // =========================================================================

    use LogTrait {
        error as private _baseError;
        info as private _baseInfo;
    }


    // Static Methods
    // =========================================================================

    public static function config(): array
    {
        Plugin::bootstrapPlugin('default-dashboard');

        return [
            'components' => [
                 'service' => Service::class,
            ],
        ];
    }

    public static function error(string $message, array $params = []): void
    {
        if (!self::$plugin?->getSettings()->logErrors) {
            return;
        }

        self::_baseError($message, $params);
    }

    public static function info(string $message, array $params = []): void
    {
        if (!self::$plugin?->getSettings()->logInfo) {
            return;
        }

        self::_baseInfo($message, $params);
    }


    // Public Methods
    // =========================================================================

    public function getService(): Service
    {
        return $this->get('service');
    }

}
