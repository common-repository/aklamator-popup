<?php
/*
Plugin Name: Aklamator PopUp
Plugin URI: https://www.aklamator.com/wordpress
Description: PopUp plugin by Aklamator enables you to show widget with your latest content in order to retain visitors, engage you visitors do not them leave and miss your content, reduce bounce rate.
Version: 2.3
Author: Aklamator
Author URI: https://www.aklamator.com/
License: GPL2

Copyright 2016 Aklamator.com (email : info@aklamator.com)

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

*/


if(!defined('POP_AKLA_PLUGIN_NAME')){
    define('POP_AKLA_PLUGIN_NAME', plugin_basename(__FILE__));
}

if (!defined('POP_AKLA_PLUGIN_DIR')) {
    define('POP_AKLA_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

if (!defined('POP_AKLA_PLUGIN_URL')) {
    define('POP_AKLA_PLUGIN_URL', plugin_dir_url(__FILE__));
}


require_once POP_AKLA_PLUGIN_DIR . 'includes/class-aklamator-popup.php';


/*
 * Activation Hook
 */
register_activation_hook( __FILE__, array('aklamatorPopWidget','set_up_options_popup'));
/*
 * Uninstall Hook
 */
register_uninstall_hook(__FILE__, array('aklamatorPopWidget','aklamatorPop_uninstall'));


// Widget section

require_once POP_AKLA_PLUGIN_DIR . 'includes/class-widget-aklamator-popup.php';

//start the plugin
aklamatorPopWidget::init();

