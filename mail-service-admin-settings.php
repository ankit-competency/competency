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
define( 'MAIL_SERVICE_DIRECTORY_VERSION', '3.0' );
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
    $plugin = new SubMenuPage();
    $plugin->init();
    $importDefaultTables = new importDefaultTables();
    mailServiceCustomAdminStyle();
    register_activation_hook( __FILE__, $importDefaultTables->createPluginDatabaseTable() );
}

function mailServiceCustomAdminStyle()
{
    wp_enqueue_style( 'custom-style-mail-service', MAIL_SERVICE_DIRECTORY_PLUGIN_URL . 'assets/css/custom-style.css' );
}

add_action( 'wp_ajax_iContactIntegration', 'iContactIntegration' );
function iContactIntegration()
{
    if ( empty( $_POST ) || !wp_verify_nonce( $_POST[ 'ankit-is-good-developer' ], 'iContactIntegration' ) ) {
        echo 'You targeted the right function, but sorry, your nonce did not verify.';
        die();
    } else {
        global $wpdb;
        //        echo '<pre>';
        //        print_r($_POST);
        //        die;
        $apiId       = sanitize_text_field( $_POST[ 'api_id' ] );
        $apiUsername = sanitize_text_field( $_POST[ 'api_username' ] );
        $apiPassword = sanitize_text_field( $_POST[ 'api_password' ] );
        $required    = explode( '&', $_SERVER[ 'HTTP_REFERER' ] );
        $required    = isset( $required[ 0 ] ) ? $required[ 0 ] : $_SERVER[ 'HTTP_REFERER' ];
        //        var_dump(!empty( $apiId ) && !empty( $apiUsername ) && !empty( $apiPassword ) );
        //        die;
        if ( !empty( $apiId ) && !empty( $apiUsername ) && !empty( $apiPassword ) ) {
            //            die;
            $userId          = sanitize_text_field( $_POST[ 'user_id' ] );
            $id              = sanitize_text_field( $_POST[ 'id' ] );
            $paramsValues    = [
                'app_id'       => $apiId,
                'api_username' => $apiUsername,
                'api_password' => $apiPassword,
                'user_id'      => empty( $userId ) ? get_current_user_id() : $userId,
                'status'       => 1,
            ];
            $paramsDataTypes = [
                '%s',
                '%s',
                '%s',
                '%d',
                '%d'
            ];
            $table_name      = $wpdb->prefix . "ecti_i_contact_setting";
            if ( !empty( $id ) ) {
                $wpdb->update(
                    $table_name,
                    $paramsValues,
                    array( 'id' => $id ),
                    $paramsDataTypes,
                    array( '%d' )
                );
                $act = 'Updated';
            } else {
                $wpdb->insert(
                    $table_name,
                    $paramsValues,
                    $paramsDataTypes
                );
                $act = 'Added';
            }
            $displayUrl = $required . "&addMsg=$act Successfully";
        } else {
            $displayUrl = $required . '&errorMsg=Please fill required details';
        }
        echo "<script type='text/javascript'>location.href = '" . $displayUrl . "';</script>";
        die( 0 );
    }
}
