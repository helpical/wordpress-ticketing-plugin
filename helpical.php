<?php

/**
 * Plugin Name: Ticketing Helpical
 * Plugin URI: https://helpical.ir
 * Description: Helpical Ticketing System for Wordpress
 * Version: 1.0.0
 * Author: ParniaGroup
 * Author URI: https://www.parniagroup.com
 */

defined('ABSPATH') or die();

define('HELPICAL_DIR', plugin_dir_path(__FILE__));
define('HELPICAL_BASENAME', plugin_basename(__FILE__));
define('HELPICAL_FILE', __FILE__);

if (!function_exists('jdate')){
    require_once(HELPICAL_DIR . 'inc/jdf.php');
}
require_once(HELPICAL_DIR . 'inc/functions.php');
require_once(HELPICAL_DIR . 'inc/admin/admin.php');
require_once(HELPICAL_DIR . 'inc/restApi/ticket.php');
