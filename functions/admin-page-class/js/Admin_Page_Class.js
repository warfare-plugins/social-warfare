/**
 * Aadmin pages class
 *
 * JS used for the admin pages class and other form items.
 *
 * Copyright 2011 Ohad Raz (admin@bainternet.info)
 * @since 1.0
 */

var $ =jQuery.noConflict();
//code editor
var Ed_array = Array;
//upload button
var formfield1;
var formfield2;
var file_frame;

	availableOptions = {
		flatFresh: {
			fullColor 		: 'Full Color',
			lightGray 		: 'Light Gray',
			mediumGray		: 'Medium Gray',
			darkGray 		: 'Dark Gray',
			lgOutlines 		: 'Light Gray Outlines',
			mdOutlines		: 'Medium Gray Outlines',
			dgOutlines 		: 'Dark Gray Outlines',
			colorOutlines 	: 'Color Outlines',
			customColor 	: 'Custom Color',
			ccOutlines 		: 'Custom Color Outlines'
		},
		leaf: {
			fullColor 		: 'Full Color',
			lightGray 		: 'Light Gray',
			mediumGray		: 'Medium Gray',
			darkGray 		: 'Dark Gray',
			lgOutlines 		: 'Light Gray Outlines',
			mdOutlines		: 'Medium Gray Outlines',
			dgOutlines 		: 'Dark Gray Outlines',
			colorOutlines 	: 'Color Outlines',
			customColor 	: 'Custom Color',
			ccOutlines 		: 'Custom Color Outlines'
		},
		pill: {	
			fullColor 		: 'Full Color',
			lightGray 		: 'Light Gray',
			mediumGray		: 'Medium Gray',
			darkGray 		: 'Dark Gray',
			lgOutlines 		: 'Light Gray Outlines',
			mdOutlines		: 'Medium Gray Outlines',
			dgOutlines 		: 'Dark Gray Outlines',
			colorOutlines 	: 'Color Outlines',
			customColor 	: 'Custom Color',
			ccOutlines 		: 'Custom Color Outlines'
		},
		threeDee: {
			fullColor 		: 'Full Color',
			lightGray 		: 'Light Gray',
			mediumGray		: 'Medium Gray',
			darkGray 		: 'Dark Gray'
		},
		connected: {
			fullColor 		: 'Full Color',
			lightGray 		: 'Light Gray',
			mediumGray		: 'Medium Gray',
			darkGray 		: 'Dark Gray',
			lgOutlines 		: 'Light Gray Outlines',
			mdOutlines		: 'Medium Gray Outlines',
			dgOutlines 		: 'Dark Gray Outlines',
			colorOutlines 	: 'Color Outlines',
			customColor 	: 'Custom Color',
			ccOutlines 		: 'Custom Color Outlines'
		},
		shift: {
			fullColor 		: 'Full Color',
			lightGray 		: 'Light Gray',
			mediumGray		: 'Medium Gray',
			darkGray 		: 'Dark Gray',
			lgOutlines 		: 'Light Gray Outlines',
			mdOutlines		: 'Medium Gray Outlines',
			dgOutlines 		: 'Dark Gray Outlines',
			colorOutlines 	: 'Color Outlines',
			customColor 	: 'Custom Color',
			ccOutlines 		: 'Custom Color Outlines'
		}
	};
	
	// Check if we are on the admin page
	if(jQuery('select[name="visualTheme"]').length) {
		
		// Update the items and previews on the initial page load
		var visualTheme = jQuery('select[name="visualTheme"]').val();
		var dColorSet    = jQuery('select[name="dColorSet"]').val();
		var iColorSet    = jQuery('select[name="iColorSet"]').val();
		var oColorSet    = jQuery('select[name="oColorSet"]').val();
		jQuery('select[name="dColorSet"] option, select[name="iColorSet"] option, select[name="oColorSet"] option').remove();
		jQuery.each(availableOptions[visualTheme], function(index, value) {
			if(index == dColorSet) {
				jQuery('select[name="dColorSet"]').append('<option value="'+index+'" selected>'+value+'</option>');
			} else {
				jQuery('select[name="dColorSet"]').append('<option value="'+index+'">'+value+'</option>');
			};
			if(index == iColorSet) {
				jQuery('select[name="iColorSet"]').append('<option value="'+index+'" selected>'+value+'</option>');
			} else {
				jQuery('select[name="iColorSet"]').append('<option value="'+index+'">'+value+'</option>');
			};
			if(index == oColorSet) {
				jQuery('select[name="oColorSet"]').append('<option value="'+index+'" selected>'+value+'</option>');
			} else {
				jQuery('select[name="oColorSet"]').append('<option value="'+index+'">'+value+'</option>');
			};
			
			
			if(dColorSet == 'customColor' || dColorSet == 'ccOutlines' || iColorSet == 'customColor' || iColorSet == 'ccOutlines' || oColorSet == 'customColor' || oColorSet == 'ccOutlines' ) {
				jQuery('h3.customColorLabel').parents('.sw_field').show();
				jQuery('h3.customColorLabel').parents('.sw_field').next('.form-table').show();
				
				updateCustomColor();
				
			} else {
				jQuery('h3.customColorLabel').parents('.sw_field').hide();
				jQuery('h3.customColorLabel').parents('.sw_field').next('.form-table').hide();
			};
		});
		
		// A function for updating the preview
		function updateTheme() {
			var visualTheme  = jQuery('select[name="visualTheme"]').val();
			var dColorSet    = jQuery('select[name="dColorSet"]').val();
			var iColorSet    = jQuery('select[name="iColorSet"]').val();
			var oColorSet    = jQuery('select[name="oColorSet"]').val();
			var buttonsClass = 'sw_'+visualTheme+' sw_d_'+dColorSet+' sw_i_'+iColorSet+' sw_o_'+oColorSet;
			if(typeof lastClass === 'undefined'){
				jQuery('.nc_socialPanel').removeClass('sw_flatFresh sw_d_fullColor sw_i_fullColor sw_o_fullColor').addClass(buttonsClass);
			} else {
				jQuery('.nc_socialPanel').removeClass(lastClass).addClass(buttonsClass);
			};
			lastClass = buttonsClass;
			if(dColorSet == 'customColor' || dColorSet == 'ccOutlines' || iColorSet == 'customColor' || iColorSet == 'ccOutlines' || oColorSet == 'customColor' || oColorSet == 'ccOutlines' ) {
				jQuery('h3.customColorLabel').parents('.sw_field').slideDown();
				jQuery('h3.customColorLabel').parents('.sw_field').next('.form-table').slideDown();
				
				updateCustomColor();
				
				
			} else {
				jQuery('h3.customColorLabel').parents('.sw_field').slideUp();
				jQuery('h3.customColorLabel').parents('.sw_field').next('.form-table').slideUp();
			};
		};
		setTimeout( updateTheme , 2000 );
		
		// If the color set changes, update the preview with the function
		jQuery('select[name="dColorSet"], select[name="iColorSet"], select[name="oColorSet"]').on('change', updateTheme);
		
		// If the visual theme is updated, update the preview manually
		jQuery('select[name="visualTheme"]').on('change', function() {
			
			var visualTheme  = jQuery('select[name="visualTheme"]').val();
			var dColorSet    = jQuery('select[name="dColorSet"]').val();
			var iColorSet    = jQuery('select[name="iColorSet"]').val();
			var oColorSet    = jQuery('select[name="oColorSet"]').val();
			
			var i = 0;
			var array = availableOptions[visualTheme];
			
			console.log(array);
			
			var dColor = array.hasOwnProperty(dColorSet);
			var iColor = array.hasOwnProperty(iColorSet);
			var oColor = array.hasOwnProperty(oColorSet);
			
			jQuery('select[name="dColorSet"] option, select[name="iColorSet"] option, select[name="oColorSet"] option').remove();
			jQuery.each(availableOptions[visualTheme], function(index, value) {
				if(index == dColorSet || (dColor == false && i == 0)) {
					jQuery('select[name="dColorSet"]').append('<option value="'+index+'" selected>'+value+'</option>');
				} else {
					jQuery('select[name="dColorSet"]').append('<option value="'+index+'">'+value+'</option>');
				};
				if(index == iColorSet || (iColor == false && i == 0)) {
					jQuery('select[name="iColorSet"]').append('<option value="'+index+'" selected>'+value+'</option>');
				} else {
					jQuery('select[name="iColorSet"]').append('<option value="'+index+'">'+value+'</option>');
				};
				if(index == oColorSet || (oColor == false && i == 0)) {
					jQuery('select[name="oColorSet"]').append('<option value="'+index+'" selected>'+value+'</option>');
				} else {
					jQuery('select[name="oColorSet"]').append('<option value="'+index+'">'+value+'</option>');
				};
				++i;
			});

			var buttonsClass = 'sw_'+visualTheme+' sw_d_'+dColorSet+' sw_i_'+iColorSet+' sw_o_'+oColorSet;
			if(typeof lastClass === 'undefined'){
				jQuery('.nc_socialPanel').removeClass('sw_flatFresh sw_d_fullColor sw_i_fullColor sw_o_fullColor').addClass(buttonsClass);
			} else {
				jQuery('.nc_socialPanel').removeClass(lastClass).addClass(buttonsClass);
			};
			lastClass = buttonsClass;
		});
	}
	
	

	if(jQuery('input[name="minTotes"]').length) {
		jQuery('input[name="minTotes"]').attr('type','number').attr('min','0').css({'float':'right','margin-top':'5px'});
	};
	jQuery('a.nav_tab_link').click( function() {
		setTimeout( function() {
			swSetWidths(true);
		}, 1000);
	});
	function swUpdateScale() {
		jQuery('.nc_socialPanel').css({width:'100%'});
		var width = jQuery('.nc_socialPanel').width();
		scale = jQuery('select[name="buttonSize"]').val();
		align = jQuery('select[name="buttonFloat"]').val();
		if((align == 'fullWidth' && scale != 1) || scale >= 1) {
			newWidth = width / scale;
			jQuery('.nc_socialPanel').css('cssText', 'width:'+newWidth+'px!important;');
			jQuery('.nc_socialPanel').css({
				'transform':'scale('+scale+')',
				'transform-origin':'left'	
			});
		} else if(align != 'fullWidth' && scale < 1) {
			newWidth = width / scale;
			jQuery('.nc_socialPanel').css({
				'transform':'scale('+scale+')',
				'transform-origin':align	
			});
		}
		swSetWidths(true);
	}
	
	function swShowAlignment() {
		scale = jQuery('select[name="buttonSize"').val();
		if(scale >= 1) {
			jQuery('select[name="buttonFloat"').parents('.sw_field').slideUp();
		} else {
			jQuery('select[name="buttonFloat"').parents('.sw_field').slideDown();	
		}
	}
	
	if(jQuery('select[name="buttonSize"]').length) {
		swUpdateScale();
		swShowAlignment();
		jQuery('select[name="buttonSize"],select[name="buttonFloat"]').on('change',function() {
			swUpdateScale();
			swShowAlignment();
		});
	}
	
	function swButtonFloatStyles() {
		useExistingStyle = jQuery('div[rel="floatStyleSource"]').hasClass('on');
		if(useExistingStyle) {
			jQuery('select[name="sideDColorSet"]').parents('.sw_field').slideUp();
			jQuery('select[name="sideIColorSet"]').parents('.sw_field').slideUp();
			jQuery('select[name="sideOColorSet"]').parents('.sw_field').slideUp();
		} else {
			jQuery('select[name="sideDColorSet"]').parents('.sw_field').slideDown();
			jQuery('select[name="sideIColorSet"]').parents('.sw_field').slideDown();
			jQuery('select[name="sideOColorSet"]').parents('.sw_field').slideDown();
		}
		var option = jQuery('select[name="floatOption"]').val();
		if(option == 'top' || option == 'bottom') {
			jQuery('select[name="floatStyle"]').parents('.sw_field').slideUp();
			jQuery('input[name="floatStyleSource"]').parents('.sw_field').slideUp();
			jQuery('input[name="floatBgColor"]').parents('.sw_field').slideDown();
		} else {
			jQuery('select[name="floatStyle"]').parents('.sw_field').slideDown();
			jQuery('input[name="floatStyleSource"]').parents('.sw_field').slideDown();
			jQuery('input[name="floatBgColor"]').parents('.sw_field').slideUp();
		}
	}
	
	if(jQuery('input[name="floatStyleSource"]').length) {
		useExistingStyle = jQuery('input[name="floatStyleSource"]').val();
		if(useExistingStyle) {
			jQuery('select[name="sideDColorSet"]').parents('.sw_field').slideUp();
			jQuery('select[name="sideIColorSet"]').parents('.sw_field').slideUp();
			jQuery('select[name="sideOColorSet"]').parents('.sw_field').slideUp();
		} else {
			jQuery('select[name="sideDColorSet"]').parents('.sw_field').slideDown();
			jQuery('select[name="sideIColorSet"]').parents('.sw_field').slideDown();
			jQuery('select[name="sideOColorSet"]').parents('.sw_field').slideDown();
		}
		swButtonFloatStyles();
		jQuery('input[name="floatStyleSource"], select[name="floatOption"]').on('change',function() {
			swButtonFloatStyles();
		});
	}
	
oldColorCode = null;
if(jQuery('input[name="customColor"]').length) {
	setInterval( function() {
		newColorCode = jQuery('input[name="customColor"]').val();
		if(newColorCode != oldColorCode) {
			updateCustomColor();	
		};
		oldColorCode = newColorCode;
	} , 500);
}
	
function updateCustomColor() {
		jQuery('style.sw_customColorStuff').remove();
			var colorCode = jQuery('input[name="customColor"]').val();
			customCSS = '';
			if(dColorSet == 'customColor' || iColorSet == 'customColor' || oColorSet == 'customColor') {
				var customCSS = '.nc_socialPanel.sw_d_customColor a, html body .nc_socialPanel.sw_i_customColor .nc_tweetContainer:hover a, body .nc_socialPanel.sw_o_customColor:hover a {color:white} .nc_socialPanel.sw_d_customColor .nc_tweetContainer, html body .nc_socialPanel.sw_i_customColor .nc_tweetContainer:hover, body .nc_socialPanel.sw_o_customColor:hover .nc_tweetContainer {background-color:'+colorCode+';border:1px solid '+colorCode+';}';
			}
			if(dColorSet == 'ccOutlines' || iColorSet == 'ccOutlines' || oColorSet == 'ccOutlines' ) {
				var customCSS = customCSS+' .nc_socialPanel.sw_d_ccOutlines a, html body .nc_socialPanel.sw_i_ccOutlines .nc_tweetContainer:hover a, body .nc_socialPanel.sw_o_ccOutlines:hover a { color:'+colorCode+'; } .nc_socialPanel.sw_d_ccOutlines .nc_tweetContainer, html body .nc_socialPanel.sw_i_ccOutlines .nc_tweetContainer:hover, body .nc_socialPanel.sw_o_ccOutlines:hover .nc_tweetContainer { background:transparent; border:1px solid '+colorCode+'; }';	
			}
			
			jQuery('head').append('<style type="text/css" class="sw_customColorStuff">'+customCSS+'</style>');	
	};
	
function sw_loading_screen() {
		jQuery('body').addClass('sw_loading').append('<div class="sw_loading_modal"><div class="sw_modal_message">Processing Registration<br /><br /><span class="sw_modal_subtitle">Please Wait a Moment<span></div></div>');	
}
function sw_clear_loading_screen() {
	jQuery('body').removeClass('sw_loading');
	jQuery('.sw_loading_modal').remove();
}

jQuery(document).ready(function($) {

	jQuery('input[name="premiumCode"]').attr('readonly','readonly');
	jQuery('input[name="regCode"]').parent('.sw_field').hide();

	if(jQuery('input[name="premiumCode"]').val() != '' && jQuery('.sw_not_registered').length ) {
		sw_loading_screen();
		jQuery('form[name="admin_page_class"]').submit();
	} else if(jQuery('input[name="premiumCode"]').val() == '' && jQuery('.sw_registered').length ) {
		sw_loading_screen();
		jQuery('form[name="admin_page_class"]').submit();
	};

	jQuery('.activate[value="Activate Plugin"]').on('click',function(e) {
		e.preventDefault();
		sw_loading_screen();
		var regCode = jQuery('input[name="regCode"]').val();
		var email = jQuery('input[name="emailAddress"]').val();
		var domain = jQuery('input[name="domain"]').val();
		url = ('https:' == document.location.protocol ? 'https:' : 'http:') + '//warfareplugins.com/registration-api/?activity=register&emailAddress='+email+'&domain='+domain+'&registrationCode='+regCode;
		jQuery.get( url, function( data ) {
			var object = jQuery.parseJSON(data);
			if(object['status'] == 'failure') {
				sw_clear_loading_screen();
				alert('Failure: '+object['message']);	
			} else {
				jQuery('input[name="premiumCode"]').val(object['premiumCode']);
				jQuery('form[name="admin_page_class"]').submit();
			}
		});
	});
	jQuery('.deactivate[value="Deactivate Plugin"]').on('click',function(e) {
		e.preventDefault();
		sw_loading_screen();
		var regCode = jQuery('input[name="regCode"]').val();
		var email = jQuery('input[name="emailAddress"]').val();
		var domain = jQuery('input[name="domain"]').val();
		url = ('https:' == document.location.protocol ? 'https:' : 'http:') + '//warfareplugins.com/registration-api/?activity=unregister&emailAddress='+email+'&domain='+domain+'&registrationCode='+regCode;
		jQuery.get( url, function( data ) {
			var object = $.parseJSON(data);
			jQuery('input[name="premiumCode"]').val('');
			jQuery('input[name="emailAddress"]').val('');
			jQuery('form[name="admin_page_class"]').submit();
		});
	});
  apc_init();  
  //editor rezise fix
  jQuery(window).resize(function() {
    jQuery.each(Ed_array, function() {
      var ee = this;
      $(ee.getScrollerElement()).width(100); // set this low enough
      width = $(ee.getScrollerElement()).parent().width();
      $(ee.getScrollerElement()).width(width); // set it to
      ee.refresh();
    });
  });

	ooIconsAdmin();  
  
	jQuery('select[name="orderOfIconsSelect"]').on('change',ooIconsAdmin);
  
}); //end ready

  function ooIconsAdmin() {
	  var orderOfIconsSelect = jQuery('select[name="orderOfIconsSelect"]').val();
	  console.log(orderOfIconsSelect);
	  if(orderOfIconsSelect != 'manual') {
		jQuery('input[name="newOrderOfIcons[twitter]"]').parents('.sw_field').slideUp();  
	  } else {
		jQuery('input[name="newOrderOfIcons[twitter]"]').parents('.sw_field').slideDown(); 
	  }
  }


/**
 * apc_init initate fields
 * @since 1.2.2
 * @return void
 */
function apc_init(){
  /**
   * Code Editor Field
   * @since 2.1
   */
  load_code_editor();
  //iphone checkboxs
  fancyCheckbox();
  //select 2
  fancySelect();
  // repeater edit
  $(".at-re-toggle").live('click', function() {$(this).prev().toggle('slow');});
  /**
   * Datepicker Field.
   *
   * @since 1.0
   */
  loadDatePicker();
  /**
   * Timepicker Field.
   *
   * @since 1.0
   */
  loadTimePicker();
  /**
   * Colorpicker Field.
   *
   * @since 1.0
   * better handler for color picker with repeater fields support
   * which now works both when button is clicked and when field gains focus.
   */
  loadColorPicker();
  /**
   * Add Files.
   *
   * @since 1.0
   */
  $('.at-add-file').click( function() {
    var $first = $(this).parent().find('.file-input:first');
    $first.clone().insertAfter($first).show();
    return false;
  });
  /**
   * Delete File.
   *
   * @since 1.0
   */
  $('.at-upload').delegate( '.at-delete-file', 'click' , function() {
    
    var $this   = $(this),
        $parent = $this.parent(),
        data     = $this.attr('rel');
        
    $.post( ajaxurl, { action: 'at_delete_file', data: data }, function(response) {
      response == '0' ? ( alert( 'File has been successfully deleted.' ), $parent.remove() ) : alert( 'You do NOT have permission to delete this file.' );
    });
    
    return false;
  });
  /**
   * initiate repeater sortable option
   * since 0.4
   */
  $(".repeater-sortable").sortable();
  /**
   * initiate sortable fields option
   * since 0.4
   */
  $(".at-sortable").sortable({placeholder: "ui-state-highlight"});
  //new image upload field  
  load_images_muploader();
  //delete img button
  $('.at-delete_image_button').live('click', function(event){
    event.preventDefault();
    remove_image($(this));
    return false;
  });
  //upload images
  $('.at-upload_image_button').live('click',function(event){
    event.preventDefault();
    image_upload($(this));
    return false;
  });
  /**
   * listen for import button click
   * @since 0.8
   * @return void
   */
  $("#apc_import_b").live("click",function(){do_ajax_import_export('import');});
  /**
   * listen for export button click
   * @since 0.8
   * @return void
   */
  $("#apc_export_b").live("click",function(){do_ajax_import_export('export');});
  //refresh page
  $("#apc_refresh_page_b").live("click",function(){refresh_page();});
  //status alert dismiss
  $('[data-dismiss="alert"]').live("click",function(){$(this).parent().remove()});
}

/**
 * loadColorPicker 
 * @since 1.2.2
 * @return void
 */
function loadColorPicker(){
  if ($.farbtastic){//since WordPress 3.5
    $('.at-color').live('focus', function() {
      load_colorPicker($(this).next());
    });

    $('.at-color').live('focusout', function() {
      hide_colorPicker($(this).next());
    });

    /**
     * Select Color Field.
     *
     * @since 1.0
     */
    $('.at-color-select').live('click', function(){
      if ($(this).next('div').css('display') == 'none')
        load_colorPicker($(this));
      else
        hide_colorPicker($(this));
    });

    function load_colorPicker(ele){
      colorPicker = $(ele).next('div');
      input = $(ele).prev('input');

      $.farbtastic($(colorPicker), function(a) { $(input).val(a).css('background', a); });

      colorPicker.show();
      //e.preventDefault();

      //$(document).mousedown( function() { $(colorPicker).hide(); });
    }

    function hide_colorPicker(ele){
      colorPicker = $(ele).next('div');
      $(colorPicker).hide();
    }
    //issue #15
    $('.at-color').each(function(){
      var colo = $(this).val();
      if (colo.length == 7)
        $(this).css('background',colo);
    });
  }else{
    if ($('.at-color-iris').length>0){
      $('.at-color-iris').wpColorPicker(); 
    }
  }
}

/**
 * loadDatePicker 
 * @since 1.2.2
 * @return void
 */
function loadDatePicker(){
  $('.at-date').each( function() {
    var $this  = $(this),
        format = $this.attr('rel');
    $this.datepicker( { showButtonPanel: true, dateFormat: format } );
  });
}

/**
 * loadTimePicker 
 * @since 1.2.2
 * @return void
 */
function loadTimePicker(){
  $('.at-time').each( function() {
    var $this = $(this),
    format   =  $this.attr('rel');
    $this.timepicker( { showSecond: true, timeFormat: format } );
  });
}

/**
 * jQuery iphone style checkbox enable function
 * @since 1.1.5
 */
function fancyCheckbox(){
  $(':checkbox').each(function (){
    var $el = $(this);
    if(! $el.hasClass('no-toggle')){
      $el.FancyCheckbox();
      if ($el.hasClass("conditinal_control")){
        $el.live('change', function() {
          var $el = $(this);
          if($el.is(':checked'))
            $el.next().next().show('fast');    
          else
            $el.next().next().hide('fast');
        });
      }
    }else{
      if ($el.hasClass("conditinal_control")){
      $el.live('change', function() { 
        var $el = $(this);
        if($el.is(':checked'))
          $el.next().show('fast');    
        else
          $el.next().hide('fast');
        });
      }
    }
  });
}

/**
 * Select 2 enable function
 * @since 1.1.5
 */
function fancySelect(){
  $("select").each(function (){
    if(! $(this).hasClass('no-fancy'))
      $(this).select2();
  });
}

/**
 * remove_image description
 * @since 1.2.2
 * @param  jQuery element object
 * @return void
 */
function remove_image(ele){
  var $el = $(ele);
  var field_id = $el.attr("rel");
  var at_id = $el.prev().prev();
  var at_src = $el.prev();
  var t_button = $el;
  data = {
      action: 'apc_delete_mupload',
      _wpnonce: $('#nonce-delete-mupload_' + field_id).val(),
      field_id: field_id,
      attachment_id: jQuery(at_id).val()
  };

  $.getJSON(ajaxurl, data, function(response) {
    if ('success' == response.status){
      $(t_button).val("Upload Image");
      $(t_button).removeClass('at-delete_image_button').addClass('at-upload_image_button');
      //clear html values
      $(at_id).val('');
      $(at_src).val('');
      $(at_id).prev().html('');
      load_images_muploader();
    }else{
      alert(response.message);
    }
  }); 
}

/**
 * image_upload handle image upload
 * @since 1.2.2
 * @param  jquery element object
 * @return void
 */
function image_upload(ele){
  var $el = $(ele);
  formfield1 = $el.prev();
  formfield2 = $el.prev().prev();      
  if ($el.attr('data-u') == 'tk'){
    tb_show('', 'media-upload.php?post_id=0&type=image&apc=apc&TB_iframe=true');
    //store old send to editor function
    window.restore_send_to_editor = window.send_to_editor;
    //overwrite send to editor function
    window.send_to_editor = function(html) {
      imgurl = $('img',html).attr('src');
      img_calsses = $('img',html).attr('class').split(" ");
      att_id = '';
      $.each(img_calsses,function(i,val){
        if (val.indexOf("wp-image") != -1){
          att_id = val.replace('wp-image-', "");
        }
      });

      $(formfield2).val(att_id);
      $(formfield1).val(imgurl);
      load_images_muploader();
      tb_remove();
      //restore old send to editor function
      window.send_to_editor = window.restore_send_to_editor;
    }
  }else{
    // Uploading files since WordPress 3.5
    // If the media frame already exists, reopen it.
    if ( file_frame ) {
      file_frame.open();
      return;
    }
    // Create the media frame.
    file_frame = wp.media.frames.file_frame = wp.media({
      title: $el.data( 'uploader_title' ),
      button: {
        text: $el.data( 'uploader_button_text' ),
      },
      multiple: false  // Set to true to allow multiple files to be selected
    });
    // When an image is selected, run a callback.
    file_frame.on( 'select', function() {
      // We set multiple to false so only get one image from the uploader
      attachment = file_frame.state().get('selection').first().toJSON();
      // Do something with attachment.id and/or attachment.url here
      jQuery(formfield2).val(attachment.id);
      jQuery(formfield1).val(attachment.url);
      load_images_muploader();
    });
    // Finally, open the modal
    file_frame.open();
  }
}

/**
 * load_images_muploader 
 * load images after upload
 * @return void
 */
function load_images_muploader(){
  $(".mupload_img_holder").each(function(i,v){
    if ($(this).next().next().val() != ''){
      if (!$(this).children().size() > 0){
        var h = $(this).attr('data-he');
        var w = $(this).attr('data-wi');
        $(this).append('<img src="' + $(this).next().next().val() + '" style="height: '+ h +';width: '+ w +';" />');
        $(this).next().next().next().val("Delete");
        $(this).next().next().next().removeClass('at-upload_image_button').addClass('at-delete_image_button');
      }
    }
  });
}

/**
 * load_code_editor  loads code editors
 * @since 1.2.2
 * @return void
 */
function load_code_editor(){
  var e_d_count = 0;
  $(".code_text").each(function() {
    var lang = $(this).attr("data-lang");
    //php application/x-httpd-php
    //css text/css
    //html text/html
    //javascript text/javascript
    switch(lang){
      case 'php':
        lang = 'application/x-httpd-php';
        break;
      case 'less':
      case 'css':
        lang = 'text/css';
        break;
      case 'html':
        lang = 'text/html';
        break;
      case 'javascript':
        lang = 'text/javascript';
        break;
      default:
        lang = 'application/x-httpd-php';
    }
    var theme  = $(this).attr("data-theme");
    switch(theme){
      case 'default':
        theme = 'default';
        break;
      case 'light':
        theme = 'solarizedLight';
        break;
      case 'dark':

        theme = 'solarizedDark';;
        break;
      default:
        theme = 'default';
    }
    
    var editor = CodeMirror.fromTextArea(document.getElementById($(this).attr('id')), {
      lineNumbers: true,
      matchBrackets: true,
      mode: lang,
      indentUnit: 4,
      indentWithTabs: true,
      enterMode: "keep",
      tabMode: "shift"
    });
    editor.setOption("theme", theme);
    $(editor.getScrollerElement()).width(100); // set this low enough
    width = $(editor.getScrollerElement()).parent().width();
    $(editor.getScrollerElement()).width(width); // set it to
    editor.refresh();
    Ed_array[e_d_count] = editor;
    e_d_count++;
  });
}

/***************************
 * Import Export Functions *
 * ************************/

/**
 * do_ajax 
 * 
 * @author Ohad Raz <admin@bainternet.info> 
 * @since 0.8
 * @param  string which  (import|export)
 * 
 * @return void
 */
function do_ajax_import_export(which){
  before_ajax_import_export(which);
  var group = jQuery("#option_group_name").val();
  var seq_selector = "#apc_" + which + "_nonce";
  var action_selctor = "apc_" + which + "_" + group;
  jQuery.ajaxSetup({ cache: false });
  if (which == 'export')
    export_ajax_call(action_selctor,group,seq_selector,which);
  else
    import_ajax_call(action_selctor,group,seq_selector,which);
  jQuery.ajaxSetup({ cache: true });
}

/**
 * export_ajax_call make export ajax call
 * 
 * @author Ohad Raz <admin@bainternet.info> 
 * @since 0.8
 * 
 * @param  string action 
 * @param  string group
 * @param  string seq_selector
 * @param  string which   
 * @return void
 */
function export_ajax_call(action,group,seq_selector,which){
  jQuery.getJSON(ajaxurl,
    {
      group: group,
      rnd: microtime(false), //hack to avoid request cache
      action: action,
      seq: jQuery(seq_selector).val()
    },
    function(data) {
      if (data){
        export_response(data);
      }else{
        alert("Something Went Wrong, try again later");
      }
      after_ajax_import_export(which);
    }
  );
}

/**
 * import_ajax_call make import ajax call
 * 
 * @author Ohad Raz <admin@bainternet.info> 
 * @since 0.8
 * 
 * @param  string action 
 * @param  string group
 * @param  string seq_selector
 * @param  string which   
 * @return void
 */
function import_ajax_call(action,group,seq_selector,which){
  jQuery.post(ajaxurl,
    {
      group: group,
      rnd: microtime(false), //hack to avoid request cache
      action: action,
      seq: jQuery(seq_selector).val(),
      imp: jQuery("#import_code").val(),
    },
    function(data) {
      if (data){
         import_response(data);
      }else{
        alert("Something Went Wrong, try again later");
      }
      after_ajax_import_export(which);
    },
     "json"
  );
}

/**
 * before_ajax_import_export 
 * 
 * @author Ohad Raz <admin@bainternet.info> 
 * @since 0.8
 * @param  string which  (import|export)
 * 
 * @return void
 */
function before_ajax_import_export(which){
  jQuery(".import_status").hide("fast");
  jQuery(".export_status").hide("fast");
  jQuery(".export_results").html('').removeClass('alert-success').hide();
  jQuery(".import_results").html('').removeClass('alert-success').hide();
  if (which == 'import')
    jQuery(".import_status").show("fast");
  else
    jQuery(".export_status").show("fast");
}

/**
 * after_ajax_import_export
 * 
 * @author Ohad Raz <admin@bainternet.info> 
 * @since 0.8
 * @param  string which  (import|export)
 * 
 * @return void
 */
function after_ajax_import_export(which){
  if (which == 'import')
    jQuery(".import_status").hide("fast");
  else
    jQuery(".export_status").hide("fast");
}

/**
 * export_reponse
 * 
 * @author Ohad Raz <admin@bainternet.info> 
 * @since 0.8
 * @param  json data ajax response
 * @return void
 */
function export_response(data){
  if (data.code)
    jQuery('#export_code').val(data.code);
  if (data.nonce)
    jQuery("#apc_export_nonce").val(data.nonce);
  if(data.err)
    jQuery(".export_results").html(data.err).show('slow');
}

/**
 * import_reponse
 * 
 * @author Ohad Raz <admin@bainternet.info> 
 * @since 0.8
 * @param  json data ajax response
 * 
 * @return void
 */
function import_response(data){
  if (data.nonce)
    jQuery("#apc_import_nonce").val(data.nonce);
  if(data.err)
    jQuery(".import_results").html(data.err);
  if (data.success)
    jQuery(".import_results").html(data.success).addClass('alert-success').show('slow');
}

/********************
 * Helper Functions *
 *******************/

/**
 * refresh_page 
 * @since 0.8
 * @return void
 */
function refresh_page(){

  location.reload();
}

/**
 * microtime used as hack to avoid ajax cache
 * 
 * @author Ohad Raz <admin@bainternet.info> 
 * @since 0.8
 * @param  boolean get_as_float 
 * 
 * @return microtime as int or float 
 */
function microtime(get_as_float) { 
  var now = new Date().getTime() / 1000; 
  var s = parseInt(now); 
  return (get_as_float) ? now : (Math.round((now - s) * 1000) / 1000) + " " + s; 
}

/**
 * Helper Function
 *
 * Get Query string value by name.
 *
 * @since 1.0
 */
function get_query_var( name ) {

  var match = RegExp('[?&]' + name + '=([^&#]*)').exec(location.href);
  return match && decodeURIComponent(match[1].replace(/\+/g, ' '));   
}