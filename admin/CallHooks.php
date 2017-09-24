<?php

class CallHooks
{
    const SUCCESS = 'success';
    const ERROR   = 'error';

    /**
     * Call all required features to WordPress Admin
     */
    public function __construct()
    {
        $subMenuPage = new SubMenuPage();
        $subMenuPage->init();
    }
    /**
     * Add All required hooks on initiate of class
    */
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
            'wp_ajax_saveIContactIntegration',
            array(
                $this,
                'saveIContactIntegration'
            )
        );
        add_action(
            'wp_ajax_bulkImportUsers',
            array(
                $this,
                'bulkImportUsers'
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
        add_action(
            'wp_ajax_triggerIContactImport',
            array(
                $this,
                'triggerIContactImport'
            )
        );
    }


    /**
     * Using for Bulk Import users to IContact
    */
    public function bulkImportUsers()
    {
        if ( empty( $_POST ) || !wp_verify_nonce( $_POST[ 'ANKIT_GUPTA_RAHUL_GUPTA' ], 'bulkImportUsers' ) ) {
            echo 'You targeted the right function, but sorry, your nonce did not verify.';
            die();
        } else {
            $required        = explode( '&', $_SERVER[ 'HTTP_REFERER' ] );
            $required        = isset( $required[ 0 ] ) ? $required[ 0 ] : $_SERVER[ 'HTTP_REFERER' ];
            $listsID         = sanitize_text_field( $_POST[ 'i_contact_list' ] );
            $blogUsers       = get_users();
            $importUsersData = [ ];
            $userCount       = 1;
            foreach ( $blogUsers as $user ) {
                if ( get_current_user_id() != $user->ID ) {
                    $importUsersData[] = [
                        'email'      => $user->user_email,
                        'first_name' => $user->first_name,
                        'last_name'  => $user->last_name
                    ];
                    $userCount++;
                }
            }
            $result = $this->importUsersToIContact( $importUsersData, $listsID );
            if ( $result[ 'status' ] == self::SUCCESS ) {
                $displayUrl = $required . "&addMsg=" . $userCount .
                              " users imported successfully to IContact. It may take to show on IContact Dashboard";
            } else {
                $displayUrl = $required . '&errorMsg=Nothing have to import in IContact';
            }
            echo "<script type='text/javascript'>location.href = '" . $displayUrl . "';</script>";
            die( 0 );
        }
    }


    /**
     * Single user Import to IContact
     * @param $importUsersData
     * @param $listID
     *
     * @return array
     */
    public function importUsersToIContact( $importUsersData, $listID )
    {
        $iContactApi = $this->initiateIContactObject();
        $counter     = 0;
        $requestStr  = "[email],[fname],[lname]\n";
        foreach ( $importUsersData as $importUser ) {
            $requestStr .= sprintf(
                               "%s,%s,%s",
                               $importUser[ 'email' ],
                               $importUser[ 'first_name' ],
                               $importUser[ 'last_name' ]
                           ) . "\n";
            $counter++;
        }
        if ( $counter > 0 ) {
            $iContactApi->uploadData( $requestStr, $listID );

            return [
                'status' => self::SUCCESS,
                'msg'    => 'Successfully user imported to IContact'
            ];
        } else {
            return [
                'status' => self::ERROR,
                'msg'    => 'Nothing have to import in IContact'
            ];
        }
    }

    /**
     * Initiate IContact Object with call APIs
    */
    public function initiateIContactObject()
    {
        global $wpdb;
        $table_name          = $wpdb->prefix . "ecti_i_contact_setting";
        $current_user_id     = get_current_user_id();
        $mailServiceIContact = $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM $table_name where user_id= %d", $current_user_id ),
            ARRAY_A
        );
        iContactApi::getInstance()
                   ->setConfig(
                       array(
                           'appId'       => $mailServiceIContact[ 'app_id' ],
                           'apiPassword' => $mailServiceIContact[ 'api_password' ],
                           'apiUsername' => $mailServiceIContact[ 'api_username' ]
                       )
                   );

        return iContactApi::getInstance();
    }


    /**
     * Save the details of IContact
    */
    public function saveIContactIntegration()
    {
        if ( empty( $_POST ) || !wp_verify_nonce( $_POST[ 'ANKIT_GUPTA_RAHUL_GUPTA' ], 'saveIContactIntegration' ) ) {
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
                    $validCredentials = false;
                    if ( $oiContact->getLists() ) {
                        $validCredentials = true;
                    }
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

    /**
     *
     * Add new column to Import user label
     *
     * @param $column
     *
     * @return mixed
     */
    public function userImport( $column )
    {
        $isDetails = $this->getIContactDetailsOfCurrentUser();
        if ( $isDetails ) {
            $column[ 'userImport' ] = 'Import User';
        }

        return $column;
    }

    /**
     * This is return IContact Details of Current User
    */
    public function getIContactDetailsOfCurrentUser()
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

    /**
     * Add Button for all users listed
     * @param $val
     * @param $column_name
     * @param $user_id
     *
     * @return string
     */
    public function userImportColumn( $val, $column_name, $user_id )
    {
        if ( 'userImport' != $column_name ) {
            return $val;
        }
        $loaderIcon = '<img id="importLoaderImage" style="display: none;" src="' . MAIL_SERVICE_DIRECTORY_PLUGIN_URL .
                      'assets/img/loading-gif.gif" />';
        $button     = '<a href="javascript:void(0)"  data-userId="' . $user_id .
                      '"class="button button-primary button-large openPopUpListing">' . $loaderIcon .
                      ' Import User</a>';

        return $button;
    }

    /**
     *  Show Import User PopUp View
     */
    public function openPopUpImportWizard()
    {
        $iContactLists  = $this->getIContactListDetails();
        $userId         = intval( $_GET[ 'userID' ] );
        $importUserData = $this->getImportUserDetails( $userId );
        include( MAIL_SERVICE_DIRECTORY_PLUGIN_DIR . 'views/import-form.php' );
        die( 0 );
    }

    /**
     *  Get Listing of IContact
     */
    public function getIContactListDetails()
    {
        $iContactApi = $this->initiateIContactObject();
        $lists       = $iContactApi->getLists();

        return $lists;
    }

    /**
     * Get details of User
     * @param $userId
     *
     * @return array
     */
    public function getImportUserDetails( $userId )
    {
        $importUser = get_userdata( $userId );
        $email      = $importUser->user_email;
        $first_name = $importUser->first_name != '' ? $importUser->first_name : 'Not available';
        $last_name  = $importUser->last_name != '' ? $importUser->last_name : 'Not available';

        return [
            'email'      => $email,
            'first_name' => $first_name,
            'last_name'  => $last_name
        ];
    }


    /**
     * Use to Import User
     */
    public function triggerIContactImport()
    {
        if ( isset( $_POST[ 'formData' ] ) ) {
            parse_str( $_POST[ 'formData' ], $formData );
            $iContactLists = $this->getIContactListDetails();
            $listIdSet     = [ ];
            foreach ( $iContactLists as $list ) {
                $listIdSet[] = $list->listId;
            }
            $importUserData = $this->getImportUserDetails( $formData[ 'user_id' ] );
            if ( in_array( $formData[ 'i_contact_list' ], $listIdSet ) ) {
                $importUsersData[] = $importUserData;
                $result            = $this->importUsersToIContact( $importUsersData, $formData[ 'i_contact_list' ] );
                echo json_encode( $result );
            } else {
                echo json_encode(
                    [
                        'status' => 'error',
                        'msg'    => 'This List not exists in IContact anymore.'
                    ]
                );
            }
        }
        die( 0 );
    }


    /**
     * Add Assets in Admin
     */
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

}
