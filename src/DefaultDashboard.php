<?php
namespace verbb\defaultdashboard;

use Craft;
use craft\base\Plugin;
use craft\events\RegisterUrlRulesEvent;
use craft\helpers\UrlHelper;
use craft\web\UrlManager;

use verbb\defaultdashboard\models\Settings;
use verbb\defaultdashboard\services\Service;

use yii\base\Event;
use yii\web\User;

class DefaultDashboard extends Plugin
{
    // Static Properties
    // =========================================================================

    public static $plugin;


    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();

        self::$plugin = $this;

        // Register Components (Services)
        $this->setComponents([
            'service' => Service::class,
        ]);

        // Register our CP routes
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, [$this, 'registerCpUrlRules']);

        Event::on(User::class, User::EVENT_AFTER_LOGIN, [$this->service, 'afterUserLogin']);
    }

    public function registerCpUrlRules(RegisterUrlRulesEvent $event)
    {
        $rules = [
            'default-dashboard/settings' => 'default-dashboard/base/settings',
        ];

        $event->rules = array_merge($event->rules, $rules);
    }

    public function getSettingsResponse()
    {
        Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('default-dashboard/settings'));
    }


    // Protected Methods
    // =========================================================================

    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }
}
