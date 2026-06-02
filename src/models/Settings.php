<?php
namespace verbb\defaultdashboard\models;

use Craft;
use craft\base\Model;
use craft\elements\User;

class Settings extends Model
{
    // Properties
    // =========================================================================

    public bool $excludeAdmin = false;
    public bool $logErrors = false;
    public bool $logInfo = false;

    // Logging
    public bool $override = true;
    public int $userDashboard = 1;


    // Public Methods
    // =========================================================================

    public function rules(): array
    {
        return [
            [['excludeAdmin', 'logErrors', 'logInfo', 'override'], 'boolean'],
            [['userDashboard'], 'required'],
            [['userDashboard'], 'integer'],
            [['userDashboard'], 'validateUserDashboard'],
        ];
    }

    public function validateUserDashboard(string $attribute, mixed $params = null): void
    {
        if (!$this->getUserDashboardUser()) {
            $this->addError($attribute, Craft::t('default-dashboard', 'Default User Dashboard must reference an existing user.'));
        }
    }

    public function getUserDashboardUser(): ?User
    {
        return Craft::$app->getUsers()->getUserById($this->userDashboard);
    }

}
