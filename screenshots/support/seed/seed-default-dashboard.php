/** Seed the source users and group-specific dashboard settings shown in the feature screenshot. */

use craft\elements\User;
use craft\enums\CmsEdition;
use craft\helpers\Json;
use craft\helpers\ProjectConfig as ProjectConfigHelper;
use craft\models\UserGroup;

Craft::$app->setEdition(CmsEdition::Pro);

$elements = Craft::$app->getElements();
$users = Craft::$app->getUsers();
$userGroups = Craft::$app->getUserGroups();

$createUser = static function(string $username, string $email, string $firstName, string $lastName) use ($elements): User {
    $user = User::find()->email($email)->status(null)->one();

    if ($user) {
        return $user;
    }

    $user = new User([
        'username' => $username,
        'email' => $email,
        'firstName' => $firstName,
        'lastName' => $lastName,
        'active' => true,
        'newPassword' => 'screenshot-password',
    ]);
    $user->setScenario(User::SCENARIO_REGISTRATION);

    if (!$elements->saveElement($user)) {
        throw new RuntimeException('Unable to save screenshot user: ' . Json::encode($user->getErrors()));
    }

    return $user;
};

$createGroup = static function(string $name, string $handle) use ($userGroups): UserGroup {
    $group = $userGroups->getGroupByHandle($handle);

    if ($group) {
        return $group;
    }

    $group = new UserGroup([
        'name' => $name,
        'handle' => $handle,
    ]);

    if (!$userGroups->saveGroup($group)) {
        throw new RuntimeException('Unable to save screenshot user group: ' . Json::encode($group->getErrors()));
    }

    return $group;
};

$admin = User::find()->admin(true)->status(null)->one();

if (!$admin) {
    throw new RuntimeException('Unable to find the shared screenshot administrator.');
}

$editorialLead = $createUser('editorial.lead', 'editorial@example.test', 'Alex', 'Editor');
$marketingLead = $createUser('marketing.lead', 'marketing@example.test', 'Morgan', 'Marketing');
$editorsGroup = $createGroup('Editors', 'editors');
$marketingGroup = $createGroup('Marketing', 'marketing');

$users->assignUserToGroups($editorialLead->id, [$editorsGroup->id]);
$users->assignUserToGroups($marketingLead->id, [$marketingGroup->id]);

$plugin = Craft::$app->getPlugins()->getPlugin('default-dashboard');

if (!$plugin) {
    throw new RuntimeException('Default Dashboard is not installed.');
}

$settingsSaved = Craft::$app->getPlugins()->savePluginSettings($plugin, [
    'userDashboard' => $admin->id,
    'userDashboardByGroup' => [
        'editors' => $editorialLead->id,
        'marketing' => $marketingLead->id,
    ],
    'override' => true,
    'excludeAdmin' => true,
]);

if (!$settingsSaved) {
    throw new RuntimeException('Unable to save Default Dashboard screenshot settings.');
}

$projectConfig = Craft::$app->getProjectConfig();
$projectConfig->saveModifiedConfigData();
$projectConfig->writeYamlFiles(true);

$savedSettings = ProjectConfigHelper::unpackAssociativeArrays(
    $projectConfig->get('plugins.default-dashboard.settings') ?? [],
);

if (
    (int)($savedSettings['userDashboard'] ?? 0) !== $admin->id ||
    (int)($savedSettings['userDashboardByGroup']['editors'] ?? 0) !== $editorialLead->id ||
    (int)($savedSettings['userDashboardByGroup']['marketing'] ?? 0) !== $marketingLead->id ||
    !($savedSettings['override'] ?? false) ||
    !($savedSettings['excludeAdmin'] ?? false)
) {
    throw new RuntimeException('Default Dashboard screenshot settings were not persisted: ' . Json::encode($savedSettings));
}

echo Json::encode([
    'users' => User::find()->status(null)->count(),
    'groups' => count($userGroups->getAllGroups()),
    'settingsSaved' => true,
], JSON_THROW_ON_ERROR);
