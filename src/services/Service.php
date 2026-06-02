<?php
namespace verbb\defaultdashboard\services;

use verbb\defaultdashboard\DefaultDashboard;
use verbb\defaultdashboard\models\Settings;

use Craft;
use craft\base\Component;
use craft\events\UserEvent as CraftUserEvent;
use craft\helpers\Db;
use craft\helpers\Json;
use craft\records\Widget as WidgetRecord;

use yii\web\UserEvent as WebUserEvent;

use Throwable;

class Service extends Component
{
    // Properties
    // =========================================================================

    private array $_activatedUserIds = [];


    // Public Methods
    // =========================================================================

    public function afterUserActivation(CraftUserEvent $event): void
    {
        if ($event->user?->id) {
            $this->_activatedUserIds[$event->user->id] = true;
        }
    }

    public function afterUserLogin(WebUserEvent $event): void
    {
        /* @var Settings $settings */
        $settings = DefaultDashboard::$plugin->getSettings();

        if (!$this->_shouldSetDashboardOnLogin($event)) {
            return;
        }

        $currentUser = $event->identity;
        $defaultUserId = $settings->getUserDashboardIdForUser($currentUser);
        $defaultUser = $settings->getUserDashboardUserForUser($currentUser);
        $isAdmin = Craft::$app->getUser()->getIsAdmin();

        if (!$defaultUser) {
            DefaultDashboard::error("Default User not found for ID: {$defaultUserId}");
            return;
        }

        // Proceed if we've got a setting for the default user, and it's not that user logging in
        if ($currentUser->id == $defaultUser->id) {
            DefaultDashboard::info("Skip setting dashboard - this is the default user");
            return;
        }

        $currentUserWidgets = $this->_getUserWidgets($currentUser->id);
        $defaultUserWidgets = $this->_getUserWidgets($defaultUser->id);

        DefaultDashboard::info("Current User ID: {$currentUser->id}");
        DefaultDashboard::info("Default User ID: {$defaultUser->id}");
        DefaultDashboard::info("Current User Widget: " . Json::encode($this->_widgets($currentUserWidgets)));
        DefaultDashboard::info("Default User Widget: " . Json::encode($this->_widgets($defaultUserWidgets)));

        // If this user has no widgets, create them and finish - or, if we're forcing override
        // If this user is an Admin, and excludeAdmin set to true, not override
        if ((!$currentUserWidgets || $settings->override) && (!$settings->excludeAdmin || !$isAdmin)) {
            // To prevent massive re-creating of widgets each login, check if default vs current is different
            if ($this->_compareWidgets($currentUserWidgets, $defaultUserWidgets)) {
                DefaultDashboard::info("Users widgets are the same");
                return;
            }

            // Remove any existing widgets for the user
            $this->_deleteUserWidgets($currentUser->id);

            // Update the logged-in users widgets
            $this->_setUserWidgets($currentUser, $defaultUserWidgets);

            // Update the user with their dashboard-set flag so the default widgets aren't added
            Db::update('{{%users}}', ['hasDashboard' => true], ['id' => $currentUser->id]);
        }
    }


    // Private Methods
    // =========================================================================

    private function _deleteUserWidgets($userId): void
    {
        Db::delete('{{%widgets}}', ['userId' => $userId]);
    }

    private function _getUserWidgets($userId): array
    {
        return WidgetRecord::find()
            ->where(['userId' => $userId])
            ->orderBy(['sortOrder' => SORT_ASC])
            ->all();
    }

    private function _compareWidgets(array $currentUserWidgets, array $defaultUserWidgets): bool
    {
        $areSame = true;

        if (count($currentUserWidgets) != count($defaultUserWidgets)) {
            DefaultDashboard::info("Current User Widgets Count: " . count($currentUserWidgets));
            DefaultDashboard::info("Default User Widgets Count: " . count($defaultUserWidgets));

            return false;
        }

        foreach ($currentUserWidgets as $i => $currentUserWidget) {
            $defaultUserWidget = $defaultUserWidgets[$i];

            // Strip off any correctly unique data
            $array1 = [
                'type' => $currentUserWidget['type'],
                'sortOrder' => $currentUserWidget['sortOrder'],
                'colspan' => $currentUserWidget['colspan'],
                'settings' => Json::encode($currentUserWidget['settings']),
            ];

            $array2 = [
                'type' => $defaultUserWidget['type'],
                'sortOrder' => $defaultUserWidget['sortOrder'],
                'colspan' => $defaultUserWidget['colspan'],
                'settings' => Json::encode($defaultUserWidget['settings']),
            ];

            DefaultDashboard::info("Current Widgets: " . Json::encode($array1));
            DefaultDashboard::info("Default Widgets: " . Json::encode($array2));

            if (array_diff($array1, $array2)) {
                $areSame = false;

                break;
            }
        }

        return $areSame;
    }

    private function _shouldSetDashboardOnLogin(WebUserEvent $event): bool
    {
        if (Craft::$app->getRequest()->getIsCpRequest()) {
            return true;
        }

        $userId = $event->identity?->id;
        $autoLoginAfterActivation = Craft::$app->getConfig()->getGeneral()->autoLoginAfterAccountActivation;

        if ($userId && $autoLoginAfterActivation && isset($this->_activatedUserIds[$userId])) {
            DefaultDashboard::info("Setting dashboard after account activation auto-login");
            return true;
        }

        DefaultDashboard::info("Not a CP request");

        return false;
    }

    private function _setUserWidgets($user, $widgets): void
    {
        $transaction = Craft::$app->getDb()->beginTransaction();
        try {
            foreach ($widgets as $widgetRecord) {
                $widget = new WidgetRecord();
                $widget->userId = $user->id;
                $widget->type = $widgetRecord->type;
                $widget->sortOrder = $widgetRecord->sortOrder;
                $widget->colspan = $widgetRecord->colspan;
                $widget->settings = $widgetRecord->settings;

                $widget->save(false);
            }

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();

            throw $e;
        }
    }

    private function _widgets($widgets): array
    {
        $array = [];

        foreach ($widgets as $widget) {
            $array[] = $widget->toArray();
        }

        return $array;
    }
}