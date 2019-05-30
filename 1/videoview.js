jQuery(document).ready(function(){
	jQuery('.poster').live('click', function() {
		jQuery(this).hide();
		
		var player = jQuery(this).siblings('.player');
		var playerHtml = (player.html().replace("<!--", "")).replace("-->", "");
		player.html(playerHtml);
		player.show();
	});

	jQuery('.videoSelector .block').live('click', function() {
		vid = jQuery(this).attr('rel');
		jQuery('.videoPlayer .video.selected').hide().removeClass('selected').find('.player').hide().end().find('.poster').show();
		jQuery('.videoPlayer .video[rel="' + vid + '"]').hide().addClass('selected').fadeIn('slow');

		var posterImg = jQuery('.videoPlayer .video[rel="' + vid + '"]').find('.posterimg');
		posterImg.css('background', 'url(' + posterImg.attr('rel') + ')');

		jQuery('.videoSelector .block.selected').removeClass('selected');
		jQuery(this).addClass('selected');
		jQuery('.videoWidget .videoTitle span').hide().html(jQuery(this).attr('title')).fadeIn('slow');
	});

	var lastPage = jQuery('.videoSelector .page').length-1;
	jQuery('.videoSelector').on('click', '.pagebutton', function() {
		
		var dir, nextPage, thisPage;
		thisPage = jQuery('.videoSelector .page.selected');
		if(jQuery(this).hasClass('next')) {
			dir = '+';
			nextPage = thisPage.next();
		} else {
			dir = '-';
			nextPage = thisPage.prev();
		}
		
		if(nextPage.position()) {
			var off = thisPage.height();

		    jQuery(this).siblings('.blocks').animate({scrollTop: dir + '=' + off });
		    thisPage.removeClass('selected');
		    nextPage.addClass('selected');

		   	nextPage.find('.thumbimg').each(function() {
		   		jQuery(this).attr('src', jQuery(this).attr('rel'));
		   	});
		    
		    if(dir === '+' && nextPage.index() === 1)
		    	jQuery(this).siblings('.pagebutton.prev').hide().html('<div class="inner"></div>').fadeIn();
		    else if(dir === '-' && nextPage.index() === 0)
		    	jQuery(this).hide().html(jQuery(this).parents('.videoSelector').attr('rel')).fadeIn();

		    if(nextPage.index() === lastPage)
		    	jQuery(this).fadeOut();
		    else if(dir === '-' && nextPage.index() === (lastPage-1))
		    	jQuery(this).siblings('.pagebutton.next').fadeIn();
		}
	});

	jQuery('.videoScroller').on('click', '.pagebutton', function() {
		var dir, nextPage, thisPage;
		thisPage = jQuery(this).parents('.videoScroller').find('.page.selected');
		if(jQuery(this).hasClass('next')) {
			dir = '-';
			nextPage = thisPage.next();
		} else {
			dir = '+';
			nextPage = thisPage.prev();
		}

		if(nextPage.position()) {
			var off = thisPage.width();
			var pgSz = nextPage.children('.block').length;
			var min = nextPage.index() * pgSz + 1;
			var max = min + pgSz - 1;

			nextPage.find('img.thumbimg').each(function() {
		   		jQuery(this).attr('src', jQuery(this).attr('rel'));
		   	});

		    jQuery(this).parents('.head').siblings('.blocks').animate({left: dir + '=' + off });
		    thisPage.removeClass('selected');
		    nextPage.addClass('selected');
		    $pageText = jQuery(this).parents('.paging').children('.pagetext');
		    $pageText.children('span.min').text(min);
		    $pageText.children('span.max').text(max);
		}
	});

	var lastScroll = 0;
	jQuery('.videoSelector, .videoScroller').bind('mousewheel DOMMouseScroll', function(event) {
		event.preventDefault();
		if(scroll && event.timeStamp > (lastScroll+500)) {
			// scroll = false;
			var delta = event.originalEvent.wheelDelta || -event.originalEvent.detail;
			// alert(delta);
			if(delta < 0) {
				jQuery('.pagebutton.next', this).trigger('click');
			} else {
				jQuery('.pagebutton.prev', this).trigger('click');
			}
			lastScroll = event.timeStamp;
		}
	});
});

// For video.js player
// delete _V_.options.components.bigPlayButton; //remove play button or it will show on initial load

// var minFlash = "11.4.31";
// var fv = swfobject.getFlashPlayerVersion();
// var canPlay = false;
// var v = document.createElement('video');
// if(v.canPlayType && v.canPlayType('video/mp4').replace(/no/, ''))
// 	canPlay = true;
// if (navigator.userAgent.match(/Chrome/)) {
// 	_V_.options.techOrder = ["flash", "html5"];
// 	canPlay = false;
// }
// if(!canPlay && !swfobject.hasFlashPlayerVersion(minFlash)) {
// 	var ver = fv.major + "." + fv.minor + "." + fv.release;
// 	jQuery(document).ready(function() {
// 		jQuery('.bcvideo').html('<div class="vjs-no-flash">This video requires Adobe Flash ' + minFlash + ' or newer.<br />Get it here: <a href="http://get.adobe.com/flashplayer/">http://get.adobe.com/flashplayer/</a></div>');
// 		jQuery('.vjs-no-flash').each(function() {
// 			jQuery(this).css('position', 'absolute');
// 			jQuery(this).css('left', (jQuery(this).parent().innerWidth() - jQuery(this).outerWidth())/2 + 'px');
// 			jQuery(this).css('top', (jQuery(this).parent().innerHeight() - jQuery(this).outerHeight())/2 + 'px');
// 		});
// 	});
// }

// _V_.options.flash.swf = "http://js1.aws.barchart.com/videojs/video-js.swf?v=2";