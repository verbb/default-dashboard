# Configuration
Create a `default-dashboard.php` file under your `/config` directory with the following options available to you. You can also use multi-environment options to change these per environment.

The below shows the defaults already used by Default Dashboard, so you don't need to add these options unless you want to modify the values.

```php
<?php

return [
    '*' => [
        'userDashboard' => 1,
        'override' => true,
        'excludeAdmin' => false,
    ],
];
```

## Configuration options
- `userDashboard` - Set the user ID to use as the default dashboard, mirrored to all other users.
- `override` - Whether to force the default dashboard, overwriting any user-specific ones. This will occur on each login.
- `excludeAdmin` - Whether to exclude the admin user when force overwrite is enabled. This will occur on each login.

## Control Panel
You can also manage configuration settings through the Control Panel by visiting Settings → Default Dashboard.
