# Default Dashboard Plugin for Craft CMS

<img src="https://github.com/verbb/default-dashboard/blob/master/screenshots/settings.png">

Default Dashboard is a Craft CMS plugin that makes it possible to setup default widgets for each of your users. Rather than setting up widgets for each user manually, or instructing your client - have them populated automatically.

This is achieved by setting the base user account from which to copy dashboard widgets from. Each time your users log into the control panel, Default Dashboard will populate their dashboard widgets. Default Dashboard is also smart enough to know when your widgets have changed, and to not update other users unless they have. This saves needless database updating. 

Importantly, you can allow your users to modify their widgets by turning off the `Override` function. With this turned on, all users' widgets will be replaced when thet login, so any of their widgets will be lost. Turning this off still provides users with your initial default widgets, but they're free to alter them to their needs.

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

<h2></h2>

<a href="https://verbb.io" target="_blank">
  <img width="100" src="https://verbb.io/assets/img/verbb-pill.svg">
</a>

