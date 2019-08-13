jQuery('document').ready(function(){

	// -------------------------------------------
	// Cookie Expiration Dates
	// -------------------------------------------
	var hideBarExpiration = new Date();
	hideBarExpiration.setDate(hideBarExpiration.getDate()+14);
	var cookieExpiration = new Date();
	cookieExpiration.setDate(cookieExpiration.getDate()+182);

	// -------------------------------------------
	// News Ticker
	// -------------------------------------------
	jQuery('#newsticker').cycle({ fx: 'scrollDown', timeout: 5000 });


	// -------------------------------------------
	// Sticky Symbols - clickable
	// -------------------------------------------
	jQuery('#stickysymbol1').click(function(){
		window.location = "/quote.php?sym=" + jQuery('#stickysymbol1').html();
	});
	jQuery('#stickysymbol2').click(function(){
		window.location = "/quote.php?sym=" + jQuery('#stickysymbol2').html();
	});
	jQuery('#stickysymbol3').click(function(){
		window.location = "/quote.php?sym=" + jQuery('#stickysymbol3').html();
	});
	jQuery('#stickysymbol4').click(function(){
		window.location = "/quote.php?sym=" + jQuery('#stickysymbol4').html();
	});
	jQuery('#stickysymbol5').click(function(){
		window.location = "/quote.php?sym=" + jQuery('#stickysymbol5').html();
	});
	jQuery('#stickysymbol6').click(function(){
		window.location = "/quote.php?sym=" + jQuery('#stickysymbol6').html();
	});	

	// -------------------------------------------
	// Settings Box
	// -------------------------------------------
	jQuery('#stickyfootersettings').click(function(){
		jQuery('#stickysettingsbox').toggle();
		if (jQuery.cookies.get('stickygroup') == "topstocks") {
			for (sNum = 1; sNum <= 6; sNum++) {
				jQuery('#stickyfooter' + sNum).attr('disabled', 'disabled');
			}
		} else if (jQuery.cookies.get('stickygroup') == "futuremarkets") {
			for (sNum = 1; sNum <= 6; sNum++) {
				jQuery('#stickyfooter' + sNum).attr('disabled', 'disabled');
			}
		} else {
			for (sNum = 1; sNum <= 6; sNum++) {
				jQuery('#stickyfooter' + sNum).removeAttr('disabled');
			}
		}
	});
	
	jQuery('.stickygroup').change(function(){
		var opt = jQuery(this).val();
		if(opt == 'topstocks' || opt == 'futuremarkets'){
			for (sNum = 1; sNum <= 6; sNum++) {
				jQuery('#stickyfooter' + sNum).attr('disabled', 'disabled');
			}
		} else{
			for (sNum = 1; sNum <= 6; sNum++) {
				jQuery('#stickyfooter' + sNum).removeAttr('disabled');
			}
		}
	});	

	// Launch the sticky footer options when you click on a missing symbol
	jQuery('body').delegate(".entersymbol", "click", function(){
  		jQuery('#stickysettingsbox').show();
	});


	// -------------------------------------------
	// Minimize Button
	// -------------------------------------------
	jQuery('#stickyfooterminimize').click(function(){
		//jQuery('#dmgfooter').fadeOut();
		jQuery('#dmgfooter').animate({"bottom": "-60px"}, "fast");
		jQuery('#stickyfooteropen').show();
		jQuery('#stickyfooteropen').animate({"bottom": "0px"}, "slow");
		jQuery('#stickysettingsbox').hide();
		jQuery.cookies.set('sticky_footer_status', 'close', {domain: '.barchart.com', expiresAt: hideBarExpiration} );
		//jQuery.cookies.set('sticky_footer_status', 'close', {domain: '.barchart.com'} );
	});
	
	// -------------------------------------------
	// Maximize Button
	// -------------------------------------------
	jQuery('#stickyfooteropen').click(function(){
		// Hide arrow thing
		jQuery('#stickyfooteropen').animate({"bottom": "-32px"}, "fast");
		loadStickyFooter();
		jQuery('#dmgfooter').show();
		jQuery('#dmgfooter').animate({"bottom": "0px"}, "slow");
		jQuery.cookies.set('sticky_footer_status', 'open', {domain: '.barchart.com', expiresAt: hideBarExpiration} );
		//jQuery.cookies.set('sticky_footer_status', 'open', {domain: '.barchart.com'} );
	});	
	
	// -------------------------------------------
	// Close Button
	// -------------------------------------------
	jQuery('#stickyfooterclose').click(function(){
		
		if (confirm("Are you sure you want to close the Barchart Data Ticker?\nYou will need to clear your cookies to re-enable it.")) {
			jQuery('#dmgfooter').animate({"bottom": "-60px"}, "fast");
			jQuery('#stickysettingsbox').hide();
			jQuery.cookies.set('sticky_footer_status', 'exit', {domain: '.barchart.com', expiresAt: cookieExpiration} );
			//jQuery.cookies.set('sticky_footer_status', 'close', {domain: '.barchart.com'} );
		}
	});	


	// -------------------------------------------
	// Save Button
	// -------------------------------------------
	jQuery('#stickysave').click(function(){

		var sNum = 1;

		// Set the 6 cookies with the custom symbols
		for (sNum = 1; sNum <= 6; sNum++) {
			jQuery.cookies.set('stickyfooter' + sNum, (jQuery('#stickyfooter' + sNum).val() == '') ? 'blank' : jQuery('#stickyfooter' + sNum).val().toUpperCase(), {domain: '.barchart.com', expiresAt: cookieExpiration} );
		}

		// Set the cookie with the custom group
		jQuery.cookies.set('stickygroup', jQuery('input:radio[name=stickygroup]:checked').val(), {domain: '.barchart.com', expiresAt: cookieExpiration} );

		// Top Stocks
		if (jQuery.cookies.get('stickygroup') == "topstocks") {

			// Set symbol names on sticky footer
			for (sNum = 1; sNum <= 6; sNum++) {
				jQuery('#stickysymbol' + sNum).html(topstocks[sNum - 1]);
			}

			// Set prices on sticky footer
			var myStickySymbolList = topstocks[0] + "," + topstocks[1] + "," + topstocks[2] + "," + topstocks[3] + "," + topstocks[4] + "," + topstocks[5];
			jQuery.getJSON("/jsonquote.php", { sym: myStickySymbolList, html: "1" }, function(json) {
				sNum = 1;
				jQuery.each(json, function(i,item){
					jQuery('#stickysymbol' + sNum + 'price').html(item.last + ' ' + item.change);
					jQuery('#stickysymbol' + sNum++).attr('title', item.name);
				});
			});

		// Futures Markets
		} else if (jQuery.cookies.get('stickygroup') == "futuremarkets") {

			// Set symbol names on sticky footer
			for (sNum = 1; sNum <= 6; sNum++) {
				jQuery('#stickysymbol' + sNum).html(topfutures[sNum - 1]);
			}

			// Set prices on sticky footer
			var myStickySymbolList = topfutures[0] + "," + topfutures[1] + "," + topfutures[2] + "," + topfutures[3] + "," + topfutures[4] + "," + topfutures[5];
			jQuery.getJSON("/jsonquote.php", { sym: myStickySymbolList, html: "1" }, function(json) {
				sNum = 1;
				jQuery.each(json, function(i,item){
					jQuery('#stickysymbol' + sNum + 'price').html(item.last + ' ' + item.change);
					jQuery('#stickysymbol' + sNum).attr('title', item.name);
					jQuery('#stickysymbol' + sNum++).html(item.symbol);
				});
			});

		// Custom Symbols
		} else {

			// Set symbol names on sticky footer
			for (sNum = 1; sNum <= 6; sNum++) {
				jQuery('#stickysymbol' + sNum).html((jQuery.cookies.get('stickyfooter' + sNum) == 'blank') ? '' : jQuery.cookies.get('stickyfooter' + sNum));
			}

			// Remove all prices from sticky footer
			for (sNum = 1; sNum <= 6; sNum++) {
				jQuery('#stickysymbol' + sNum + 'price').html('');
			}

			// Set prices on sticky footer
			var myStickySymbolList = jQuery.cookies.get('stickyfooter1') + "," + jQuery.cookies.get('stickyfooter2') + "," + jQuery.cookies.get('stickyfooter3') + "," + jQuery.cookies.get('stickyfooter4') + "," + jQuery.cookies.get('stickyfooter5') + "," + jQuery.cookies.get('stickyfooter6');
			jQuery.getJSON("/jsonquote.php", { sym: myStickySymbolList, html: "1" }, function(json) {
				jQuery.each(json, function(i,item){
					
					if (item.name !== undefined) {
						jQuery('#stickysymbol' + item.callerID + 'price').html(item.last + ' ' + item.change);
						jQuery('#stickysymbol' + item.callerID).attr('title', item.name);
						jQuery('#stickysymbol' + item.callerID).html(item.symbol);
					}
					
					// Alert the user of bad symbols
					if (jQuery('#stickysymbol' + item.callerID + 'price').html() == '') {
						jQuery('#stickysymbol' + item.callerID + 'price').html('<span class="entersymbol">Enter Symbol</span>');
						if (jQuery('#stickyfooter' + item.callerID).val() != "") {
							alert (jQuery('#stickyfooter' + item.callerID).val() + " is not a valid symbol.");
							jQuery('#stickyfooter' + item.callerID).val('');
							jQuery('#stickysymbol' + item.callerID).html('');
							jQuery.cookies.set('stickyfooter' + item.callerID, 'blank', {domain: '.barchart.com', expiresAt: cookieExpiration} );
						}
					}		
				});
			});
			
			if (window.userId !== undefined) {
				jQuery.post("/saveStickyFooter.php", { id: window.userId, symbol1: jQuery('#stickysymbol1').html(), symbol2: jQuery('#stickysymbol2').html(), symbol3: jQuery('#stickysymbol3').html(), symbol4: jQuery('#stickysymbol4').html(), symbol5: jQuery('#stickysymbol5').html(), symbol6: jQuery('#stickysymbol6').html() },
				   function(data) {
				     //alert("Data Loaded: " + data);
				     //window.userId
				   });
			}

		}

		// Hide the settings box
		jQuery('#stickysettingsbox').hide();
	});



	// -------------------------------------------
	// Cancel Button - revert changes using cookie values
	// -------------------------------------------
	jQuery('#stickycancel').click(function(){
		var sNum = 1;

		jQuery("[name=stickygroup]").filter("[value=" + jQuery.cookies.get('stickygroup') + "]").attr("checked","checked");

		for (sNum = 1; sNum <= 6; sNum++) {
			jQuery('#stickyfooter' + sNum).val( (jQuery.cookies.get('stickyfooter' + sNum) == 'blank') ? '' : jQuery.cookies.get('stickyfooter' + sNum) );
		}
		jQuery('#stickysettingsbox').hide();
	});


	// -------------------------------------------
	// Show Sticky Footer
	// -------------------------------------------

	function loadStickyFooter() {
		var sNum = 1;

		// If there is no sticky group defined this means this is the first time seeing it
		// Set the group to Top Stocks and set all the cookies required
		if (jQuery.cookies.get('stickygroup') == null) {
			jQuery("[name=stickygroup]").filter("[value=topstocks]").attr("checked","checked");
			jQuery.cookies.set('stickyfooter1', '$DOWI', {domain: '.barchart.com', expiresAt: cookieExpiration} );
			jQuery.cookies.set('stickyfooter2', 'AAPL', {domain: '.barchart.com', expiresAt: cookieExpiration} );
			jQuery.cookies.set('stickyfooter3', 'GC*0', {domain: '.barchart.com', expiresAt: cookieExpiration} );
			jQuery.cookies.set('stickyfooter4', '$SPX', {domain: '.barchart.com', expiresAt: cookieExpiration} );
			jQuery.cookies.set('stickyfooter5', 'GOOG', {domain: '.barchart.com', expiresAt: cookieExpiration} );
			jQuery.cookies.set('stickyfooter6', 'ZC*0', {domain: '.barchart.com', expiresAt: cookieExpiration} );
			jQuery.cookies.set('stickygroup', jQuery('input:radio[name=stickygroup]:checked').val(), {domain: '.barchart.com', expiresAt: cookieExpiration} );
		}

		// Load the settings dialog with all the cookie information (group and custom symbols)
		jQuery("[name=stickygroup]").filter("[value=" + jQuery.cookies.get('stickygroup') + "]").attr("checked","checked");
		for (sNum = 1; sNum <= 6; sNum++) {
			jQuery('#stickyfooter' + sNum).val( (jQuery.cookies.get('stickyfooter' + sNum) == 'blank') ? '' : jQuery.cookies.get('stickyfooter' + sNum) );
		}

		// Top Stocks
		if (jQuery.cookies.get('stickygroup') == "topstocks") {

			// Set symbol names on sticky footer
			for (sNum = 1; sNum <= 6; sNum++) {
				jQuery('#stickysymbol' + sNum).html(topstocks[sNum - 1]);
			}

			// Set prices on sticky footer
			var myStickySymbolList = topstocks[0] + "," + topstocks[1] + "," + topstocks[2] + "," + topstocks[3] + "," + topstocks[4] + "," + topstocks[5];
			jQuery.getJSON("/jsonquote.php", { sym: myStickySymbolList, html: "1" }, function(json) {
				var sNum = 1;
				jQuery.each(json, function(i,item){
					jQuery('#stickysymbol' + sNum + 'price').html(item.last + ' ' + item.change);
					jQuery('#stickysymbol' + sNum++).attr('title', item.name);
				});
			});


		// Future Markets
		} else if (jQuery.cookies.get('stickygroup') == "futuremarkets") {

			// Set symbol names on sticky footer
			for (sNum = 1; sNum <= 6; sNum++) {
				jQuery('#stickysymbol' + sNum).html(topfutures[sNum - 1]);
			}

			// Set prices on sticky footer
			var myStickySymbolList = topfutures[0] + "," + topfutures[1] + "," + topfutures[2] + "," + topfutures[3] + "," + topfutures[4] + "," + topfutures[5];
			jQuery.getJSON("/jsonquote.php", { sym: myStickySymbolList, html: "1" }, function(json) {
				var sNum = 1;
				jQuery.each(json, function(i,item){
					jQuery('#stickysymbol' + sNum + 'price').html(item.last + ' ' + item.change);
					jQuery('#stickysymbol' + sNum).attr('title', item.name);
					jQuery('#stickysymbol' + sNum++).html(item.symbol);
				});
			});

		// Custom Symbols
		} else {

			// Set symbol names on sticky footer
			for (sNum = 1; sNum <= 6; sNum++) {
				jQuery('#stickysymbol' + sNum).html((jQuery.cookies.get('stickyfooter' + sNum) == 'blank') ? '' : jQuery.cookies.get('stickyfooter' + sNum));
			}

			// Remove all prices from sticky footer
			for (sNum = 1; sNum <= 6; sNum++) {
				jQuery('#stickysymbol' + sNum + 'price').html('');
			}

						// Set prices on sticky footer
						var myStickySymbolList = jQuery.cookies.get('stickyfooter1') + "," + jQuery.cookies.get('stickyfooter2') + "," + jQuery.cookies.get('stickyfooter3') + "," + jQuery.cookies.get('stickyfooter4') + "," + jQuery.cookies.get('stickyfooter5') + "," + jQuery.cookies.get('stickyfooter6');
						jQuery.getJSON("/jsonquote.php", { sym: myStickySymbolList, html: "1" }, function(json) {
							jQuery.each(json, function(i,item){
								
								if (item.name !== undefined) {
									jQuery('#stickysymbol' + item.callerID + 'price').html(item.last + ' ' + item.change);
									jQuery('#stickysymbol' + item.callerID).attr('title', item.name);
									jQuery('#stickysymbol' + item.callerID).html(item.symbol);
								}
								
								// Alert the user of bad symbols
								if (jQuery('#stickysymbol' + item.callerID + 'price').html() == '') {
									jQuery('#stickysymbol' + item.callerID + 'price').html('<span class="entersymbol">Enter Symbol</span>');
									if (jQuery('#stickyfooter' + item.callerID).val() != "") {
										alert (jQuery('#stickyfooter' + item.callerID).val() + " is not a valid symbol.");
										jQuery('#stickyfooter' + item.callerID).val('');
										jQuery('#stickysymbol' + item.callerID).html('');
										jQuery.cookies.set('stickyfooter' + item.callerID, 'blank', {domain: '.barchart.com', expiresAt: cookieExpiration} );
									}
								}					
			
							});
			});
		}
	}

		if (jQuery.cookies.get('sticky_footer_status') != 'exit') {
			if (jQuery.cookies.get('sticky_footer_status') != 'close') {
				loadStickyFooter();
				jQuery('#dmgfooter').show();
				jQuery('#dmgfooter').animate({"bottom": "0px"}, "slow");
			} else {
				jQuery('#stickyfooteropen').show();
				jQuery('#stickyfooteropen').animate({"bottom": "0px"}, "slow");
			}
		}
});