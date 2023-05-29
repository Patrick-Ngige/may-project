<?php

/**
 * @package pms
 */

/*
Plugin Name: pms
Plugin URI: http://...
Description: A ticket management system for project managers and employees
Version: 1.0.0
Author: Hope ,Nicholas,Patrick
Author URI: http://...
License: GPLv2 or Later
Text Domain: pms plugin
*/

//Security Check 

defined('ABSPATH') or die("Caught you hacker");

//Require once the Composer Autoload
if(file_exists(dirname(__FILE__).'/vendor/autoload.php')){
    require_once dirname(__FILE__).'/vendor/autoload.php';
}

use Inc\Base;
function activate_pms_plugin(){
    Base\Activate::activate();
}
register_activation_hook(__FILE__, 'activate_pms_plugin');

function deactivate_pms_plugin(){
    Base\Deactivate::deactivate();
}
register_deactivation_hook(__FILE__, 'deactivate_pms_plugin');