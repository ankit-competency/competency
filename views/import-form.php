<?php
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<div class="IContactImportForm modal-content">
    <span class="close closeModalListing">&times;</span>
    <div>
        <div class="alert" id="IContactImportFormDiv" style="display: none;"></div>
        <form method="POST" id="IContactImportForm" name="IContactImportForm">
            <input type="hidden" name="user_id" value="<?php echo $userId; ?>"/>
            <label for="fname">First Name</label>
            <input type="text" id="fname" name="firstname" value="<?php echo $importUserData[ 'first_name' ] ?>"
                   readonly="readonly" placeholder="User First name..">

            <label for="lname">Last Name</label>
            <input type="text" id="lname" name="lastname" value="<?php echo $importUserData[ 'last_name' ]; ?>"
                   readonly="readonly" placeholder="User Last name..">

            <label for="email">Email Address</label>
            <input type="text" id="email" name="email" value="<?php echo $importUserData[ 'email' ]; ?>"
                   readonly="readonly" placeholder="User Email..">

            <label for="i_contact_list">IContact List</label>
            <select id="i_contact_list" name="i_contact_list">
                <?php foreach ( $iContactLists as $key => $list ) { ?>
                    <option value="<?php echo $list->listId; ?>" <?php if ( !$key ) {
                        echo 'selected="selected"';
                    } ?> ><?php echo $list->name; ?></option>
                <?php } ?>
            </select>
            <Button type="submit" class="IContactImportFormButton" id="IContactImportFormSubmit">
                <img id="loaderImage" style="display: none;"
                     src="<?php echo MAIL_SERVICE_DIRECTORY_PLUGIN_URL . 'assets/img/loading-gif.gif'; ?>"/>
                Submit
            </Button>
        </form>
    </div>

</div>