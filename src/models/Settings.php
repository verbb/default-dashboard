<?php
namespace verbb\defaultdashboard\models;

use craft\base\Model;

class Settings extends Model
{
    // Public Properties
    // =========================================================================

    public int $userDashboard = 1;
    public bool $override = true;
    public bool $excludeAdmin = false;

    // Logging
    public bool $logInfo = false;
    public bool $logErrors = false;

}
