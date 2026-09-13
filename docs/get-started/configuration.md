# Configuration

You can customise Default Dashboard’s settings using a PHP configuration file. This is optional: each setting has a default, so you only need to include the values you want to change.

To override a setting, create `default-dashboard.php` in your Craft project’s `/config` directory and return an array of setting names and values. For example, the following will exclude administrators from dashboard overrides:

```php
<?php

return [
    'excludeAdmin' => true,
];
```

All other settings keep their defaults. Add any further settings you want to change to the same array. The options below explain the available settings and their defaults.

## Configuration Options

::: reference
### `userDashboard`

**Type:** `int` · **Default:** `1`

Set the user ID to use as the default dashboard, mirrored to all other users.
:::

::: reference
### `override`

**Type:** `bool` · **Default:** `true`

Whether to force the default dashboard, overwriting any user-specific ones. This will occur on each login.
:::

::: reference
### `excludeAdmin`

**Type:** `bool` · **Default:** `false`

Whether to exclude the admin user when force overwrite is enabled. This will occur on each login.
:::


## Control Panel
You can also manage configuration settings through the Control Panel by visiting Settings → Default Dashboard.
