var socialWarfarePlugin = socialWarfarePlugin || {};

(function( window, $, undefined ) {
	'use strict';

	var swpCheckIsRunning = false;

	function isOdd( num ) {
		return num % 2;
	}

	function absint( $int ) {
		return parseInt( $int, 10 );
	}

	// Function to check if the buttons are on one line or two
	function buttonSizeCheck() {
		if ( true === swpCheckIsRunning ) {
			return false;
		} else {
			swpCheckIsRunning = true;

			// Let's check each iteration of the social panel
			var notInline = false;
			$( '.nc_socialPanel:not(.nc_socialPanelSide)' ).each( function() {
				var $that = $( this );
				var firstButton, firstLabel, lastButton, lastLabel;

				// Fetch the offset.top of the first element in the panel
				if ( 'none' !== $that.find( '.nc_tweetContainer:nth-child(1)' ).css( 'display' ) ) {
					firstButton = $( this ).find( '.nc_tweetContainer:nth-child(1)' ).offset();
					firstLabel = 'First';
				} else {
					firstButton = $that.find( '.nc_tweetContainer:nth-child(2)' ).offset();
					firstLabel = 'Second';
				}

				// Fetch the offset.top of the last element in the panel
				if ( 'none' !== $that.find( '.nc_tweetContainer:nth-last-child(1)' ).css( 'display' ) ) {
					lastButton = $that.find( '.nc_tweetContainer:nth-last-child(1)' ).offset();
					lastLabel = 'Last';
				} else {
					lastButton = $that.find( '.nc_tweetContainer:nth-last-child(2)' ).offset();
					lastLabel = 'Second Last';
				}

				if ( firstButton.top !== lastButton.top ) {
					notInline = true;
				}
			});
			if ( 'undefined' === typeof window.swpAdjust ) {
				window.swpAdjust = 0;
			}
			if ( true === notInline && window.swpAdjust <= 20 ) {
				socialWarfarePlugin.setWidths( true, true );
			} else {
				$( '.nc_socialPanel' ).css({ opacity: 1 });
			}
			swpCheckIsRunning = false;
		}
	}

	function reuseWidths( animation ) {
		$( '.nc_tweetContainer' ).not( '.totesalt' ).each(function() {
			var index;

			if ( $( this ).parents( '.nc_wrapper' ).length ) {
				index = 'float';
			} else {
				index = $( '.nc_socialPanel' ).index( $( this ).parent( '.nc_socialPanel' ) );
			}

			var dataId = absint( $( this ).attr( 'data-id' ) );

			if ( 'undefined' !== typeof window.origSets[index] ) {
				$( this ).find( '.iconFiller' ).animate({
					width: window.origSets[index][dataId].fil
				}, animation );

				$( this ).find( '.swp_count' ).animate({
					'padding-left': window.origSets[index][dataId].pl,
					'padding-right':  window.origSets[index][dataId].pr
				}, animation );
			}
		});
	}

	// Function to set or reset the button sizes to fit their respective container area
	socialWarfarePlugin.setWidths = function( resize, adjust, secondary ) {
		var animProps = {
			duration: 0,
			easing: 'linear',
			queue: false
		};

		// Check if this is the first or a forced resize
		if ( 'undefined' !== typeof window.origSets && ! resize ) {
			reuseWidths( animProps );
			return;
		}

		// Declare the variable for saving presets
		window.origSets = [];

		// Declare the variable for saving original measurements
		if ( 'undefined' === typeof window.defaults ) {
			window.defaults = [];
		}

		// Loop through each set of buttons
		$( '.nc_socialPanel:not(.nc_socialPanelSide)' ).each( function() {
			// Declare a global so we can save the sizes for faster processing later
			var index = $( '.nc_socialPanel' ).index( $( this ) );
			if ( 'undefined' === typeof window.defaults[index] ) {
				window.defaults[index] = [];
			}

			if ( 'undefined' === typeof window.swpAdjust && ! adjust ) {
				window.swpAdjust = 0;
			} else if ( 'undefined' === typeof window.swpAdjust && true === adjust ) {
				window.swpAdjust = 1;
			} else if ( true === adjust ) {
				++window.swpAdjust;
			} else {
				window.swpAdjust = 0;
			}

			var totalWidth  = $( this ).width() - window.swpAdjust;

			// Count the number of buttons
			var totalElements	= $( this ).attr( 'data-count' );

			// The average shows us how wide each button needs to become
			var average = absint( totalWidth ) / absint( totalElements );

			// Check how much space is on the left so we can show or hide the floating buttons if they exist
			var offset = $( '.nc_socialPanel:not(.nc_socialPanelSide)' ).offset();
			var minScreenWidth = $( '.nc_socialPanelSide' ).attr( 'data-screen-width' );

			// If we have 100px, show the side floaters. If not, hide it.
			if ( offset.left < 100 || $( window ).width() < minScreenWidth ) {
				$( '.nc_socialPanelSide' ).addClass( 'mobile' );
			} else {
				$( '.nc_socialPanelSide' ).removeClass( 'mobile' );
			}

			// Declare some variables for use later
			var widthNeeded = 0;
			var padding = 0;
			var totesWidth = 0;

			// Check if we already have a widthNeeded saved from earlier
			if ( 'undefined' === typeof window.defaults[index].defaultWidthNeeded ) {
				// Loop through each button
				$( this ).find( '.nc_tweetContainer' ).each( function() {
					var extraSpace = 0;

					// Make sure we add extra space for expansions and whatnot
					if ( totalElements > 3 ) {
						extraSpace = ( totalElements - 1 ) * 5;
					} else {
						extraSpace = ( totalElements - 1 ) * 15;
					}

					// Check how wide it must be to fit
					widthNeeded += $( this ).width() + extraSpace;

					var paddingLeft = $( this ).find( '.swp_count' ).css( 'padding-left' );
					paddingLeft = absint( paddingLeft.replace( 'px', '' ) );
					var paddingRight = $( this ).find( '.swp_count' ).css( 'padding-right' );
					paddingRight = absint( paddingRight.replace( 'px', '' ) );
					padding = paddingLeft + paddingRight;
					widthNeeded = widthNeeded - padding;
				});
				// Save the width needed for later use
				window.defaults[index].defaultWidthNeeded = widthNeeded;

			// If we already have it, use it
			} else {
				widthNeeded = window.defaults[index].defaultWidthNeeded;
			}

			// Check if we have enough room to display the total shares
			totesWidth = $( this ).find( '.nc_tweetContainer.totes' ).width();

			if ( totalWidth < widthNeeded && ! $( this ).hasClass( 'nc_floater' ) ) {
				$( this ).find( '.nc_tweetContainer.totes' ).hide();
			}

			var oddball, marginLeft, marginRight;

			if ( ( totalWidth ) <= ( widthNeeded - totesWidth + 25 ) && ! $( this ).hasClass( 'nc_floater' ) ) {
				if ( $( this ).find( '.totes' ).length ) {
					if ( $( this ).hasClass( 'connected' ) ) {
						average = ( absint( totalWidth ) / ( absint( totalElements ) - 1 ) );
					} else {
						average = ( absint( totalWidth ) / ( absint( totalElements ) - 1 ) ) - 10;
					}
					oddball = average * ( totalElements - 1 );
				} else {
					if ( $( this ).hasClass( 'connected' ) ) {
						average = ( absint( totalWidth ) / ( absint( totalElements ) ) );
					} else {
						average = ( absint( totalWidth ) / ( absint( totalElements ) ) ) - 11;
					}
					oddball = average * totalElements;
				}

				oddball = totalWidth - oddball;

				$( this ).addClass( 'mobile' ).removeClass( 'notMobile' );
				$( '.spaceManWilly' ).css({ width: 'auto' });

				if ( ! $( '.swp_count .iconFiller' ).length ) {
					$( this ).find( '.nc_tweetContainer.totes,.nc_tweetContainer .swp_count' ).hide();
				} else {
					$( this ).find( '.nc_tweetContainer.totes' ).hide();
				}
				$( this ).find( '.nc_tweetContainer' ).each(function() {
					width = $( this ).find( '.iconFiller' ).width();
					if ( isOdd( average ) ) {
						marginLeft = Math.floor( ( average - width ) / 2 ) - 1;
						marginRight = Math.floor( ( average - width ) / 2 ) - 1;
					} else {
						marginLeft = ( ( average - width ) / 2 ) - 1;
						marginRight = ( ( average - width ) / 2 ) - 1;
					}
					$( this ).find( '.swp_count' ).animate({ 'padding-left': 0,'padding-right': 0 }, animProps );
					$( this ).find( '.iconFiller' ).animate({ 'margin-left': marginLeft + 'px','margin-right': marginRight + 'px' }, animProps );
				});
			} else {
				$( this ).addClass( 'notMobile' ).removeClass( 'mobile' );

				if ( totalWidth > widthNeeded ) {
					$( this ).find( '.nc_tweetContainer.totes,.nc_tweetContainer .swp_count' ).show();
				}

				$( this ).find( '.nc_tweetContainer .iconFiller' ).animate({ 'margin-left': '0px','margin-right': '0px' }, animProps );

				average = Math.floor( average );
				oddball = average * totalElements;
				oddball = totalWidth - oddball;

				if ( $( this ).find( '.totesalt' ).length ) {
					var totes = $( this ).find( '.totes:visible' ).outerWidth( true );
					var newTotalWidth = totalWidth - totes;
					average = absint( newTotalWidth ) / absint( totalElements - 1 );
					average = Math.floor( average );
					oddball = average * ( totalElements - 1 );
					oddball = newTotalWidth - oddball;
				} else {
					var totes = $( this ).find( '.totes:visible' ).outerWidth( true );
					if ( totes > average ) {
						newTotalWidth = totalWidth - totes;
						average = absint( newTotalWidth ) / absint( totalElements - 1 );
						average = Math.floor( average );
						oddball = average * ( totalElements - 1 );
						oddball = newTotalWidth - oddball;
					}
				}

				var count = 0;
				index = $( '.nc_socialPanel' ).index( $( this ) );
				window.origSets[index] = [];

				if ( $( this ).hasClass( 'nc_floater' ) ) {
					// If this is the floating bar, don't size it independently. Just clone the settings from the other one.
					var firstSocialPanel = $( '.nc_socialPanel' ).not( '[data-float="float_ignore"]' ).first();
					var floatIndexOrigin = $( '.nc_socialPanel' ).index( firstSocialPanel );
					$( this ).replaceWith( firstSocialPanel.prop( 'outerHTML' ) );
					var width = firstSocialPanel.outerWidth( true );
					offset = firstSocialPanel.offset();
					var parentOffset = firstSocialPanel.parent().offset();
					$( '.nc_socialPanel' ).last().addClass( 'nc_floater' ).css(
						{
							width: width,
							left: parentOffset.left
						});
					socialWarfarePlugin.activateHoverStates();
					window.origSets['float'] = window.origSets[floatIndexOrigin];
				} else {
					$( this ).find( '.nc_tweetContainer' ).not( '.totesalt' ).each(function() {
						var icon      = $( this ).find( 'i.sw' ).outerWidth() + 14;
						var shareTerm = $( this ).find( '.swp_share' ).outerWidth();
						var tote      = icon + shareTerm + 3;
						$( this ).find( '.spaceManWilly' ).animate({ width: tote + 'px' }, animProps );

						++count;
						var paddingLeft = $( this ).find( '.swp_count' ).css( 'padding-left' );
						paddingLeft = absint( paddingLeft.replace( 'px', '' ) );
						var paddingRight = $( this ).find( '.swp_count' ).css( 'padding-right' );
						paddingRight = absint( paddingRight.replace( 'px', '' ) );
						var dataId = $( this ).attr( 'data-id' );
						dataId = absint( dataId );
						if ( count > totalElements ) {
							count = 1;
						}
						var add = 0;
						if ( count <= oddball ) {
							add = 1;
						}
						var curWidth = $( this ).outerWidth( true );
						curWidth = curWidth - paddingLeft;
						curWidth = curWidth - paddingRight;
						var dif = average - curWidth;
						window.origSets[index][dataId] = [];
						if ( isOdd( dif ) ) {
							dif = dif - 1;
							dif = dif / 2;
							pl = dif + 1 + average;
							pr = dif + average;
							window.origSets[index][dataId].pl = dif + 1 + 'px';
							window.origSets[index][dataId].pr = dif + 'px';
							window.origSets[index][dataId].fil = $( this ).find( '.iconFiller' ).width() + 'px';
							$( this ).find( '.swp_count' ).animate({
								'padding-left': window.origSets[index][dataId].pl,
								'padding-right': window.origSets[index][dataId].pr
							}, 0, 'linear', function() {
								$( this ).css({ transition: 'padding .1s linear'
								});
							});
						} else {
							dif = dif / 2;
							var pl = dif + average;
							var pr = dif + average;
							window.origSets[index][dataId].pl = dif + 'px';
							window.origSets[index][dataId].pr = dif + 'px';
							window.origSets[index][dataId].fil = $( this ).find( '.iconFiller' ).width() + 'px';
							$( this ).find( '.swp_count' ).animate({
								'padding-left': window.origSets[index][dataId].pl,
								'padding-right': window.origSets[index][dataId].pr
							}, 0, 'linear', function() {
									$( this ).css({
										transition: 'padding .1s linear'
									});
								});
						}
						window.resized = true;
					});
				}
			}
		});

		if ( true === secondary || true === window.swpSecondary ) {
			window.swpSecondary = true;
			setTimeout( function() {
				buttonSizeCheck();
			}, 200 );
		}
	};

	socialWarfarePlugin.activateHoverStates = function() {
		$( '.nc_tweetContainer' ).not( '.totesalt, .nc_socialPanelSide .nc_tweetContainer' ).on( 'mouseenter',
			function() {
				if ( ! $( this ).parents( '.nc_socialPanel' ).hasClass( 'mobile' ) ) {
					var thisElem = $( this );
					var icon         = thisElem.find( '.iconFiller' ).width();
					var shareTerm    = thisElem.find( '.swp_share' ).outerWidth();
					var wrapper      = thisElem.find( '.spaceManWilly' ).outerWidth();
					var tote         = wrapper;
					var dif          = wrapper - icon;
					var origDif      = dif;
					var orig         = absint( tote ) - absint( dif );
					var ele          = $( this ).parents( '.nc_socialPanel' ).attr( 'data-count' );
					var average, oddball, index;

					if ( $( this ).siblings( '.totes' ).length ) {
						average = ( absint( dif ) / ( ( absint( ele ) -2 ) ) );
						average = Math.floor( average );
						oddball = dif % ( ele - 2 );
					} else {
						average = ( absint( dif ) / ( ( absint( ele ) -1 ) ) );
						average = Math.floor( average );
						oddball = dif % ( ele - 1 );
					}

					if ( $( this ).parents( '.nc_wrapper' ).length ) {
						index = 'float';
					} else {
						index = $( '.nc_socialPanel' ).index( $( this ).parent( '.nc_socialPanel' ) );
					}

					var dataId = absint( $( this ).attr( 'data-id' ) );

					$( this ).find( '.iconFiller' ).css({ width: wrapper });

					var pl = window.origSets[index][dataId].pl;
					var pr = window.origSets[index][dataId].pr;

					$( this ).find( '.swp_count' ).css({
						'padding-left': window.origSets[index][dataId].pl,
						'padding-right': window.origSets[index][dataId].pr
					});

					dataId = $( this ).attr( 'data-id' );
					var count = 0;

					if ( $( this ).hasClass( 'totes' ) ) {
						$( this ).siblings( '.nc_tweetContainer' ).each(function() {
							dataId = absint( $( this ).attr( 'data-id' ) );
							$( this ).find( '.iconFiller' ).css({
								width: window.origSets[index][dataId].fil
							});
							$( this ).find( '.swp_count' ).css({
								'padding-left': window.origSets[index][dataId].pl,
								'padding-right': window.origSets[index][dataId].pr
							});
						});
					} else {
						$( this ).siblings( '.nc_tweetContainer' ).not( '.totes' ).each(function() {
							++count;
							var ave = average;
							var offsetL, offsetR;

							if ( count <= oddball ) {
								ave = ave + 1;
							}

							dataId = absint( $( this ).attr( 'data-id' ) );
							if ( isOdd( ave ) ) {
								offsetL = ( ( ( ave - 1 ) / 2 ) + 1 );
								offsetR = ( ( ave - 1 ) / 2 );
								pl = absint( window.origSets[index][dataId].pl ) - offsetL;
								pr = absint( window.origSets[index][dataId].pr ) - offsetR;
							} else {
								offsetL = ( ave / 2 );
								offsetR = ( ave / 2 );
								pl = absint( window.origSets[index][dataId].pl ) - offsetL;
								pr = absint( window.origSets[index][dataId].pr ) - offsetR;
							}

							$( this ).find( '.iconFiller' ).css({ width: origSets[index][dataId].fil });
							$( this ).find( '.swp_count' ).css({
								'padding-left': pl + 'px',
								'padding-right': pr + 'px'
							});
						});
					}
				}
			}
		);

		$( '.nc_socialPanel' ).on( 'mouseleave click', function() {
			if ( ! $( this ).hasClass( 'mobile' ) ) {
				socialWarfarePlugin.setWidths();
			}
		});

		$( '.nc_fade .nc_tweetContainer' ).on( 'mouseenter', function() {
			$( this ).css({ opacity: 1 }).siblings( '.nc_tweetContainer' ).css({ opacity: 0.5 });
		});

		$( '.nc_fade' ).on( 'mouseleave', function() {
			$( '.nc_fade .nc_tweetContainer' ).css({ opacity: 1 });
		});
	};

	/****************************************************************************

		Fetch and Store Facebook Counts

	****************************************************************************/

	socialWarfarePlugin.fetchFacebookShares = function() {
		var requestUrl = 'https://graph.facebook.com/?id=' + swp_post_url;
		$.get( requestUrl, function( response ) {
			//response = $.parseJSON(data);
			var requestUrTwo = 'https://graph.facebook.com/?id=' + swp_post_url + '&fields=og_object{likes.summary(true),comments.summary(true)}';
			$.get( requestUrTwo, function( responseTwo ) {
				var shares, likes, comments, activity;

				//responseTwo = $.parseJSON(data);
				shares = absint( response.share.share_count );
				likes = absint( responseTwo.og_object.likes.summary.total_count );
				comments = absint( responseTwo.og_object.comments.summary.total_count );
				activity = shares + likes + comments;
				console.log( activity );

				swpPostData = {
					action: 'swp_facebook_shares_update',
					post_id: swp_post_id,
					activity: activity
				};

				$.post( swp_admin_ajax, swpPostData, function( response ) {
					console.log( response );
				});
			});
		});
	}

	function createFloatBar() {
		if ( ! $( '.nc_wrapper .nc_socialPanel' ).length && ! $( '.nc_socialPanelSide' ).length ) {
			var firstSocialPanel = $( '.nc_socialPanel' ).not( '[data-float="float_ignore"]' ).first();
			var index = $( '.nc_socialPanel' ).index( firstSocialPanel );
			var floatOption = firstSocialPanel.attr( 'data-float' );
			var alignment = firstSocialPanel.attr( 'data-align' );
			if ( floatOption ) {
				var backgroundColor = $( '.nc_socialPanel' ).attr( 'data-floatColor' );
				$( '<div class="nc_wrapper" style="background-color:' + backgroundColor + '"></div>' ).appendTo( 'body' );
				var position = firstSocialPanel.attr( 'data-float' );
				firstSocialPanel.clone().appendTo( '.nc_wrapper' );
				$( '.nc_wrapper' ).hide().addClass( position );
				var width = firstSocialPanel.outerWidth( true );
				var offset = firstSocialPanel.offset();
				$( '.nc_socialPanel' ).last().addClass( 'nc_floater' ).css({
					width: width,
					left: ( alignment == 'center' ? 0 : offset.left )
				});
				$( '.nc_socialPanel .swp_count' ).css({ transition: 'padding .1s linear' });
				$( '.nc_socialPanel' ).eq( 0 ).addClass( 'swp_one' );
				$( '.nc_socialPanel' ).eq( 2 ).addClass( 'swp_two' );
				$( '.nc_socialPanel' ).eq( 1 ).addClass( 'swp_three' );
				window.origSets['float'] = window.origSets[index];
				socialWarfarePlugin.setWidths();
			}
		}
	}

	function floatingBarReveal() {
		// Adjust the floating bar
		var panels = $( '.nc_socialPanel' );
		var floatOption = panels.not( '[data-float="float_ignore"]' ).eq( 0 ).attr( 'data-float' );
		var windowElement = $( window );
		var windowHeight = windowElement.height();
		var ncWrapper = $( '.nc_wrapper' );
		var ncSideFloater = $( '.nc_socialPanelSide' ).filter( ':not(.mobile)' );
		var position = $( '.nc_socialPanel' ).attr( 'data-position' );
		var minWidth = ncSideFloater.attr( 'data-screen-width' );
		var offsetOne = panels.eq( 0 ).offset();
		var scrollPos = windowElement.scrollTop();
		var st = $( window ).scrollTop();
		if ( typeof window.swpOffsets == 'undefined' ) {
			window.swpOffsets = {};
		}

		var visible = false;

		if ( floatOption == 'floatBottom' || floatOption == 'floatTop' ) {
			$( '.nc_socialPanel' ).not( '.nc_socialPanelSide, .nc_wrapper .nc_socialPanel' ).each(function() {
				var thisOffset, thisHeight, screenBottom;

				var index = $( '.nc_socialPanel' ).index( $( this ) );

				// Fetch our base numbers
				if ( typeof window.swpOffsets[index] == 'undefined' ) {
					thisOffset   = $( this ).offset();
					thisHeight   = $( this ).height();
					screenBottom = thisOffset + thisHeight;
					window.swpOffsets[index] = thisOffset;
				} else {
					thisOffset   = window.swpOffsets[index];
					thisHeight   = $( this ).height();
					screenBottom = thisOffset + thisHeight;
				}

				// Check if it's visible
				if ( thisOffset.top + thisHeight > scrollPos && thisOffset.top < scrollPos + windowHeight ) {
					visible = true;
				}
			});
			if ( visible ) {
				// Hide the Floating bar
				ncWrapper.hide();

				// Add some padding to the page so it fits nicely at the top or bottom
				if ( floatOption == 'floatBottom' ) {
					$( 'body' ).animate({ 'padding-bottom': window.body_padding_bottom + 'px' }, 0 );
				} else if ( floatOption == 'floatTop' ) {
					$( 'body' ).animate({ 'padding-top': window.body_padding_top + 'px' }, 0 );
				}
			} else {
				var newPadding, firstOffset;
				// Show the floating bar
				ncWrapper.show();

				// Add some padding to the page so it fits nicely at the top or bottom
				if ( floatOption == 'floatBottom' ) {
					newPadding = window.body_padding_bottom + 50;
					$( 'body' ).animate({ 'padding-bottom': newPadding + 'px' }, 0 );
				} else if ( floatOption == 'floatTop' ) {
					firstOffset = $( '.nc_socialPanel' ).not( '.nc_socialPanelSide, .nc_wrapper .nc_socialPanel' ).first().offset();
					console.log( firstOffset );
					if ( firstOffset.top > scrollPos + windowHeight ) {
						newPadding = window.body_padding_top + 50;
						$( 'body' ).animate({ 'padding-top': newPadding + 'px' }, 0 );
					}
				}
			}
		} else if ( floatOption == 'floatLeft' ) {
			visible = false;
			if ( $( '.nc_socialPanel' ).not( '.nc_socialPanelSide' ).length ) {
				$( '.nc_socialPanel' ).not( '.nc_socialPanelSide' ).each(function() {
						var thisOffset = $( this ).offset();
						var thisHeight = $( this ).height();
						if ( thisOffset.top + thisHeight > scrollPos && thisOffset.top < scrollPos + windowHeight ) {
							visible = true;
						}
					});
				if ( visible || $( '.nc_socialPanelSide' ).hasClass( 'mobile' ) ) {
					visible = true;
				} else {
					visible = false;
				}
			} else {
				if ( $( window ).width() > minWidth ) {
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
		}

		var lst = st;
	}

	// Twitter Shares Count
	function floatingBar() {
		$( window ).on( 'scroll', function() {
			floatingBarReveal();
		});
	}

	function applyScale() {
		$( '.nc_socialPanel' ).each( function() {
			$( this ).css({ width: '100%' });
			var width = $( this ).width();
			var scale = $( this ).attr( 'data-scale' );
			var align = $( this ).attr( 'data-align' );
			var newWidth;
			if ( ( align == 'fullWidth' && scale != 1 ) || scale > 1 || $( this ).hasClass( 'nc_socialPanelSide' ) ) {
				newWidth = width / scale;
				$( this ).css( 'cssText', 'width:' + newWidth + 'px!important;' );
				$( this ).css({
					transform: 'scale(' + scale + ')',
					'transform-origin': 'left'
				});
			} else if ( align != 'fullWidth' && scale < 1 ) {
				newWidth = width / scale;
				$( this ).css({
					transform: 'scale(' + scale + ')',
					'transform-origin': align
				});
			}
		});
	}

	function initShareButtons() {
		if ( $( '.nc_socialPanel' ).length ) {
			applyScale();
			$.when(
				socialWarfarePlugin.setWidths( true )
			).done(function() {
				setTimeout( function() {
					socialWarfarePlugin.setWidths( true, false, true );
				}, 200 );
			});
			createFloatBar();
			$( window ).scrollTop();
			floatingBar();
			floatingBarReveal();
			socialWarfarePlugin.activateHoverStates();
		}
	}

	/****************************************************************************

		Pin It Hover Effect

	****************************************************************************/

	function pinitButton() {
		var defaults = {
			wrap: '<span class="sw-pinit"/>',
			pageURL: document.URL
		};

		var options = $.extend( defaults, options );
		var o = options;

		//Iterate over the current set of matched elements
		$( '.swp-content-locator' ).parent().find( 'img' ).each( function() {
			var e = $( this ),
				pi_media = e.data( 'media' ) ? e.data( 'media' ) : e[0].src,
				pi_url = o.pageURL,
				pi_desc = e.attr( 'title' ) ? encodeURIComponent( e.attr( 'title' ) ) : encodeURIComponent( e.attr( 'alt' ) ),
				pi_isvideo = 'false';
			var bookmark = 'http://pinterest.com/pin/create/bookmarklet/?media=' + encodeURI( pi_media ) + '&url=' + encodeURI( pi_url ) + '&is_video=' + encodeURI( pi_isvideo ) + '&description=' + pi_desc;
			var css = $( this ).css([ 'float','margin','padding','height','width' ]);

			var eHeight = e.outerHeight();
			var eWidth = e.outerWidth();

			if ( eHeight >= sw_pinit_min_height && eWidth >= sw_pinit_min_width ) {
				e.wrap( o.wrap );
				e.parent( '.sw-pinit' ).css( css ).css({ display: 'block' });
				e.css({ margin: 0 });
				e.before( '<span class="sw-pinit-overlay" style="height: ' + eHeight + 'px"><a href="' + bookmark + '" class="sw-pinit-button sw-pinit-' + swp_pinit_v_location + ' sw-pinit-' + swp_pinit_h_location + '">Save</a></span>' );
				e.css({ position: 'absolute' });

				$( '.sw-pinit .sw-pinit-button' ).on( 'click', function() {
					window.open( $( this ).attr( 'href' ), 'Pinterest', 'width=632,height=253,status=0,toolbar=0,menubar=0,location=1,scrollbars=1' );
					return false;
				});

				$( '.sw-pinit' ).mouseenter(function() {
					$( this ).children( '.sw-pinit-overlay' ).show();
				}).mouseleave(function() {
					$( this ).children( '.sw-pinit-overlay' ).hide();
				});
			}
		});
	}

	$( document ).ready( function() {
		$( document ).on( 'click', '.nc_tweet, a.swp_CTT', function( event ) {
			if ( $( this ).hasClass( 'noPop' ) || ! $( this ).attr( 'data-link' ) ) {} else {
				event.preventDefault ? event.preventDefault() : ( event.returnValue = false );
				href = $( this ).attr( 'data-link' );
				href = href.replace( '’', '\'' );
				if ( $( this ).hasClass( 'pinterest' ) || $( this ).hasClass( 'buffer_link' ) || $( this ).hasClass( 'flipboard' ) ) {
					height = 550;
					width = 775;
				} else {
					height = 270;
					width = 500;
				}

				instance = window.open( href, '_blank', 'height=' + height + ',width=' + width );

				return false;
			}
		});

		// Fetch the padding amount to make space later for the floating bars
		window.body_padding_top = absint( $( 'body' ).css( 'padding-top' ).replace( 'px', '' ) );
		window.body_padding_bottom = absint( $( 'body' ).css( 'padding-bottom' ).replace( 'px', '' ) );

		$( window ).resize(function() {
			if ( $( '.nc_socialPanel' ).length && $( '.nc_socialPanel:hover' ).length !== 0 ) { } else {
				setTimeout( function() {
					window.swpAdjust = 1;
					initShareButtons();
				}, 100 );
			}
		});

		$( document.body ).on( 'post-load', function() {
			setTimeout( function() {
				initShareButtons();
			}, 100 );
		});

		if ( $( '.nc_socialPanelSide' ).length ) {
			var buttonsHeight = $( '.nc_socialPanelSide' ).height();
			var windowHeight = $( window ).height();
			var newPosition = absint( ( windowHeight / 2 ) - ( buttonsHeight / 2 ) );
			setTimeout( function() {
				$( '.nc_socialPanelSide' ).animate({ top: newPosition }, 0 ); console.log( newPosition );
			}, 105 );
		}

		setTimeout( function() {
			initShareButtons();
		}, 100 );

		// Reset the cache
		if ( 'undefined' !== typeof swpCacheURL ) {
			var urlParams;

			// If the URL Contains a question mark already
			if ( swpCacheURL.indexOf( '?' ) != -1 ) {
				urlParams = '&swp_cache=rebuild';
			} else {
				urlParams = '?swp_cache=rebuild';
			}

			$.get( swpCacheURL + urlParams );
		}

		setTimeout( function() {
			if ( typeof swp_pinit != 'undefined' && swp_pinit == true ) {
				pinitButton();
			}
		}, 500 );
	});
})( this, jQuery );
