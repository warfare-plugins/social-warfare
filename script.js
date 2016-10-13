jQuery.fn.outerHTML = function( s ) {
	return s ? this.before( s ).remove() : jQuery( '<p>' ).append( this.eq( 0 ).clone() ).html();
 };
(function( jQuery ) {
	var uniqueCntr = 0;
	jQuery.fn.scrolled = function( waitTime, fn ) {
		if ( typeof waitTime === 'function' ) {
			fn = waitTime;
			waitTime = 500;
		};
		var tag = 'scrollTimer' + uniqueCntr++;
		this.scroll(function() {
			var self = jQuery( this );
			var timer = self.data( tag );
			if ( timer ) {
				clearTimeout( timer );
			};
			timer = setTimeout(function() {
				self.removeData( tag );
				fn.call( self[0] );
			}, waitTime );
			self.data( tag, timer );
		});
	};
})( jQuery );
jQuery( document ).on( 'click', '.nc_tweet, a.swp_CTT', function( event ) {
	if ( jQuery( this ).hasClass( 'noPop' ) || ! jQuery( this ).attr( 'data-link' ) ) {} else {
		event.preventDefault ? event.preventDefault() : ( event.returnValue = false );
		href = jQuery( this ).attr( 'data-link' );
		href = href.replace( '’', '\'' );
		if ( jQuery( this ).hasClass( 'pinterest' ) || jQuery( this ).hasClass( 'buffer_link' ) || jQuery( this ).hasClass( 'flipboard' ) ) {
			height = 550;
			width = 775;
		} else {
			height = 270;
			width = 500;
		};
		instance = window.open( href, '_blank', 'height=' + height + ',width=' + width );
		// instance.document.write("<meta http-equiv=\"refresh\" content=\"0;url="+href+"\">");
		// instance.document.close();
		return false;
	};
});
function isOdd( num ) {
	return num % 2;
}

// Function to check if the buttons are on one line or two

var swp_check_is_running = false;
function swp_button_size_check() {
	if ( swp_check_is_running == true ) {
		return false
	} else {
		swp_check_is_running = true;

		// Let's check each iteration of the social panel
		var not_inline = false;
		jQuery( '.nc_socialPanel:not(.nc_socialPanelSide)' ).each( function() {
			// Fetch the offset.top of the first element in the panel
			if ( jQuery( this ).find( '.nc_tweetContainer:nth-child(1)' ).css( 'display' ) != 'none' ) {
				first_button = jQuery( this ).find( '.nc_tweetContainer:nth-child(1)' ).offset();
				first_label = 'First';
			} else {
				first_button = jQuery( this ).find( '.nc_tweetContainer:nth-child(2)' ).offset();
				first_label = 'Second';
			}

			// Fetch the offset.top of the last element in the panel
			if ( jQuery( this ).find( '.nc_tweetContainer:nth-last-child(1)' ).css( 'display' ) != 'none' ) {
				last_button = jQuery( this ).find( '.nc_tweetContainer:nth-last-child(1)' ).offset();
				last_label = 'Last';
			} else {
				last_button = jQuery( this ).find( '.nc_tweetContainer:nth-last-child(2)' ).offset();
				last_label = 'Second Last';
			}

			if ( first_button.top != last_button.top ) {
				not_inline = true;
			}
		});
		if ( typeof window.swp_adjust == 'undefined' ) {
			window.swp_adjust = 0
		}
		if ( not_inline == true && window.swp_adjust <= 20 ) {
			swSetWidths( true, true );
		} else {
			jQuery( '.nc_socialPanel' ).css({ opacity: 1 });
		}
		swp_check_is_running = false;
	}
}

// Function to set or reset the button sizes to fit their respective container area
function swSetWidths( resize, adjust, secondary ) {
	var anim_props = {
		duration: 0,
		easing: 'linear',
		queue: false
	};

	// Check if this is the first or a forced resize
	if ( typeof window.origSets === 'undefined' || resize ) {
		// Declare the variable for saving presets
		window.origSets = [];

		// Declare the variable for saving original measurements
		if ( typeof window.defaults === 'undefined' ) {
			window.defaults = [];
		};

		// Loop through each set of buttons
		jQuery( '.nc_socialPanel:not(.nc_socialPanelSide)' ).each( function() {
			// Declare a global so we can save the sizes for faster processing later
			var index = jQuery( '.nc_socialPanel' ).index( jQuery( this ) );
			if ( typeof window.defaults[index] === 'undefined' ) {
				window.defaults[index] = [];
			};

			// Measure the width of the container. Find out how much space is available.
			// if( resize == true && adjust == false ) {
			//	window.swp_adjust = 0;
			// }

			if ( typeof window.swp_adjust === 'undefined' && ! adjust ) {
				window.swp_adjust = 0;
				//var totalWidth 	= jQuery(this).width() - 0;
			} else if ( typeof window.swp_adjust === 'undefined' && adjust == true ) {
				window.swp_adjust = 1;
				// var totalWidth  = jQuery(this).width() - 1;
			} else if ( adjust == true ) {
				++window.swp_adjust;
				// var totalWidth  = jQuery(this).width() - window.swp_adjust;
			} else {
				window.swp_adjust = 0;
			};
			var totalWidth  = jQuery( this ).width() - window.swp_adjust;

			// Count the number of buttons
			var totalElements	= jQuery( this ).attr( 'data-count' );

			// The average shows us how wide each button needs to become
			var average = parseInt( totalWidth ) / parseInt( totalElements );
			var space = parseInt( totalWidth ) - parseInt( totalElements );

			// Check how much space is on the left so we can show or hide the floating buttons if they exist
			var offset = jQuery( '.nc_socialPanel:not(.nc_socialPanelSide)' ).offset();
			var min_screen_width = jQuery( '.nc_socialPanelSide' ).attr( 'data-screen-width' );

			// If we have 100px, show the side floaters. If not, hide it.
			if ( offset.left < 100 || jQuery( window ).width() < min_screen_width ) {
				jQuery( '.nc_socialPanelSide' ).addClass( 'mobile' );
			} else {
				jQuery( '.nc_socialPanelSide' ).removeClass( 'mobile' );
			};

			// Declare some variables for use later
			var widthNeeded = 0;
			var padding = 0;
			var totesWidth = 0;

			// Check if we already have a widthNeeded saved from earlier
			if ( typeof window.defaults[index].default_width_needed === 'undefined' ) {
				// Loop through each button
				jQuery( this ).find( '.nc_tweetContainer' ).each( function() {
					// Make sure we add extra space for expansions and whatnot
					if ( totalElements > 3 ) {
						extraSpace = ( totalElements - 1 ) * 5;
					} else {
						extraSpace = ( totalElements - 1 ) * 15;
					};

					// Check how wide it must be to fit
					widthNeeded += jQuery( this ).width() + extraSpace;

					var paddingLeft = jQuery( this ).find( '.swp_count' ).css( 'padding-left' );
					paddingLeft = parseInt( paddingLeft.replace( 'px', '' ) );
					var paddingRight = jQuery( this ).find( '.swp_count' ).css( 'padding-right' );
					paddingRight = parseInt( paddingRight.replace( 'px', '' ) );
					padding = paddingLeft + paddingRight;
					widthNeeded = widthNeeded - padding;
				});
				// Save the width needed for later use
				window.defaults[index].default_width_needed = widthNeeded;

			// If we already have it, use it
			} else {
				widthNeeded = window.defaults[index].default_width_needed;
			}

			// Check if we have enough room to display the total shares
			totesWidth = jQuery( this ).find( '.nc_tweetContainer.totes' ).width();
			if ( totalWidth < widthNeeded && ! jQuery( this ).hasClass( 'nc_floater' ) ) {
				jQuery( this ).find( '.nc_tweetContainer.totes' ).hide();
			}

			if ( ( totalWidth ) <= ( widthNeeded - totesWidth + 25 ) && ! jQuery( this ).hasClass( 'nc_floater' ) ) {
				if ( jQuery( this ).find( '.totes' ).length ) {
					if ( jQuery( this ).hasClass( 'connected' ) ) {
						var average = ( parseInt( totalWidth ) / ( parseInt( totalElements ) - 1 ) );
					} else {
						var average = ( parseInt( totalWidth ) / ( parseInt( totalElements ) - 1 ) ) - 10;
					};
					var oddball = average * ( totalElements - 1 );
				} else {
					if ( jQuery( this ).hasClass( 'connected' ) ) {
						var average = ( parseInt( totalWidth ) / ( parseInt( totalElements ) ) );
					} else {
						var average = ( parseInt( totalWidth ) / ( parseInt( totalElements ) ) ) - 11;
					};
					var oddball = average * totalElements;
				};
				var oddball = totalWidth - oddball;
				jQuery( this ).addClass( 'mobile' ).removeClass( 'notMobile' );
				jQuery( '.spaceManWilly' ).css({ width: 'auto' });
				buttonWidths = 0;
				if ( ! jQuery( '.swp_count .iconFiller' ).length ) {
					jQuery( this ).find( '.nc_tweetContainer.totes,.nc_tweetContainer .swp_count' ).hide();
				} else {
					jQuery( this ).find( '.nc_tweetContainer.totes' ).hide();
				};
				jQuery( this ).find( '.nc_tweetContainer' ).each(function() {
					width = jQuery( this ).find( '.iconFiller' ).width();
					if ( isOdd( average ) ) {
						marginLeft = Math.floor( ( average - width ) / 2 ) - 1;
						marginRight = Math.floor( ( average - width ) / 2 ) - 1;
					} else {
						marginLeft = ( ( average - width ) / 2 ) - 1;
						marginRight = ( ( average - width ) / 2 ) - 1;
					};
					jQuery( this ).find( '.swp_count' ).animate({ 'padding-left': 0,'padding-right': 0 }, anim_props );
					jQuery( this ).find( '.iconFiller' ).animate({ 'margin-left': marginLeft + 'px','margin-right': marginRight + 'px' }, anim_props );
				});
			// jQuery('.nc_tweetContainer').css({"padding-left":"4px","padding-right":"4px"});
			} else {
				jQuery( this ).addClass( 'notMobile' ).removeClass( 'mobile' );
				if ( totalWidth > widthNeeded ) {
					jQuery( this ).find( '.nc_tweetContainer.totes,.nc_tweetContainer .swp_count' ).show();
				}
				jQuery( this ).find( '.nc_tweetContainer .iconFiller' ).animate({ 'margin-left': '0px','margin-right': '0px' }, anim_props );
				var average = Math.floor( average );
				var oddball = average * totalElements;
				var oddball = totalWidth - oddball;
				if ( jQuery( this ).find( '.totesalt' ).length ) {
					var totes = jQuery( this ).find( '.totes:visible' ).outerWidth( true );
					newTotalWidth = totalWidth - totes;
					average = parseInt( newTotalWidth ) / parseInt( totalElements - 1 );
					average = Math.floor( average );
					oddball = average * ( totalElements - 1 );
					oddball = newTotalWidth - oddball;
				} else {
					var totes = jQuery( this ).find( '.totes:visible' ).outerWidth( true );
					if ( totes > average ) {
						newTotalWidth = totalWidth - totes;
						average = parseInt( newTotalWidth ) / parseInt( totalElements - 1 );
						average = Math.floor( average );
						oddball = average * ( totalElements - 1 );
						oddball = newTotalWidth - oddball;
					};
				}
				count = 0;
				index = jQuery( '.nc_socialPanel' ).index( jQuery( this ) );
				window.origSets[index] = [];
				if ( jQuery( this ).hasClass( 'nc_floater' ) ) {
					// If this is the floating bar, don't size it independently. Just clone the settings from the other one.
					var firstSocialPanel = jQuery( '.nc_socialPanel' ).not( '[data-float="float_ignore"]' ).first();
					float_index_origin = jQuery( '.nc_socialPanel' ).index( firstSocialPanel );
					jQuery( this ).replaceWith( firstSocialPanel.outerHTML() );
					width = firstSocialPanel.outerWidth( true );
					offset = firstSocialPanel.offset();
					parent_offset = firstSocialPanel.parent().offset();
					jQuery( '.nc_socialPanel' ).last().addClass( 'nc_floater' ).css(
						{
							width: width,
							left: parent_offset.left
						});
					activateHoverStates();
					window.origSets['float'] = window.origSets[float_index_origin];
				} else {
					jQuery( this ).find( '.nc_tweetContainer' ).not( '.totesalt' ).each(function() {
						icon 		= jQuery( this ).find( 'i.sw' ).outerWidth() + 14;
						shareTerm 	= jQuery( this ).find( '.swp_share' ).outerWidth();
						tote		= icon + shareTerm + 3;
						jQuery( this ).find( '.spaceManWilly' ).animate({ width: tote + 'px' }, anim_props );

						++count;
						var paddingLeft = jQuery( this ).find( '.swp_count' ).css( 'padding-left' );
						paddingLeft = parseInt( paddingLeft.replace( 'px', '' ) );
						var paddingRight = jQuery( this ).find( '.swp_count' ).css( 'padding-right' );
						paddingRight = parseInt( paddingRight.replace( 'px', '' ) );
						dataId = jQuery( this ).attr( 'data-id' );
						dataId = parseInt( dataId );
						if ( count > totalElements ) {
							count = 1;
						}
						if ( count <= oddball ) {
							add = 1;
						} else {
							add = 0;
						};
						curWidth = jQuery( this ).outerWidth( true );
						curWidth = curWidth - paddingLeft;
						curWidth = curWidth - paddingRight;
						dif = average - curWidth;
						window.origSets[index][dataId] = new Array();
						if ( isOdd( dif ) ) {
							dif = dif - 1;
							dif = dif / 2;
							pl = dif + 1 + average;
							pr = dif + average;
							window.origSets[index][dataId]['pl'] = dif + 1 + 'px';
							window.origSets[index][dataId]['pr'] = dif + 'px';
							window.origSets[index][dataId]['fil'] = jQuery( this ).find( '.iconFiller' ).width() + 'px';
							jQuery( this ).find( '.swp_count' ).animate({
								'padding-left': window.origSets[index][dataId]['pl'],
								'padding-right': window.origSets[index][dataId]['pr']
							}, 0, 'linear', function() {
								jQuery( this ).css({ transition: 'padding .1s linear'
							});
							});
						} else {
							dif = dif / 2;
							pl = dif + average;
							pr = dif + average;
							window.origSets[index][dataId]['pl'] = dif + 'px';
							window.origSets[index][dataId]['pr'] = dif + 'px';
							window.origSets[index][dataId]['fil'] = jQuery( this ).find( '.iconFiller' ).width() + 'px';
							jQuery( this ).find( '.swp_count' ).animate({
								'padding-left': window.origSets[index][dataId]['pl'],
								'padding-right': window.origSets[index][dataId]['pr']
							}, 0, 'linear', function() {
									jQuery( this ).css({ transition: 'padding .1s linear' });
								});
						};
						window.resized = true;
					});
				};
			};
		});
		if ( secondary == true || window.swp_secondary == true ) {
			window.swp_secondary = true;
			setTimeout( function() {
				swp_button_size_check();
			}, 200 );
		}
	// If we already have sizes, just reuse them
	} else {
		jQuery( '.nc_tweetContainer' ).not( '.totesalt' ).each(function() {
			if ( jQuery( this ).parents( '.nc_wrapper' ).length ) {
				index = 'float';
			} else {
				index = jQuery( '.nc_socialPanel' ).index( jQuery( this ).parent( '.nc_socialPanel' ) );
			};
			dataId = parseInt( jQuery( this ).attr( 'data-id' ) );
			if ( typeof window.origSets[index] === 'undefined' ) { } else {
				jQuery( this ).find( '.iconFiller' ).animate({ width: window.origSets[index][dataId]['fil'] }, anim_props );
				jQuery( this ).find( '.swp_count' ).animate({
					'padding-left': window.origSets[index][dataId]['pl'],
					'padding-right':  window.origSets[index][dataId]['pr']
				}, anim_props );
			};
		});
	};
};

function createFloatBar() {
	if ( ! jQuery( '.nc_wrapper .nc_socialPanel' ).length && ! jQuery( '.nc_socialPanelSide' ).length ) {
		var firstSocialPanel = jQuery( '.nc_socialPanel' ).not( '[data-float="float_ignore"]' ).first();
		var index = jQuery( '.nc_socialPanel' ).index( firstSocialPanel );
		var floatOption = firstSocialPanel.attr( 'data-float' );
		var alignment = firstSocialPanel.attr( 'data-align' );
		if ( floatOption ) {
			backgroundColor = jQuery( '.nc_socialPanel' ).attr( 'data-floatColor' );
			jQuery( '<div class="nc_wrapper" style="background-color:' + backgroundColor + '"></div>' ).appendTo( 'body' );
			position = firstSocialPanel.attr( 'data-float' );
			firstSocialPanel.clone().appendTo( '.nc_wrapper' );
			jQuery( '.nc_wrapper' ).hide().addClass( position );
			width = firstSocialPanel.outerWidth( true );
			offset = firstSocialPanel.offset();
			jQuery( '.nc_socialPanel' ).last().addClass( 'nc_floater' ).css(
				{
					width: width,
					left: ( alignment == 'center' ? 0 : offset.left )
				});
			jQuery( '.nc_socialPanel .swp_count' ).css({ transition: 'padding .1s linear' });
			jQuery( '.nc_socialPanel' ).eq( 0 ).addClass( 'swp_one' );
			jQuery( '.nc_socialPanel' ).eq( 2 ).addClass( 'swp_two' );
			jQuery( '.nc_socialPanel' ).eq( 1 ).addClass( 'swp_three' );
			window.origSets['float'] = window.origSets[index];
			swSetWidths();
		};
	}
};
// Format Number functions
function ReplaceNumberWithCommas( nStr ) {
	nStr += '';
	x = nStr.split( '.' );
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while ( rgx.test( x1 ) ) {
		x1 = x1.replace( rgx, '$1' + ',' + '$2' );
	};
	return x1 + x2;
};
function number_format( val ) {
	if ( val < 1000 ) {
		 return ReplaceNumberWithCommas( val );
	 } else {
		 val = val / 1000;
		val = Math.round( val );
		return ReplaceNumberWithCommas( val ) + 'K';
	 };
};
// Twitter Shares Count

function floatingBar() {
	jQuery( window ).on( 'scroll', function() {
		floatingBarReveal();
	});
};

function floatingBarReveal() {
	// Adjust the floating bar
	var panels = jQuery( '.nc_socialPanel' );
	var floatOption = panels.not( '[data-float="float_ignore"]' ).eq( 0 ).attr( 'data-float' );
	var windowElement = jQuery( window );
	var windowHeight = windowElement.height();
	var ncWrapper = jQuery( '.nc_wrapper' );
	var ncSideFloater = jQuery( '.nc_socialPanelSide' ).filter( ':not(.mobile)' );
	var position = jQuery( '.nc_socialPanel' ).attr( 'data-position' );
	var minWidth = ncSideFloater.attr( 'data-screen-width' );
	offsetOne = panels.eq( 0 ).offset();
	scrollPos = windowElement.scrollTop();
	var st = jQuery( window ).scrollTop();
	if ( typeof window.swp_offsets == 'undefined' ) {
		window.swp_offsets = {};
	}
	if ( floatOption == 'floatBottom' || floatOption == 'floatTop' ) {
		var visible = false;
		jQuery( '.nc_socialPanel' ).not( '.nc_socialPanelSide, .nc_wrapper .nc_socialPanel' ).each(function() {
			index = jQuery( '.nc_socialPanel' ).index( jQuery( this ) );

			// Fetch our base numbers
			if ( typeof window.swp_offsets[index] == 'undefined' ) {
				var thisOffset 		= jQuery( this ).offset();
				var thisHeight 		= jQuery( this ).height();
				var screenBottom 	= thisOffset + thisHeight;
				window.swp_offsets[index] = thisOffset;
			} else {
				var thisOffset 		= window.swp_offsets[index];
				var thisHeight 		= jQuery( this ).height();
				var screenBottom 	= thisOffset + thisHeight;
			}

			// Check if it's visible
			if ( thisOffset.top + thisHeight > scrollPos && thisOffset.top < scrollPos + windowHeight ) {
				visible = true;
			};
		});
		if ( visible ) {
			// Hide the Floating bar
			ncWrapper.hide();

			// Add some padding to the page so it fits nicely at the top or bottom
			if ( floatOption == 'floatBottom' ) {
				jQuery( 'body' ).animate({ 'padding-bottom': window.body_padding_bottom + 'px' }, 0 );
			} else if ( floatOption == 'floatTop' ) {
				jQuery( 'body' ).animate({ 'padding-top': window.body_padding_top + 'px' }, 0 );
			};
		} else {
			// Show the floating bar
			ncWrapper.show();

			// Add some padding to the page so it fits nicely at the top or bottom
			if ( floatOption == 'floatBottom' ) {
				new_padding = window.body_padding_bottom + 50;
				jQuery( 'body' ).animate({ 'padding-bottom': new_padding + 'px' }, 0 );
			} else if ( floatOption == 'floatTop' ) {
				first_offset = jQuery( '.nc_socialPanel' ).not( '.nc_socialPanelSide, .nc_wrapper .nc_socialPanel' ).first().offset();
				console.log( first_offset );
				if ( first_offset.top > scrollPos + windowHeight ) {
					new_padding = window.body_padding_top + 50;
					jQuery( 'body' ).animate({ 'padding-top': new_padding + 'px' }, 0 );
				}
			};
		};
	} else if ( floatOption == 'floatLeft' ) {
		var visible = false;
		if ( jQuery( '.nc_socialPanel' ).not( '.nc_socialPanelSide' ).length ) {
			jQuery( '.nc_socialPanel' ).not( '.nc_socialPanelSide' ).each(function() {
					var thisOffset = jQuery( this ).offset();
					var thisHeight = jQuery( this ).height();
					if ( thisOffset.top + thisHeight > scrollPos && thisOffset.top < scrollPos + windowHeight ) {
						visible = true; ncSideFloaterDisplay = true;
					};
				});
			if ( visible || jQuery( '.nc_socialPanelSide' ).hasClass( 'mobile' ) ) {
				visible = true;
			} else {
				visible = false;
			};
		} else {
			if ( jQuery( window ).width() > minWidth ) {
				visible = false;
			} else {
				visible = true;
			}
		}

		var transition = ncSideFloater.attr( 'data-transition' );
		if ( transition == 'slide' ) {
			if ( visible == true ) {
				ncSideFloater.css({ left: '-100px' }, 200 );
			} else {
				ncSideFloater.css({ left: '5px' });
			}
		} else if ( transition == 'fade' ) {
			if ( visible == true ) {
				ncSideFloater.fadeOut( 200 );
			} else {
				ncSideFloater.fadeIn( 200 );
			}
		}
		/*

		if(position == 'both') {
			offsetTwo = panels.eq(1).offset();
			if(offsetOne.top < (scrollPos) && offsetTwo.top > (scrollPos + windowHeight) && st >= lst) {
				ncSideFloater.addClass('displayed');

			} else if(offsetOne.top < (scrollPos) && offsetTwo.top > (scrollPos + windowHeight) && st <= lst) {
				ncSideFloater.addClass('displayed');
			} else if(offsetTwo.top < (scrollPos + (windowHeight / 2)) && offsetTwo.top > scrollPos ){
				ncSideFloater.removeClass('displayed');
			} else if(offsetTwo.top < (scrollPos)) {
				ncSideFloater.addClass('displayed');
			} else if(offsetTwo.top < (scrollPos + windowHeight) && st > lst) {
				ncSideFloater.removeClass('displayed');
			} else {
				ncSideFloater.removeClass('displayed');
			};
		} else if(position == 'above') {
			if(offsetOne.top < (scrollPos)) {
				ncSideFloater.addClass('displayed');
			} else {
				ncSideFloater.removeClass('displayed');
			};
		} else if(position == 'below') {
			if(offsetOne.top > (scrollPos + windowHeight)) {
				ncSideFloater.addClass('displayed');
			} else if (offsetOne.top < (scrollPos)) {
				ncSideFloater.addClass('displayed');
			} else if(offsetOne.top < scrollPos + (windowHeight / 2) ){
				ncSideFloater.removeClass('displayed');
			} else {
				ncSideFloater.removeClass('displayed');
			};
		};
		*/
	};
	lst = st;
}

function activateHoverStates() {
	jQuery( '.nc_tweetContainer' ).not( '.totesalt, .nc_socialPanelSide .nc_tweetContainer' ).on( 'mouseenter',
		function() {
			if ( ! jQuery( this ).parents( '.nc_socialPanel' ).hasClass( 'mobile' ) ) {
				thisElem 	= jQuery( this );
				icon 		= thisElem.find( '.iconFiller' ).width();
				shareTerm 	= thisElem.find( '.swp_share' ).outerWidth();
				wrapper		= thisElem.find( '.spaceManWilly' ).outerWidth();
				tote		= wrapper;
				dif			= wrapper - icon;
				origDif		= dif;
				orig		= parseInt( tote ) - parseInt( dif );
				ele			= jQuery( this ).parents( '.nc_socialPanel' ).attr( 'data-count' );
				if ( jQuery( this ).siblings( '.totes' ).length ) {
					average 	= ( parseInt( dif ) / ( ( parseInt( ele ) -2 ) ) );
					average 	= Math.floor( average );
					oddball 	= dif % ( ele - 2 );
				} else {
					average 	= ( parseInt( dif ) / ( ( parseInt( ele ) -1 ) ) );
					average 	= Math.floor( average );
					oddball 	= dif % ( ele - 1 );
				};
				if ( jQuery( this ).parents( '.nc_wrapper' ).length ) {
					index = 'float';
				} else {
					index = jQuery( '.nc_socialPanel' ).index( jQuery( this ).parent( '.nc_socialPanel' ) );
				};
				dataId = parseInt( jQuery( this ).attr( 'data-id' ) );
				jQuery( this ).find( '.iconFiller' ).css({ width: wrapper });
				pl = window.origSets[index][dataId]['pl'];
				pr = window.origSets[index][dataId]['pr'];
				jQuery( this ).find( '.swp_count' ).css({
						'padding-left': window.origSets[index][dataId]['pl'],
						'padding-right': window.origSets[index][dataId]['pr']
					});
				dataId = jQuery( this ).attr( 'data-id' );
				count = 0;
				if ( jQuery( this ).hasClass( 'totes' ) ) {
					jQuery( this ).siblings( '.nc_tweetContainer' ).each(function() {
						dataId = parseInt( jQuery( this ).attr( 'data-id' ) );
						jQuery( this ).find( '.iconFiller' ).css({ width: window.origSets[index][dataId]['fil'] });
						jQuery( this ).find( '.swp_count' ).css({
							'padding-left': window.origSets[index][dataId]['pl'],
							'padding-right': window.origSets[index][dataId]['pr']
						});
					});
				} else {
					jQuery( this ).siblings( '.nc_tweetContainer' ).not( '.totes' ).each(function() {
						++count;
						if ( count <= oddball ) {
							ave = average + 1;
						} else {
							ave = average;
						};
						dataId = parseInt( jQuery( this ).attr( 'data-id' ) );
						if ( isOdd( ave ) ) {
							offsetL = ( ( ( ave - 1 ) / 2 ) + 1 );
							offsetR = ( ( ave - 1 ) / 2 );
							pl = parseInt( window.origSets[index][dataId]['pl'] ) - offsetL;
							pr = parseInt( window.origSets[index][dataId]['pr'] ) - offsetR;
						} else {
							offsetL = ( ave / 2 );
							offsetR = ( ave / 2 );
							pl = parseInt( window.origSets[index][dataId]['pl'] ) - offsetL;
							pr = parseInt( window.origSets[index][dataId]['pr'] ) - offsetR;
						};

						jQuery( this ).find( '.iconFiller' ).css({ width: origSets[index][dataId]['fil'] });
						jQuery( this ).find( '.swp_count' ).css({
							'padding-left': pl + 'px',
							'padding-right': pr + 'px'
						});
					});
				};
			};
		}
	);
	jQuery( '.nc_socialPanel' ).on( 'mouseleave click', function() {
		if ( ! jQuery( this ).hasClass( 'mobile' ) ) {
			swSetWidths();
		};
	});
	jQuery( '.nc_fade .nc_tweetContainer' ).on( 'mouseenter', function() {
		jQuery( this ).css({ opacity: 1 }).siblings( '.nc_tweetContainer' ).css({ opacity: 0.5 });
	});
	jQuery( '.nc_fade' ).on( 'mouseleave',
	function() {
		jQuery( '.nc_fade .nc_tweetContainer' ).css({ opacity: 1 });
	});
}
/*
function createTotal() {
	var tweets 		= jQuery('.twitter').attr('data-count');
	var linkshares	= jQuery('.linkedIn').attr('data-count');
	var pins 		= jQuery('.nc_pinterest').attr('data-count');
	var shares 		= jQuery('.fb').attr('data-count');
	var plusses 	= jQuery('.googlePlus').attr('data-count');
	var total = parseInt(tweets) + parseInt(linkshares) + parseInt(pins) + parseInt(shares) + parseInt(plusses);
	jQuery('.totes .swp_count').text(number_format(total)+' SHARES');
}
*/
function swApplyScale() {
	jQuery( '.nc_socialPanel' ).each( function() {
		jQuery( this ).css({ width: '100%' });
		var width = jQuery( this ).width();
		var scale = jQuery( this ).attr( 'data-scale' );
		var align = jQuery( this ).attr( 'data-align' );
		if ( ( align == 'fullWidth' && scale != 1 ) || scale > 1 || jQuery( this ).hasClass( 'nc_socialPanelSide' ) ) {
			newWidth = width / scale;
			jQuery( this ).css( 'cssText', 'width:' + newWidth + 'px!important;' );
			jQuery( this ).css({
				transform: 'scale(' + scale + ')',
				'transform-origin': 'left'
			});
		} else if ( align != 'fullWidth' && scale < 1 ) {
			newWidth = width / scale;
			jQuery( this ).css({
				transform: 'scale(' + scale + ')',
				'transform-origin': align
			});
		}
	});
}

function swp_init_share_buttons() {
	if ( jQuery( '.nc_socialPanel' ).length ) {
		swApplyScale();
		jQuery.when(
			swSetWidths( true )
		).done(function() {
			setTimeout( function() {
				swSetWidths( true, false, true );
			}, 200 );
		});
		createFloatBar();
		lst = jQuery( window ).scrollTop();
		floatingBar();
		floatingBarReveal();
		activateHoverStates();
	}
}

jQuery( document ).ready(function() {
	// Fetch the padding amount to make space later for the floating bars
	window.body_padding_top = parseInt( jQuery( 'body' ).css( 'padding-top' ).replace( 'px', '' ) );
	window.body_padding_bottom = parseInt( jQuery( 'body' ).css( 'padding-bottom' ).replace( 'px', '' ) );

	jQuery( window ).resize(function() {
		if ( jQuery( '.nc_socialPanel' ).length && jQuery( '.nc_socialPanel:hover' ).length !== 0 ) { } else {
			setTimeout( function() {
				window.swp_adjust = 1;
				swp_init_share_buttons();
			}, 100 );
		};
	});

	jQuery( document.body ).on( 'post-load', function() {
		setTimeout( function() {
			swp_init_share_buttons();
		}, 100 );
	} );
	if ( jQuery( '.nc_socialPanelSide' ).length ) {
		var buttonsHeight = jQuery( '.nc_socialPanelSide' ).height();
		var windowHeight = jQuery( window ).height();
		var newPosition = parseInt( ( windowHeight / 2 ) - ( buttonsHeight / 2 ) );
		setTimeout( function() {
			jQuery( '.nc_socialPanelSide' ).animate({ top: newPosition }, 0 ); console.log( newPosition );
		}, 105 );
	};

	setTimeout( function() {
		swp_init_share_buttons();
	}, 100 );

	// Reset the cache
	if ( typeof swp_cache_url != 'undefined' ) {
		// If the URL Contains a question mark already
		if ( swp_cache_url.indexOf( '?' ) != -1 ) {
			var url_params = '&swp_cache=rebuild';

		// If the URL does not contain a question mark already
		} else {
			var url_params = '?swp_cache=rebuild';
		};
		jQuery.get( swp_cache_url + url_params );
	};
});

/****************************************************************************

	Fetch and Store Facebook Counts

****************************************************************************/

function swp_fetch_facebook_shares() {
	var request_url = 'https://graph.facebook.com/?id=' + swp_post_url;
	jQuery.get( request_url, function( response ) {
		//response = jQuery.parseJSON(data);
		var request_url_two = 'https://graph.facebook.com/?id=' + swp_post_url + '&fields=og_object{likes.summary(true),comments.summary(true)}';
		jQuery.get( request_url_two, function( response_two ) {
			//response_two = jQuery.parseJSON(data);
			shares = parseInt( response['share']['share_count'] );
			likes = parseInt( response_two['og_object']['likes']['summary']['total_count'] );
			comments = parseInt( response_two['og_object']['comments']['summary']['total_count'] );
			activity = shares + likes + comments;
			console.log( activity );

			swp_post_data = { action: 'swp_facebook_shares_update',post_id: swp_post_id,activity: activity };
			jQuery.post( swp_admin_ajax, swp_post_data, function( response ) {
				console.log( response );
			});
		});
	});
}
/****************************************************************************

	Pin It Hover Effect

****************************************************************************/

function swp_pinit_button() {
	var defaults = {
		wrap: '<span class="sw-pinit"/>',
		pageURL: document.URL
	} ;

	var options = jQuery.extend( defaults, options );
	var o = options;

	//Iterate over the current set of matched elements
	jQuery( '.swp-content-locator' ).parent().find( 'img' ).each( function() {
		var e = jQuery( this ),
			pi_media = e.data( 'media' ) ? e.data( 'media' ) : e[0].src,
			pi_url = o.pageURL,
			pi_desc = e.attr( 'title' ) ? encodeURIComponent( e.attr( 'title' ) ) : encodeURIComponent( e.attr( 'alt' ) ),
			pi_isvideo = 'false';
		bookmark = 'http://pinterest.com/pin/create/bookmarklet/?media=' + encodeURI( pi_media ) + '&url=' + encodeURI( pi_url ) + '&is_video=' + encodeURI( pi_isvideo ) + '&description=' + pi_desc;
		css = jQuery( this ).css([ 'float','margin','padding','height','width' ]);

		var eHeight = e.outerHeight();
		var eWidth = e.outerWidth();

		if ( eHeight >= sw_pinit_min_height && eWidth >= sw_pinit_min_width ) {
			e.wrap( o.wrap );
			e.parent( '.sw-pinit' ).css( css ).css({ display: 'block' });
			e.css({ margin: 0 });
			e.before( '<span class="sw-pinit-overlay" style="height: ' + eHeight + 'px"><a href="' + bookmark + '" class="sw-pinit-button sw-pinit-' + swp_pinit_v_location + ' sw-pinit-' + swp_pinit_h_location + '">Save</a></span>' );
			e.css({ position: 'absolute' });

			jQuery( '.sw-pinit .sw-pinit-button' ).on( 'click', function() {
				window.open( jQuery( this ).attr( 'href' ), 'Pinterest', 'width=632,height=253,status=0,toolbar=0,menubar=0,location=1,scrollbars=1' );
				return false;
			});

			jQuery( '.sw-pinit' ).mouseenter(function() {
				jQuery( this ).children( '.sw-pinit-overlay' ).show();
			}).mouseleave(function() {
				jQuery( this ).children( '.sw-pinit-overlay' ).hide();
			});
		};
	});
}

jQuery( document ).ready( function() {
	setTimeout( function() {
		if ( typeof swp_pinit != 'undefined' && swp_pinit == true ) {
			swp_pinit_button();
		};
	}, 500 );
});
