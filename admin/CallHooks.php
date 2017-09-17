<?php

class CallHooks
{
    /**
     * Adds a menu for this plugin to the 'Tools' menu.
     */
    public function __construct()
    {
        $subMenuPage = new SubMenuPage();
        $subMenuPage->init();
    }

    public function init()
    {
        add_action(
            'admin_enqueue_scripts',
            array(
                $this,
                'mailServiceCustomAdminStyle'
            )
        );
        add_action(
            'wp_ajax_iContactIntegration',
            array(
                $this,
                'iContactIntegration'
            )
        );
        add_filter(
            'manage_users_columns',
            array(
                $this,
                'userImport'
            )
        );
        add_filter(
            'manage_users_custom_column',
            array(
                $this,
                'userImportColumn'
            ),
            10,
            3
        );
        add_action(
            'wp_ajax_openPopUpImportWizard',
            array(
                $this,
                'openPopUpImportWizard'
            )
        );
    }

    public function mailServiceCustomAdminStyle()
    {
        wp_enqueue_style(
            'custom-style-mail-service',
            MAIL_SERVICE_DIRECTORY_PLUGIN_URL . 'assets/css/custom-style.css'
        );
        wp_register_script(
            'custom-script-mail-service',
            MAIL_SERVICE_DIRECTORY_PLUGIN_URL . 'assets/scripts/customScript.js',
            array(),
            '1.0.0',
            true
        );
        // Localize the script with new data
        $translation_array = array(
            'URL' => admin_url( 'admin-ajax.php' ),
        );
        wp_localize_script( 'custom-script-mail-service', 'ADMIN_AJAX', $translation_array );
        // Enqueued script with localized data.
        wp_enqueue_script( 'custom-script-mail-service' );
    }

    public function iContactIntegration()
    {
        if ( empty( $_POST ) || !wp_verify_nonce( $_POST[ 'ankit-is-good-developer' ], 'iContactIntegration' ) ) {
            echo 'You targeted the right function, but sorry, your nonce did not verify.';
            die();
        } else {
            global $wpdb;
            $apiId       = sanitize_text_field( $_POST[ 'api_id' ] );
            $apiUsername = sanitize_text_field( $_POST[ 'api_username' ] );
            $apiPassword = sanitize_text_field( $_POST[ 'api_password' ] );
            $required    = explode( '&', $_SERVER[ 'HTTP_REFERER' ] );
            $required    = isset( $required[ 0 ] ) ? $required[ 0 ] : $_SERVER[ 'HTTP_REFERER' ];
            if ( !empty( $apiId ) && !empty( $apiUsername ) && !empty( $apiPassword ) ) {
                iContactApi::getInstance()
                           ->setConfig(
                               array(
                                   'appId'       => $apiId,
                                   'apiPassword' => $apiPassword,
                                   'apiUsername' => $apiUsername
                               )
                           );
                $oiContact = iContactApi::getInstance();
                try {
                    $oiContact->getContacts();
                    $validCredentials = true;
                } catch ( Exception $oException ) {
                    $validCredentials = false;
                }
                if ( $validCredentials ) {
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
                    $displayUrl = $required .
                                  '&errorMsg=The application was not recognized. Possible reasons are: the Api-AppId was entered incorrectly; the application is not registered for that user.';
                }
            } else {
                $displayUrl = $required . '&errorMsg=Please fill required details';
            }
            echo "<script type='text/javascript'>location.href = '" . $displayUrl . "';</script>";
            die( 0 );
        }
    }

    protected function getIContactDetailsOfCurrentUser()
    {
        global $wpdb;
        $table_name          = $wpdb->prefix . "ecti_i_contact_setting";
        $user_id             = get_current_user_id();
        $mailServiceIContact = $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM $table_name where user_id= %d", $user_id ),
            ARRAY_A
        );

        return $mailServiceIContact;
    }

    public function userImport( $column )
    {
        $isDetails = $this->getIContactDetailsOfCurrentUser();
        if ( $isDetails ) {
            $column[ 'userImport' ] = 'Import User';
        }

        return $column;
    }

    public function userImportColumn( $val, $column_name, $user_id )
    {
        if ( 'userImport' != $column_name ) {
            return $val;
        }
        $button = '<a href="javascript:void(0)"  data-userId="' . $user_id .
                  '"class="button button-primary button-large openPopUpListing">Import User</a>';

        return $button;
    }

    public function openPopUpImportWizard()
    {
        //print_r(intval($_GET['userID']);
        //echo 'ankit';
        echo '<div class="modal-content">
    <span class="close closeModalListing">&times;</span>
    <p>Some text in the Modal..</p>
    </div>';
        die( 0 );
    }

}
