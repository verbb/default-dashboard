<?php
namespace verbb\defaultdashboard\services;

use verbb\defaultdashboard\DefaultDashboard;
use verbb\defaultdashboard\models\Settings;

use Craft;
use craft\base\Component;
use craft\helpers\Json;
use craft\records\Widget as WidgetRecord;

use yii\web\UserEvent;

use Throwable;

class Service extends Component
{
    // Public Methods
    // =========================================================================

    public function afterUserLogin(UserEvent $event): void
    {
        /* @var Settings $settings */
        $settings = DefaultDashboard::$plugin->getSettings();

        // For the moment, only check on CP requests
        if (!Craft::$app->getRequest()->getIsCpRequest()) {
            DefaultDashboard::log("Not a CP request");
            return;
        }

        $currentUser = $event->identity;
        $defaultUser = Craft::$app->getUsers()->getUserById($settings->userDashboard);
        $isAdmin = Craft::$app->getUser()->getIsAdmin();

        if (!$defaultUser) {
            DefaultDashboard::error("Default User not found for ID: {$settings->userDashboard}");
            return;
        }

        // Proceed if we've got a setting for the default user, and it's not that user logging in
        if ($currentUser->id == $defaultUser->id) {
            DefaultDashboard::log("Skip setting dashboard - this is the default user");
            return;
        }

        $currentUserWidgets = $this->_getUserWidgets($currentUser->id);
        $defaultUserWidgets = $this->_getUserWidgets($defaultUser->id);

        DefaultDashboard::log("Current User ID: {$currentUser->id}");
        DefaultDashboard::log("Default User ID: {$defaultUser->id}");
        DefaultDashboard::log("Current User Widget: " . Json::encode($this->_widgets($currentUserWidgets)));
        DefaultDashboard::log("Default User Widget: " . Json::encode($this->_widgets($defaultUserWidgets)));

        // If this user has no widgets, create them and finish - or, if we're forcing override
        // If this user is an Admin, and excludeAdmin set to true, not override
        if ((!$currentUserWidgets || $settings->override) && (!$settings->excludeAdmin || !$isAdmin)) {
            // To prevent massive re-creating of widgets each login, check if default vs current is different
            if ($this->_compareWidgets($currentUserWidgets, $defaultUserWidgets)) {
                DefaultDashboard::log("Users widgets are the same");
                return;
            }

            // Remove any existing widgets for the user
            $this->_deleteUserWidgets($currentUser->id);

            // Update the logged-in users widgets
            $this->_setUserWidgets($currentUser, $defaultUserWidgets);

            // Update the user with their dashboard-set flag so the default widgets aren't added
            Craft::$app->getDb()->createCommand()
                ->update('{{%users}}', ['hasDashboard' => true], ['id' => $currentUser->id])
                ->execute();
        }
    }


    // Private Methods
    // =========================================================================

    private function _deleteUserWidgets($userId): void
    {
        Craft::$app->getDb()->createCommand()
            ->delete('{{%widgets}}', ['userId' => $userId])
            ->execute();
    }

    private function _getUserWidgets($userId): array
    {
        return WidgetRecord::find()
            ->where(['userId' => $userId])
            ->all();
    }

    private function _compareWidgets(array $currentUserWidgets, array $defaultUserWidgets): bool
    {
        $areSame = true;

        if (count($currentUserWidgets) != count($defaultUserWidgets)) {
            DefaultDashboard::log("Current User Widgets Count: " . count($currentUserWidgets));
            DefaultDashboard::log("Default User Widgets Count: " . count($defaultUserWidgets));

            return false;
        }

        foreach ($currentUserWidgets as $currentUserWidget) {
            $currentUserWidget = $currentUserWidget->toArray();
            $defaultUserWidget = $currentUserWidget->toArray();

            // Strip off any correctly unique data
            $array1 = [
                'type' => $currentUserWidget['type'],
                'sortOrder' => $currentUserWidget['sortOrder'],
                'colspan' => $currentUserWidget['colspan'],
                'settings' => $currentUserWidget['settings'],
            ];

            $array2 = [
                'type' => $defaultUserWidget['type'],
                'sortOrder' => $defaultUserWidget['sortOrder'],
                'colspan' => $defaultUserWidget['colspan'],
                'settings' => $defaultUserWidget['settings'],
            ];

            DefaultDashboard::log("Current Widgets: " . Json::encode($array1));
            DefaultDashboard::log("Default Widgets: " . Json::encode($array2));

            if (array_diff($array1, $array2)) {
                $areSame = false;

                break;
            }
        }

        return $areSame;
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