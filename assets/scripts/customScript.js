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