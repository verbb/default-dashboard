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
            return;
        }

        $currentUser = $event->identity;
        $defaultUser = Craft::$app->users->getUserById($settings->userDashboard);

        // Proceed if we've got a setting for the default user, and its not that user logging in
        if (!$defaultUser || $currentUser->id == $defaultUser->id) {
            return;
        }

        $currentUserWidgets = $this->_getUserWidgets($currentUser->id);
        $defaultUserWidgets = $this->_getUserWidgets($defaultUser->id);

        // If this user has no widgets, create them and finish - or, if we're forcing override
        if (!$currentUserWidgets || $settings->override) {
            // To prevent massive re-creating of widgets each login, check if default vs current is different
            if ($this->_compareWidgets($currentUserWidgets, $defaultUserWidgets)) {
                return;
            }

            // Remove any existing widgets for the user
            $this->_deleteUserWidgets($currentUser->id);

            // Update the logged in users widgets
            $this->_setUserWidgets($currentUser, $defaultUserWidgets);
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
}