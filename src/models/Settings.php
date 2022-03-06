<?php
namespace verbb\defaultdashboard\models;

use craft\base\Model;

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

}
