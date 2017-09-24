jQuery(document).on('ready', function () {

    // Click on element with 'popup' class
    jQuery('.openPopUpListing').on('click', function () {

        var userID = jQuery(this).data('userid');
        var _this = this;
        jQuery(this).children('img').css('display', 'block');
        jQuery('<div id="openPopUpListingModal" class="modal"></div>').appendTo('body')
            .load(ADMIN_AJAX.URL + '?action=openPopUpImportWizard&userID=' + userID, function () {
                jQuery(_this).children('img').css('display', 'none');
            }).show();
        return false;

    });

    jQuery('.notice-dismiss').click(function () {
        jQuery(this).parent('div').remove();
    });
});
jQuery('body').on('click', '.closeModalListing', function () {
    jQuery('#openPopUpListingModal').remove();
});

jQuery('body').on('submit', '#IContactImportForm', function (event) {
    event.preventDefault();
    jQuery('#IContactImportFormSubmit').attr('disabled', 'disabled').addClass('disabled');
    jQuery('#loaderImage').css('display', 'block');
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
            jQuery('#loaderImage').css('display', 'none');
        }
    });
});
