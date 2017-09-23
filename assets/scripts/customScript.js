jQuery(document).on('ready', function () {

    // Click on element with 'popup' class
    jQuery('.openPopUpListing').on('click', function () {

        var userID = jQuery(this).data('userid');
        jQuery('<div id="openPopUpListingModal" class="modal"></div>').appendTo('body')
            .load(ADMIN_AJAX.URL + '?action=openPopUpImportWizard&userID=' + userID).show();
        return false;

    });


});
jQuery('body').on('click', '.closeModalListing', function () {
    jQuery('#openPopUpListingModal').remove();
});

jQuery('body').on('submit', '#IContactImportForm', function (event) {
    console.log(jQuery("#IContactImportForm").serialize());
    event.preventDefault();
    jQuery('#IContactImportFormSubmit').attr('disabled', 'disabled').addClass('disabled');
    jQuery.ajax({
        data: {action: 'triggerIContactImport', formData: jQuery("#IContactImportForm").serialize()},
        type: 'POST',
        url: ADMIN_AJAX.URL,
        dataType: 'json',
        success: function (data) {
            jQuery('#IContactImportFormSubmit').removeAttr('disabled').removeClass('disabled');
            jQuery('#IContactImportForm').css('display', 'none');
            jQuery('#IContactImportFormDiv').html(data.msg).css('display', 'block');
            if (data.status === 'error') {
                jQuery('#IContactImportFormDiv').addClass('error');
            } else {
                jQuery('#IContactImportFormDiv').addClass('success');
            }
            setTimeout(function () {
                jQuery('#openPopUpListingModal').remove();
            }, 5000);
        },
        error: function (err) {
            jQuery('#IContactImportFormDiv').addClass('error');
            jQuery('#IContactImportFormDiv').html(err);
        }
    });
});
