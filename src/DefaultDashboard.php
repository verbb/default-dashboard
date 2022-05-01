<?php
namespace verbb\defaultdashboard;

use verbb\defaultdashboard\base\PluginTrait;
use verbb\defaultdashboard\models\Settings;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\events\RegisterUrlRulesEvent;
use craft\helpers\UrlHelper;
use craft\web\UrlManager;

use yii\base\Event;
use yii\web\User;

class DefaultDashboard extends Plugin
{
    // Properties
    // =========================================================================

    public bool $hasCpSettings = true;
    public string $schemaVersion = '1.0.0';


    // Traits
    // =========================================================================

    use PluginTrait;


    // Public Methods
    // =========================================================================

    public function init(): void
    {
        parent::init();

        self::$plugin = $this;

        $this->_registerComponents();
        $this->_registerLogTarget();
        $this->_registerCpRoutes();
        $this->_registerEventHandlers();
    }

    public function getSettingsResponse(): mixed
    {
        return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('default-dashboard/settings'));
    }


    // Protected Methods
    // =========================================================================

    protected function createSettingsModel(): ?Model
    {
        return new Settings();
    }


    // Private Methods
    // =========================================================================

    private function _registerCpRoutes(): void
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules = array_merge($event->rules, [
                'default-dashboard/settings' => 'default-dashboard/base/settings',
            ]);
        });
    }

    private function _registerEventHandlers(): void
    {
        Event::on(User::class, User::EVENT_AFTER_LOGIN, [$this->getService(), 'afterUserLogin']);
    }
}
