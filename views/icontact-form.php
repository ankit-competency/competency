<?php
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<?php
global $wpdb;
$table_name          = $wpdb->prefix . "ecti_i_contact_setting";
$user_id             = get_current_user_id();
$mailServiceIContact = $wpdb->get_row(
    $wpdb->prepare( "SELECT * FROM $table_name where user_id= %d", $user_id ),
    ARRAY_A
);
$api_id              = $api_username = $api_password = $user_id = $id = '';
if ( $mailServiceIContact ) {
    $api_id       = $mailServiceIContact[ 'app_id' ];
    $api_username = $mailServiceIContact[ 'api_username' ];
    $api_password = $mailServiceIContact[ 'api_password' ];
    $user_id      = $mailServiceIContact[ 'user_id' ];
    $id           = $mailServiceIContact[ 'id' ];
}
$addMsg   = filter_input( INPUT_GET, 'addMsg', FILTER_SANITIZE_SPECIAL_CHARS );
$errorMsg = filter_input( INPUT_GET, 'errorMsg', FILTER_SANITIZE_SPECIAL_CHARS );
if ( isset( $addMsg ) ) {
    ?>
    <div id="message" class="updated notice is-dismissible">
        <p><?php echo $addMsg; ?></p>
        <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span>
        </button>
    </div>
<?php } ?>

<?php if ( isset( $errorMsg ) ) {
    ?>
    <div id="message" class="error notice is-dismissible">
        <p><?php echo $errorMsg; ?></p>
        <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span>
        </button>
    </div>
<?php } ?>
<br/>
<br/>
<table  class="widefat">

    <tr>
        <td>
            <div>
                <h2>How can I got this details</h2>
                <ul>
                    <li>Logged in to IContact Dashboard.</li>
                    <li><a href="https://app.icontact.com/icp/core/registerapp">Create API Keys</a></li>
                    <li><a href="https://app.icontact.com/icp/core/externallogin">Create your external login details</a></li>
                </ul>
            </div>
        </td>
    </tr>
</table>
<table class="wrap" style="width: 100%">
    <tr>
        <td>


        </td>
        <td>


        </td><td>


        </td><td>


        </td>


    </tr>
    <tr>
        <td colspan="2">


            <form method="POST" action="<?php echo admin_url( 'admin-ajax.php' ); ?>">

                <?php wp_nonce_field( 'saveIContactIntegration', 'ANKIT_GUPTA_RAHUL_GUPTA' ); ?>

                <input name="action" value="saveIContactIntegration" type="hidden"/>
                <input name="user_id" value="<?= $user_id; ?>" type="hidden"/>
                <input name="id" value="<?= $id; ?>" type="hidden"/>

                <table class="widefat" style="height: 330px;">

                    <thead>

                    <tr>

                        <th colspan="2">

                            <h3> <?php esc_html_e( 'Fill  IContact Details', 'Mail_Service_Admin_Settings' ); ?></h3>

                        </th>

                    </tr>

                    </thead>

                    <tbody>


                    <tr>

                        <td>

                            <h3>
                                <?php esc_html_e( 'API ID:', 'Mail_Service_Admin_Settings' ); ?>
                            </h3>
                        </td>

                        <td>

                            <input type="text" id="api_id" size="50" name="api_id" value="<?= $api_id; ?>"
                                   required="required"/>

                        </td>

                    </tr>
                    <tr>

                        <td>

                            <h3>
                                <?php esc_html_e( 'API Username:', 'Mail_Service_Admin_Settings' ); ?>
                            </h3>
                        </td>

                        <td>

                            <input type="text" id="api_username" size="50" name="api_username"
                                   value="<?= $api_username; ?>"
                                   required="required"/>

                        </td>

                    </tr>
                    <tr>

                        <td>

                            <h3>
                                <?php esc_html_e( 'API Password:', 'Mail_Service_Admin_Settings' ); ?>
                            </h3>
                        </td>

                        <td>

                            <input type="password" id="api_password" size="50" name="api_password"
                                   value="<?= $api_password; ?>"
                                   required="required"/>

                        </td>

                    </tr>


                    <tr>

                        <td colspan="2">

                            <input type="submit" value="Submit" name="submit" class="button button-primary"/>

                        </td>

                    </tr>

                    </tbody>

                </table>

            </form>


        </td>

    </tr>
    <?php if ( $mailServiceIContact ) { ?>
        <?php
        $callHook = new CallHooks();
        $lists    = $callHook->getIContactListDetails();
        ?>
        <tr>
            <td colspan="4">


                <form method="POST" action="<?php echo admin_url( 'admin-ajax.php' ); ?>">

                    <?php wp_nonce_field( 'bulkImportUsers', 'ANKIT_GUPTA_RAHUL_GUPTA' ); ?>

                    <input name="action" value="bulkImportUsers" type="hidden"/>
                    <input name="user_id" value="<?= $user_id; ?>" type="hidden"/>

                    <table class="widefat">

                        <thead>

                        <tr>

                            <th colspan="2">

                                <h3> <?php esc_html_e(
                                        'Import All Users in IContact',
                                        'Mail_Service_Admin_Settings'
                                    ); ?></h3>

                            </th>

                        </tr>

                        </thead>

                        <tbody>


                        <tr>

                            <td>

                                <h3>
                                    <?php esc_html_e( 'Lists:', 'Mail_Service_Admin_Settings' ); ?>
                                </h3>
                            </td>

                            <td>

                                <select id="i_contact_list" name="i_contact_list">
                                    <?php foreach ( $lists as $key => $list ) { ?>
                                        <option value="<?php echo $list->listId; ?>" <?php if ( !$key ) {
                                            echo 'selected="selected"';
                                        } ?> ><?php echo $list->name; ?></option>
                                    <?php } ?>
                                </select>

                            </td>

                        </tr>

                        <tr>

                            <td colspan="2">

                                <input type="submit" value="Submit" name="submit" class="button button-primary"/>

                            </td>

                        </tr>

                        </tbody>

                    </table>

                </form>


            </td>
        </tr>
    <?php } ?>
</table>


