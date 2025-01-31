<?php
/**
 * Plugin Name: DB Plugin
 * Description: Collection of fuctionnalities for Websites
 * Version: 1.0.0
 * Author: Damien BECHERINI
 * Author URI: https://damien.becherini.fr
 * Text Domain: db-plugin
 * Domain Path: /languages
 * @package DB_Plugin
 * 
 * Mobile Menu with animate Hamburger icon
 * ---------------------------------------
 * See Readme.md for instructions
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 */

require_once plugin_dir_path( __FILE__ ) . '/inc/constants.php';
require_once plugin_dir_path( __FILE__ ) . '/inc/helpers/autoloader.php';

function db_plugin_get_instance()
{
	\DB_PLUGIN\Inc\Main::getInstance();
}
db_plugin_get_instance();

