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
    <div class="updated below-h2" id="message">
        <p><?php echo $addMsg; ?></p>
    </div>
<?php } ?>

<?php if ( isset( $errorMsg ) ) {
    ?>
    <div class="error below-h2" id="message">
        <p><?php echo $errorMsg; ?></p>
    </div>
<?php } ?>
<table class="wrap" style="width: 100%">
    <tr>
        <td>


        </td>
    </tr>
    <tr>
        <td colspan="4">


            <form method="POST" action="<?php echo admin_url( 'admin-ajax.php' ); ?>">

                <?php wp_nonce_field( 'iContactIntegration', 'ankit-is-good-developer' ); ?>

                <input name="action" value="iContactIntegration" type="hidden"/>
                <input name="user_id" value="<?= $user_id; ?>" type="hidden"/>
                <input name="id" value="<?= $id; ?>" type="hidden"/>

                <table class="widefat">

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
</table>