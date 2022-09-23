# Default Dashboard Plugin for Craft CMS

Default Dashboard is a Craft CMS plugin that makes it possible to setup default widgets for each of your users. Rather than setting up widgets for each user manually, or instructing your client - have them populated automatically.

This is achieved by setting the base user account from which to copy dashboard widgets from. Each time your users log into the control panel, Default Dashboard will populate their dashboard widgets. Default Dashboard is also smart enough to know when your widgets have changed, and to not update other users unless they have. This saves needless database updating. 

Importantly, you can allow your users to modify their widgets by turning off the `Override` function. With this turned on, all users' widgets will be replaced when thet login, so any of their widgets will be lost. Turning this off still provides users with your initial default widgets, but they're free to alter them to their needs.

<img src="https://github.com/verbb/default-dashboard/blob/craft-3/screenshots/settings.png" style="box-shadow: 0 4px 16px rgba(0,0,0,0.08); border-radius: 4px; border: 1px solid rgba(0,0,0,0.12);">

## Installation
You can install Default Dashboard via the plugin store, or through Composer.

### Craft Plugin Store
To install **Default Dashboard**, navigate to the _Plugin Store_ section of your Craft control panel, search for `Default Dashboard`, and click the _Try_ button.

### Composer
You can also add the package to your project using Composer and the command line.

1. Open your terminal and go to your Craft project:
```shell
cd /path/to/project
```

2. Then tell Composer to require the plugin, and Craft to install it:
```shell
composer require verbb/default-dashboard && php craft plugin/install default-dashboard
```

### Control Panel

Install the plugin, go to Settings > Default Dashboard. Select the user from which to mirror dashboard widget from (most commongly yourself), and whether you want to force your widgets to overwrite their own.

### Configuration File

For even more flexibility, make a config file as part of your regular Craft setup, and never have to worry about it again. Create a file named `default-dashboard.php` in your config folder for Craft. You'll have access to the following:

```
<?php

return [
    'userDashboard' => 1, // The User ID you want to mirror from
    'override' => true,
];

?>
```

### Credits
Based on plugins [One Dashboard](https://github.com/boboldehampsink/onedashboard), [Duplicate User Dashboard](https://github.com/james1238/duplicateuserdashboard).

## Show your Support

Default Dashboard is licensed under the MIT license, meaning it will always be free and open source – we love free stuff! If you'd like to show your support to the plugin regardless, [Sponsor](https://github.com/sponsors/verbb) development.

<h2></h2>

<a href="https://verbb.io" target="_blank">
  <img width="100" src="https://verbb.io/assets/img/verbb-pill.svg">
</a>

