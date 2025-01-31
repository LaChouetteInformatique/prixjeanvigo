<?php
/**
 * CONSTANTS
 */
define( 'DB_PLUGIN_VERSION', time());
define( 'DB_PLUGIN_TEXTDOMAIN', 'db-plugin');
define( 'DB_PLUGIN_DIR_PATH',  str_replace('/inc', '', plugin_dir_path( __FILE__ )));
define( 'DB_PLUGIN_DIR_URL',  str_replace('/inc', '', plugin_dir_url( __FILE__ )));