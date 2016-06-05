// Admin Tabs
jQuery(document).ready(function() {
	jQuery(document).on('click','.sw-tab-selector',function(event) {
		event.preventDefault ? event.preventDefault() : (event.returnValue = false);
		var tab = jQuery(this).attr('data-link');
		jQuery('.sw-admin-tab').hide();
		jQuery('#'+tab).show();
		jQuery('.sw-header-menu li').removeClass('sw-active-tab');
		jQuery(this).parents('li').addClass('sw-active-tab');
	});
});