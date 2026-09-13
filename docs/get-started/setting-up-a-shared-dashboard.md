# Setting Up a Shared Dashboard

Default Dashboard copies widgets from a chosen user's dashboard when another user logs in. This gives a group of editors a useful starting point, such as recent entries and an introductory message, without configuring each account separately.

## Prepare the Source Dashboard

Choose an existing Craft user to supply the dashboard. Log in as that user and arrange the widgets you want other users to receive. The source account is excluded from copying its own dashboard back onto itself.

Open Default Dashboard's settings and select that user as the default dashboard source. The `userDashboard` configuration value identifies the source user by ID; it is not the ID of an individual widget. See [Configuration](docs:get-started/configuration) for the setting values.

## Decide Whether to Replace Existing Widgets

With `override` enabled, the plugin can replace a user's existing widgets on login when they differ from the source. Disable it if users should keep dashboards they have already arranged; users without widgets can still receive the default dashboard. Enable `excludeAdmin` if administrators should be left out.

For example, on a site where editors are free to customise their dashboards, start with `override` disabled. This makes the shared layout a starting point rather than something that replaces their changes at the next login.

## Check a Recipient Account

Use a separate editor account to test the result. Log out and log back in as that editor, then open the dashboard and compare it with the source account. Test an account with existing widgets as well as one without them so you can confirm the chosen override behaviour.

The plugin copies widget configuration. A widget's displayed content can still depend on the receiving user's permissions, so check the result as the editor rather than only as an administrator.
