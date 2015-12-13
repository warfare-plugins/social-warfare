(function(jQuery) {
    var uniqueCntr = 0;
    jQuery.fn.scrolled = function (waitTime, fn) {
        if (typeof waitTime === "function") {
            fn = waitTime;
            waitTime = 500;
        };
        var tag = "scrollTimer" + uniqueCntr++;
        this.scroll(function () {
            var self = jQuery(this);
            var timer = self.data(tag);
            if (timer) {
                clearTimeout(timer);
            };
            timer = setTimeout(function () {
                self.removeData(tag);
                fn.call(self[0]);
            }, waitTime);
            self.data(tag, timer);
        });
    };
})(jQuery);
jQuery(document).on('click','.nc_tweet, a.sw_CTT',function(event) {
	if(jQuery(this).hasClass('noPop') || !jQuery(this).attr("data-link")) {} else {
		event.preventDefault();
		href = jQuery(this).attr("data-link");
		href = href.replace("’","'");
		if (jQuery(this).hasClass("pinterest"))
		{
			height = 550;
			width = 700;
		} else {
			height = 270;
			width = 500;
		};
		instance = window.open(href, "_blank", "height=" + height + ",width=" + width);
		// instance.document.write("<meta http-equiv=\"refresh\" content=\"0;url="+href+"\">");
		// instance.document.close();
		return false;
	};
});
function isOdd(num) { return num % 2;} 
jQuery.fn.outerHTML = function(s) { return s ? this.before(s).remove() : jQuery("<p>").append(this.eq(0).clone()).html(); };
function swSetWidths(resize) {
	if(typeof window.origSets === 'undefined' || resize) {
		window.origSets = new Array();
		jQuery('.nc_socialPanel:not(.nc_socialPanelSide)').each( function() {
			var totalWidth 	= jQuery(this).width() - 2;
			var totalElements	= jQuery(this).attr('data-count');
			var average = parseInt(totalWidth) / parseInt(totalElements);
			var space = parseInt(totalWidth) - parseInt(totalElements);
			var offset = jQuery('.nc_socialPanel:not(.nc_socialPanelSide)').offset();
			if(offset.left < 100) {
				jQuery('.nc_socialPanelSide').addClass('mobile');
			} else {
				jQuery('.nc_socialPanelSide').removeClass('mobile');
			};
			var widthNeeded = 0;
			var padding = 0;
			var totesWidth = 0;
			jQuery(this).find('.nc_tweetContainer').each( function() {
				if(totalElements > 3) {
					extraSpace = (totalElements - 1) * 5;
				} else {
					extraSpace = (totalElements - 1) * 15;
				};
				widthNeeded += jQuery(this).width() + extraSpace;
				var paddingLeft = jQuery(this).find('.sw_count').css('padding-left');
				paddingLeft = parseInt(paddingLeft.replace('px',''));
				var paddingRight = jQuery(this).find('.sw_count').css('padding-right');
				paddingRight = parseInt(paddingRight.replace('px',''));
				padding = paddingLeft + paddingRight;
				widthNeeded = widthNeeded - padding;
			});
			totesWidth = jQuery(this).find('.nc_tweetContainer.totes').width();
			if(totalWidth < widthNeeded && !jQuery(this).hasClass('nc_floater')) {
				jQuery(this).find('.nc_tweetContainer.totes').hide();
			}
			if(totalWidth < (widthNeeded - totesWidth) && !jQuery(this).hasClass('nc_floater')) {
				if(jQuery(this).find('.totes').length) {
					if(jQuery(this).hasClass('connected')) {
						var average = (parseInt(totalWidth) / (parseInt(totalElements) - 1));
					} else {
						var average = (parseInt(totalWidth) / (parseInt(totalElements) - 1)) - 10;
					};
					var oddball = average * (totalElements - 1);
				} else {
					
					if(jQuery(this).hasClass('connected')) {
						var average = (parseInt(totalWidth) / (parseInt(totalElements)));
					} else {
						var average = (parseInt(totalWidth) / (parseInt(totalElements))) - 11;
					};
					var oddball = average * totalElements;
				};
				var oddball = totalWidth - oddball;
				jQuery(this).addClass('mobile').removeClass('notMobile');
				jQuery('.spaceManWilly').css({'width':'auto'});
				buttonWidths = 0;
				if(!jQuery('.sw_count .iconFiller').length) {
					jQuery(this).find('.nc_tweetContainer.totes,.nc_tweetContainer .sw_count').hide();
				} else {
					jQuery(this).find('.nc_tweetContainer.totes').hide();	
				};
				jQuery(this).find('.nc_tweetContainer').each(function() {
					width = jQuery(this).find('.iconFiller').width();
					if(isOdd(average)) {
						marginLeft = Math.floor((average - width) / 2) - 1;	
						marginRight = Math.floor((average - width) / 2) - 1;
					} else {
						marginLeft = ((average - width) / 2) - 1;
						marginRight = ((average - width) / 2) - 1;
					};
					jQuery(this).find('.iconFiller').css({'margin-left':marginLeft+'px','margin-right':marginRight+'px'});
				});
			// jQuery('.nc_tweetContainer').css({"padding-left":"4px","padding-right":"4px"});
			} else {
				
				jQuery(this).addClass('notMobile').removeClass('mobile');
				if(totalWidth > widthNeeded) {
					jQuery(this).find('.nc_tweetContainer.totes,.nc_tweetContainer .sw_count').show();
				}
				jQuery(this).find('.nc_tweetContainer .iconFiller').css({'margin-left':'0px','margin-right':'0px'});
				var average = Math.floor(average);
				var oddball = average * totalElements;
				var oddball = totalWidth - oddball;
				if(jQuery(this).find('.totesalt').length) {
					var totes = jQuery(this).find('.totes:visible').outerWidth(true);
					newTotalWidth = totalWidth - totes;
					average = parseInt(newTotalWidth) / parseInt(totalElements - 1);
					average = Math.floor(average);
					oddball = average * (totalElements - 1);
					oddball = newTotalWidth - oddball;
				} else {
					var totes = jQuery(this).find('.totes:visible').outerWidth(true);
					if(totes > average) {
						newTotalWidth = totalWidth - totes;
						average = parseInt(newTotalWidth) / parseInt(totalElements - 1);
						average = Math.floor(average);
						oddball = average * (totalElements - 1);
						oddball = newTotalWidth - oddball;
					};
				}
				count = 0;
				index = jQuery('.nc_socialPanel').index(jQuery(this));
				window.origSets[index] = new Array();
				if(jQuery(this).hasClass('nc_floater')) {
					// If this is the floating bar, don't size it independently. Just clone the settings from the other one.
					var firstSocialPanel = jQuery('.nc_socialPanel').first();
					jQuery(this).replaceWith(firstSocialPanel.outerHTML());
					width = firstSocialPanel.outerWidth(true);
					offset = firstSocialPanel.offset();
					jQuery('.nc_socialPanel').last().addClass('nc_floater').css(
						{
							'width':width,
							'left':offset.left
						});
					activateHoverStates();
					window.origSets['float'] = window.origSets[0];
				} else {
					jQuery(this).find('.nc_tweetContainer').not('.totesalt').each(function() {
						
				
						icon 		= jQuery(this).find('i.sw').outerWidth() + 14;
						shareTerm 	= jQuery(this).find('.sw_share').outerWidth();
						tote		= icon + shareTerm + 3;
						jQuery(this).find('.spaceManWilly').css({'width':tote+'px'});
						
						++count; 
						var paddingLeft = jQuery(this).find('.sw_count').css('padding-left');
						paddingLeft = parseInt(paddingLeft.replace('px',''));
						var paddingRight = jQuery(this).find('.sw_count').css('padding-right');
						paddingRight = parseInt(paddingRight.replace('px',''));
						dataId = jQuery(this).attr('data-id');
						dataId = parseInt(dataId);
						if(count > totalElements) {count = 1;}
						if(count <= oddball){add = 1;} else {add = 2;};
						curWidth = jQuery(this).outerWidth(true);
						curWidth = curWidth - paddingLeft;
						curWidth = curWidth - paddingRight;
						dif = average - curWidth;
						window.origSets[index][dataId] = new Array();
						if(isOdd(dif)){ 
							dif = dif - 1;
							dif = dif / 2;
							pl = dif+1+average;
							pr = dif+average;
							window.origSets[index][dataId]['pl'] = dif+1+'px';
							window.origSets[index][dataId]['pr'] = dif+'px';
							window.origSets[index][dataId]['fil'] = jQuery(this).find('.iconFiller').width()+'px';
							jQuery(this).find('.sw_count').animate({
								"padding-left": window.origSets[index][dataId]['pl'],
								"padding-right": window.origSets[index][dataId]['pr']
								}, 0, "linear", function() {
									jQuery(this).css({transition : 'padding .1s linear'
								});		
							});
						} else {
							dif = dif / 2;
							pl = dif+average;
							pr = dif+average;
							window.origSets[index][dataId]['pl'] = dif+'px';
							window.origSets[index][dataId]['pr'] = dif+'px';
							window.origSets[index][dataId]['fil'] = jQuery(this).find('.iconFiller').width()+'px';
							jQuery(this).find('.sw_count').animate({
								"padding-left": window.origSets[index][dataId]['pl'],
								"padding-right": window.origSets[index][dataId]['pr']
								}, 0, "linear", function() {	
									jQuery(this).css({transition : 'padding .1s linear'});
								});	
						};
					window.resized = true;
					}); 
				};
			};
		});
	} else {
		jQuery('.nc_tweetContainer').not('.totesalt').each(function() {
			if(jQuery(this).parents('.nc_wrapper').length) {
				index = 'float';
			} else {	
				index = jQuery('.nc_socialPanel').index(jQuery(this).parent('.nc_socialPanel'));
			};
			dataId = parseInt(jQuery(this).attr('data-id'));
			if(typeof window.origSets[index] === 'undefined') { } else {
				jQuery(this).find('.iconFiller').css({"width":window.origSets[index][dataId]['fil']});
				jQuery(this).find('.sw_count').css({
					"padding-left": window.origSets[index][dataId]['pl'],
					"padding-right":  window.origSets[index][dataId]['pr']
				});
			};
		});
	};
};

function createFloatBar() {
	if(!jQuery('.nc_wrapper .nc_socialPanel').length && !jQuery('.nc_socialPanelSide').length) {
		var firstSocialPanel = jQuery('.nc_socialPanel').first();
		var floatOption = firstSocialPanel.attr('data-float');
		if(floatOption) {
			backgroundColor = jQuery('.nc_socialPanel').attr('data-floatColor');
			jQuery('<div class="nc_wrapper" style="background-color:'+backgroundColor+'"></div>').appendTo('body');
			position = firstSocialPanel.attr('data-float');
			firstSocialPanel.clone().appendTo('.nc_wrapper');
			jQuery('.nc_wrapper').hide().addClass(position);
			width = firstSocialPanel.outerWidth(true);
			offset = firstSocialPanel.offset();
			jQuery('.nc_socialPanel').last().addClass('nc_floater').css(
				{
					'width':width,
					'left':offset.left
				});
			jQuery('.nc_socialPanel .sw_count').css({transition : 'padding .1s linear'});
			jQuery('.nc_socialPanel').eq(0).addClass('sw_one');
			jQuery('.nc_socialPanel').eq(2).addClass('sw_two');
			jQuery('.nc_socialPanel').eq(1).addClass('sw_three');
			if(floatOption == 'floatBottom') {
				jQuery('body').animate({'padding-bottom': '+=50px'}, 0);
			};
			window.origSets['float'] = window.origSets[0];
			swSetWidths();
		};
	}
};
// Format Number functions
function ReplaceNumberWithCommas(nStr) {
  nStr += '';
  x = nStr.split('.');
  x1 = x[0];
  x2 = x.length > 1 ? '.' + x[1] : '';
  var rgx = /(\d+)(\d{3})/;
  while (rgx.test(x1)) {
    x1 = x1.replace(rgx, '$1' + ',' + '$2');
  };
  return x1 + x2;
};
function number_format( val ) {
    if( val < 1000 ){ 
	 	return ReplaceNumberWithCommas(val);
	 } else { 
	 	val = val/1000; 
		val = Math.round(val);
		return ReplaceNumberWithCommas(val)+'K';
	 };
};
// Twitter Shares Count

function floatingBar() {
	jQuery(window).on('scroll', function() {
		floatingBarReveal();
	});
};

function floatingBarReveal() {	
		// Adjust the floating bar
		var panels = jQuery('.nc_socialPanel');
		var floatOption = panels.eq(0).attr('data-float');
		var windowElement = jQuery(window);
		var windowHeight = windowElement.height();
		var ncWrapper = jQuery('.nc_wrapper');
		var ncSideFloater = jQuery('.nc_socialPanelSide').filter(':not(.mobile)');
		var position = jQuery('.nc_socialPanel').attr('data-position');
		offsetOne = panels.eq(0).offset();
		scrollPos = windowElement.scrollTop();
		var st = jQuery(window).scrollTop();
		if(floatOption == 'floatBottom') {
			if(position == 'both') {
				offsetTwo = panels.eq(1).offset();
				if(offsetOne.top < (scrollPos - 50) && offsetTwo.top > (scrollPos + windowHeight - 50) && st >= lst) {
					ncWrapper.slideDown();
				} else if(offsetOne.top < (scrollPos - 50) && offsetTwo.top > (scrollPos + windowHeight - 50) && st <= lst) {
					ncWrapper.show();
				} else if(offsetTwo.top < (scrollPos + (windowHeight / 2)) && offsetTwo.top > scrollPos ){
					ncWrapper.slideUp();
				} else if(offsetTwo.top < (scrollPos - 50)) {
					ncWrapper.slideDown();
				} else if(offsetTwo.top < (scrollPos + windowHeight - 50) && st > lst) {
					ncWrapper.hide();
				} else {
					ncWrapper.slideUp();
				};
			} else if(position == 'above') {
				if(offsetOne.top < (scrollPos - 60)) {
					ncWrapper.slideDown();	
				} else {
					ncWrapper.slideUp();
				};
			} else if(position == 'below') {	
				if(offsetOne.top > (scrollPos + windowHeight - 60)) {
					ncWrapper.filter(":hidden").show();
				} else if (offsetOne.top < (scrollPos - 60)) {
					ncWrapper.filter(":hidden").slideDown();
				} else if(offsetOne.top < scrollPos + (windowHeight / 2) ){
					ncWrapper.slideUp();
				} else {
					ncWrapper.hide();
				};
			};
		} else if(floatOption == 'floatTop') {
			if(position == 'both') {
				offsetTwo = panels.eq(1).offset();
				if(offsetOne.top < (scrollPos) && offsetTwo.top > (scrollPos + windowHeight) && st >= lst) {
					ncWrapper.show(); 
				} else if(offsetOne.top < (scrollPos) && offsetTwo.top > (scrollPos + windowHeight) && st <= lst) {
					ncWrapper.slideDown();
				} else if(offsetTwo.top < (scrollPos + (windowHeight / 2)) && offsetTwo.top > scrollPos ){
					ncWrapper.hide();
				} else if(offsetTwo.top < (scrollPos)) {
					ncWrapper.show();
				} else if(offsetTwo.top < (scrollPos + windowHeight) && st > lst) {
					ncWrapper.slideUp();
				} else {
					ncWrapper.hide();
				};
			} else if(position == 'above') {
				if(offsetOne.top < (scrollPos)) {
					ncWrapper.show();	
				} else {
					ncWrapper.hide();
				};
			} else if(position == 'below') {	
				if(offsetOne.top > (scrollPos + windowHeight)) {
					ncWrapper.filter(":hidden").slideDown();
				} else if (offsetOne.top < (scrollPos)) {
					ncWrapper.filter(":hidden").show();
				} else if(offsetOne.top < scrollPos + (windowHeight / 2) ){
					ncWrapper.hide();
				} else {
					ncWrapper.slideUp();
				};
			};
		} else if(floatOption == 'floatLeft') {
			var visible = false;
			jQuery('.nc_socialPanel').not('.nc_socialPanelSide').each(function() {
				var thisOffset = jQuery(this).offset();
				var thisHeight = jQuery(this).height();
				if(thisOffset.top + thisHeight > scrollPos && thisOffset.top < scrollPos + windowHeight) { visible = true; };
			});
			if(visible || jQuery('.nc_socialPanelSide').hasClass('mobile')) {
				ncSideFloater.removeClass('displayed');
			} else {
				ncSideFloater.addClass('displayed');
			};
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
	jQuery('.nc_tweetContainer').not('.totesalt, .nc_socialPanelSide .nc_tweetContainer').on("mouseenter",
		function() {
			if(!jQuery(this).parents('.nc_socialPanel').hasClass('mobile')) {
				thisElem 	= jQuery(this);
				icon 		= thisElem.find('.iconFiller').width();
				shareTerm 	= thisElem.find('.sw_share').outerWidth();
				wrapper		= thisElem.find('.spaceManWilly').outerWidth();
				tote		= wrapper;
				dif			= wrapper - icon;
				origDif		= dif;
				orig		= parseInt(tote) - parseInt(dif);
				ele			= jQuery('.nc_socialPanel').attr('data-count');
				if(jQuery(this).siblings('.totes').length) {
					average 	= (parseInt(dif) / ((parseInt(ele)-2)));
					average 	= Math.floor(average);
					oddball 	= dif % (ele - 2);
				} else {
					average 	= (parseInt(dif) / ((parseInt(ele)-1)));
					average 	= Math.floor(average);
					oddball 	= dif % (ele - 1);
				};
				if(jQuery(this).parents('.nc_wrapper').length) {
					index = 'float';
				} else {
					index = jQuery('.nc_socialPanel').index(jQuery(this).parent('.nc_socialPanel'));
				};
				dataId = parseInt(jQuery(this).attr('data-id'));
				jQuery(this).find('.iconFiller').css({ "width":wrapper });
				pl = window.origSets[index][dataId]['pl'];
				pr = window.origSets[index][dataId]['pr'];
				jQuery(this).find('.sw_count').css({
						"padding-left": window.origSets[index][dataId]['pl'],
						"padding-right": window.origSets[index][dataId]['pr']
					});
				dataId = jQuery(this).attr('data-id');
				count = 0;
				if(jQuery(this).hasClass('totes')) {
					jQuery(this).siblings('.nc_tweetContainer').each(function() {
						dataId = parseInt(jQuery(this).attr('data-id'));
						jQuery(this).find('.iconFiller').css({"width":window.origSets[index][dataId]['fil']});
						jQuery(this).find('.sw_count').css({
							"padding-left": window.origSets[index][dataId]['pl'],
							"padding-right": window.origSets[index][dataId]['pr']
							});
					});
				} else {
					jQuery(this).siblings('.nc_tweetContainer').not('.totes').each(function() {
						++count;
						if(count <= oddball) {  ave = average + 1;
						} else { ave = average; };
						dataId = parseInt(jQuery(this).attr('data-id'));
						if(isOdd(ave)){
							offsetL = (((ave - 1) / 2) +1);
							offsetR = ((ave - 1) / 2);
							pl = parseInt(window.origSets[index][dataId]['pl']) - offsetL;
							pr = parseInt(window.origSets[index][dataId]['pr']) - offsetR;
						} else {
	
							offsetL = (ave / 2);
							offsetR = (ave / 2 );
							pl = parseInt(window.origSets[index][dataId]['pl']) - offsetL;
							pr = parseInt(window.origSets[index][dataId]['pr']) - offsetR;
						};
						
						jQuery(this).find('.iconFiller').css({"width":origSets[index][dataId]['fil']});
						jQuery(this).find('.sw_count').css({
							"padding-left": pl +"px",
							"padding-right": pr +"px"
						});
					});
				};
			};
		}
	);
	jQuery('.nc_socialPanel').on("mouseleave", function() {
		if(!jQuery(this).hasClass('mobile')) {
			swSetWidths();
		};
	});
	jQuery('.nc_fade .nc_tweetContainer').on("mouseenter", function() {
		jQuery(this).css({"opacity": 1 }).siblings('.nc_tweetContainer').css({"opacity": 0.5 });
	});
	jQuery('.nc_fade').on("mouseleave", 
	function() {
		jQuery('.nc_fade .nc_tweetContainer').css({"opacity": 1 });
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
	jQuery('.totes .sw_count').text(number_format(total)+' SHARES');
}
*/
function swApplyScale() {
	jQuery('.nc_socialPanel').each( function() {
		var width = jQuery(this).width();
		var scale = jQuery(this).attr('data-scale');
		var align = jQuery(this).attr('data-align');
		if((align == 'fullWidth' && scale != 1) || scale > 1) {
			newWidth = width / scale;
			jQuery(this).css('cssText', 'width:'+newWidth+'px!important;');
			jQuery(this).css({
				'transform':'scale('+scale+')',
				'transform-origin':'left'	
			});
		} else if(align != 'fullWidth' && scale < 1) {
			newWidth = width / scale;
			jQuery(this).css({
				'transform':'scale('+scale+')',
				'transform-origin':align	
			});
		}
	});
}

function getShares() {
		
	if(jQuery('.nc_socialPanel').length) {
		var checkVisible = setInterval(function() {
			if(jQuery('.nc_socialPanel:visible')) {
				clearInterval(checkVisible);
				swApplyScale();
				swSetWidths();
				createFloatBar();
				lst = jQuery(window).scrollTop();
				floatingBar();
				floatingBarReveal();
				activateHoverStates();
			}
		} , 250 );
	}
}

jQuery(document).ready(function() {
	jQuery(window).resize(function() {
		if(jQuery('.nc_socialPanel:hover').length) { } else {
			swSetWidths(true);
			floatingBarReveal();
		};
	});
	
	if(jQuery('.nc_socialPanelSide').length) {
		var buttonsHeight = jQuery('.nc_socialPanelSide').height();
		var windowHeight = jQuery(window).height();
		var newPosition = (windowHeight / 2) - (buttonsHeight / 2);
		jQuery('.nc_socialPanelSide').css({'top':newPosition+'px'});
	}

	getShares();

	// Reset the cache
	if (typeof sw_cache_url != 'undefined') { 
		
		// If the URL Contains a question mark already
		if (sw_cache_url.indexOf('?') != -1) {
			var url_params = '&sw_cache=rebuild';
			
		// If the URL does not contain a question mark already
		} else {
			var url_params = '?sw_cache=rebuild';
		}
		jQuery.get(sw_cache_url + url_params); 
	}
});