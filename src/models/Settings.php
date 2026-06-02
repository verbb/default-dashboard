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
    public array $userDashboardByGroup = [];


    // Public Methods
    // =========================================================================

    public function rules(): array
    {
        return [
            [['excludeAdmin', 'logErrors', 'logInfo', 'override'], 'boolean'],
            [['userDashboard'], 'required'],
            [['userDashboard'], 'integer'],
            [['userDashboard'], 'validateUserDashboard'],
            [['userDashboardByGroup'], 'validateUserDashboardByGroup'],
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

    public function validateUserDashboardByGroup(string $attribute, mixed $params = null): void
    {
        foreach ($this->getUserDashboardByGroup() as $groupHandle => $userId) {
            $group = Craft::$app->getUserGroups()->getGroupByHandle($groupHandle);

            if (!$group) {
                $this->addError($attribute, Craft::t('default-dashboard', 'Default User Dashboard group "{group}" does not exist.', [
                    'group' => $groupHandle,
                ]));

                continue;
            }

            if (!$this->getUserDashboardUserById($userId)) {
                $this->addError($attribute, Craft::t('default-dashboard', 'Default User Dashboard for "{group}" must reference an existing user.', [
                    'group' => $group->name,
                ]));
            }
        }
    }

    public function getUserDashboardByGroup(): array
    {
        $dashboards = [];

        foreach ($this->userDashboardByGroup as $groupHandle => $userId) {
            if ($userId === null || $userId === '') {
                continue;
            }

            $dashboards[(string)$groupHandle] = (int)$userId;
        }

        return $dashboards;
    }

    public function getUserDashboardIdForUser(User $user): int
    {
        $userGroupHandles = array_map(fn($group) => $group->handle, $user->getGroups());

        foreach ($this->getUserDashboardByGroup() as $groupHandle => $userId) {
            if (in_array($groupHandle, $userGroupHandles, true)) {
                return $userId;
            }
        }

        return $this->userDashboard;
    }

    public function getUserDashboardUserForUser(User $user): ?User
    {
        return $this->getUserDashboardUserById($this->getUserDashboardIdForUser($user));
    }

    public function getUserDashboardUserById(int $userId): ?User
    {
        return Craft::$app->getUsers()->getUserById($userId);
    }

}
