<?php
namespace verbb\defaultdashboard\services;

use Craft;
use craft\base\Component;
use craft\records\Widget as WidgetRecord;

use verbb\defaultdashboard\DefaultDashboard;

use yii\web\UserEvent;

class Service extends Component
{
    // Public Methods
    // =========================================================================

    public function afterUserLogin(UserEvent $event)
    {
        $settings = DefaultDashboard::$plugin->getSettings();

        // For the moment, only check on CP requests
        if (!Craft::$app->getRequest()->isCpRequest) {
            DefaultDashboard::log("Not a CP request");
            return;
        }

        $currentUser = $event->identity;
        $defaultUser = Craft::$app->users->getUserById($settings->userDashboard);
        $isAdmin = Craft::$app->user->getIsAdmin();

        if (!$defaultUser) {
            DefaultDashboard::error("Default User not found for ID: $settings->userDashboard");
            return;
        }

        // Proceed if we've got a setting for the default user, and its not that user logging in
        if ($currentUser->id == $defaultUser->id) {
            DefaultDashboard::log("Skip setting dashboard - this is the default user");
            return;
        }

        $currentUserWidgets = $this->_getUserWidgets($currentUser->id);
        $defaultUserWidgets = $this->_getUserWidgets($defaultUser->id);

        DefaultDashboard::log("Current User ID: " . $currentUser->id);
        DefaultDashboard::log("Default User ID: " . $defaultUser->id);
        DefaultDashboard::log("Current User Widget: " . json_encode($this->_widgets($currentUserWidgets)));
        DefaultDashboard::log("Default User Widget: " . json_encode($this->_widgets($defaultUserWidgets)));

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

            // Update the logged in users widgets
            $this->_setUserWidgets($currentUser, $defaultUserWidgets);

            // Update the user with their dashboard-set flag so the default widgets aren't added
            Craft::$app->getDb()->createCommand()
                ->update('{{%users}}', ['hasDashboard' => true], ['id' => $currentUser->id])
                ->execute();
        }
    }


    // Protected Methods
    // =========================================================================

    private function _deleteUserWidgets($userId)
    {
        Craft::$app->getDb()->createCommand()
            ->delete('{{%widgets}}', ['userId' => $userId])
            ->execute();
    }

    public function _getUserWidgets($userId)
    {
        return WidgetRecord::find()
            ->where(['userId' => $userId])
            ->all();
    }

    public function _compareWidgets($currentUserWidgets, $defaultUserWidgets)
    {
        $areSame = true;

        if (count($currentUserWidgets) != count($defaultUserWidgets)) {
            DefaultDashboard::log("Current User Widgets Count: " . count($currentUserWidgets));
            DefaultDashboard::log("Default User Widgets Count: " . count($defaultUserWidgets));

            return false;
        }

        for ($i = 0; $i < count($currentUserWidgets); $i++) { 
            $currentUserWidget = $currentUserWidgets[$i]->toArray();
            $defaultUserWidget = $defaultUserWidgets[$i]->toArray();

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

            DefaultDashboard::log("Current Widgets: " . json_encode($array1));
            DefaultDashboard::log("Default Widgets: " . json_encode($array2));

            if (array_diff($array1, $array2)) {
                $areSame = false;

                break;
            }
        }

        return $areSame;
    }

    private function _setUserWidgets($user, $widgets)
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
        } catch (\Throwable $e) {
            $transaction->rollBack();

            throw $e;
        }
    }

    private function _widgets($widgets)
    {
        $array = [];

        foreach ($widgets as $widget) {
            $array[] = $widget->toArray();
        }

        return $array;
    }
}