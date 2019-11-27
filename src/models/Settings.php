<?php
namespace verbb\defaultdashboard\models;

use craft\base\Model;

class Settings extends Model
{
    // Public Properties
    // =========================================================================

    public $userDashboard = 1;
    public $override = true;
    public $excludeAdmin = false;

    // Logging
    public $logInfo = false;
    public $logErrors = false;

}
