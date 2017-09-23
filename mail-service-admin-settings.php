<?php
/**
 * Plugin Name: Export Contacts to IContact
 * Plugin URI:
 * Description: A plugins to Connect IContact Account So that Wordpress Admin can Import user to IContact Dashboard.
 * Version: 1.0
 * Author: Rahul Gupta, Ankit Gupta
 * Author URI:
 * License: GPL2
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}
define( 'MAIL_SERVICE_DIRECTORY_VERSION', '1.0' );
define( 'MAIL_SERVICE_DIRECTORY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MAIL_SERVICE_DIRECTORY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
// Include the dependencies needed to instantiate the plugin.
foreach ( glob( plugin_dir_path( __FILE__ ) . 'admin/*.php' ) as $file ) {
    include_once $file;
}
add_action( 'plugins_loaded', 'mailServiceCustomAdminSettings' );
/**
 * Starts the plugin.
 *
 * @since 1.0.0
 */
function mailServiceCustomAdminSettings()
{
    
    $hooks= new CallHooks();
    $hooks->init();
    $importDefaultTables = new importDefaultTables();
    register_activation_hook( __FILE__, $importDefaultTables->createPluginDatabaseTable() );
}


