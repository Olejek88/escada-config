function toggleHints(show) {
	if (show) {
		$$('.hintsHidden').each(function(item){
			item.removeClassName('hintsHidden');
			item.addClassName('hinted');
		});
	} else {
		$$('.hinted').each(function(item){
			item.removeClassName('hinted');
			item.addClassName('hintsHidden');
		});
	}
}

function closeAllDialogs() {
	$$('.pulldown, .dialog').each(function(item){
		item.hide();
	});
	toggleHints(true);
}

function undoDelete(target, postQuery) {
	closeAllDialogs();
	target.innerHTML = "<img src='/shared/images/throbber2.gif'>";
	new Ajax.Request('/inc/UserActionsBackend.php', {
		method: 'post',
		parameters: postQuery,
		onSuccess: function(transport) {
			target.replace(transport.responseText);
			updateAlertPickVoteUI();
		}
	});
}

function deletePick(target, pick, columns) {	// If in list, inList is columns in table
	closeAllDialogs();
	target.innerHTML = "<img src='/shared/images/throbber2.gif'>";
	new Ajax.Request('/inc/UserActionsBackend.php', {
		method: 'post',
		parameters: 'user=' + userId + '&deletePick=' + pick + '&columns=' + (columns ? columns : ''),
		onSuccess: function(transport) {
			$(target).replace(transport.responseText);
			updateAlertPickVoteUI();
		}
	});
}

function deleteAlert(target, alert, inList) {
	closeAllDialogs();
	new Effect.Opacity((inList ? target.previous() : target), {
		from: 1,
		to: 0,
		duration: 0.25,
		afterFinish: function() {
		new Ajax.Request('/inc/UserActionsBackend.php', {
			method: 'post',
			parameters: 'user=' + userId + '&deleteAlert=' + alert + '&undoable=' + (inList ? 1 : 0),
			onSuccess: function(transport) {
				if (inList)	{
					$(target).previous().remove();
				}
				$(target).update(transport.responseText);
				$$('.useraction.alert.menuButton').each(function(item){
					item.show();
				});
				updateAlertPickVoteUI();
			}
		});
}
	});
}

// JQuery version of deleting an alert
function deleteAlertJq(alert, success) {
	jQuery.ajax({
		url: 		'/inc/UserActionsBackend.php',
		type: 		'post',
		dataType: 	'json',
		data: 		{
			user: 			userId,
			removeAlert: 	true,
			alertId: 		alert
		},
		success: 	success
	});
}

function acknowledgeAlert(target, alertId, inList) {
	closeAllDialogs();
	new Ajax.Request('/inc/UserActionsBackend.php', {
		method: 'post',
		parameters: 'user=' + userId + '&acknowledgeAlert=' + alertId,
		onSuccess: function(transport) {
			if (inList)	{// Edit page
				Effect.Fade(target.previous(), {
					duration: 0.25
				});
				Effect.Fade(target, {
					duration: 0.25
				});
			} else {
				$(target).replace(transport.responseText);
			}
			updateAlertPickVoteUI();
		}
	});
}

function saveVote(target, symbol, action) {

	if (typeof action !== 'undefined' && typeof symbol !== 'undefined') {
	closeAllDialogs();
	var htmlID = 'saveVote'+Math.floor((Math.random()*100000)+1); 
		$(target).innerHTML = '<img src="/shared/images/throbber2.gif" id="'+htmlID+'" >';

	var post = {'symbol':symbol,'setVote':action,'user':userID}
	jQuery.ajax('/inc/UserActionsBackend.php',{
			type : 'POST',
			data : post, 
			success : function(data,textStatus,jqXHR){
				jQuery("#"+htmlID).hide().remove();
				var container = jQuery(target).closest('div.mpbox');
				jQuery(container).children().remove();
				jQuery(container).empty().html(data);

				if(jQuery.browser.msie === true && jQuery.browser.version === '7.0') {
					fixIEVoteSlider();
				}

				if( typeof('updateAlertPickVoteUI') == 'function'){
					updateAlertPickVoteUI();

				}
			},
			error : function(){
				jQuery("#"+htmlID).hide().remove();
				var container = jQuery(target).closest('div.mpbox');
				jQuery(container).children().remove();
				jQuery(container).empty().html('<p class="error" >An Error occurred.</p>');

				if( typeof('updateAlertPickVoteUI') == 'function'){
					updateAlertPickVoteUI();
				}
			}
			
	});

	}
}


jQuery(document).ready(function(){
	if(location.hash.substring(1) === ''){
		var currentTab = 'triggered';
	} else {
		var currentTab = location.hash.substring(1);
	}

	jQuery('.vote').click(function(e){
		e.preventDefault();
		target = jQuery(this)[0];
		symbol = jQuery('div.mpbox div.useraction  input.symbol[type="hidden"]').val();
		action = jQuery(this).attr('action');
		if (typeof action !== 'undefined') {
		saveVote(target, symbol, action);
		}

	});

	jQuery('.useraction, #feed').on('click','.confirmPick',function(e){
		e.preventDefault();
		var symbol = jQuery(this).closest('.useraction').find('.symbol').val();

		pickDiv = jQuery(this).closest('.pick')[0];
		pickDiv.html("<img src='/shared/images/throbber2.gif'>");
		jQuery.ajax({
			url: 	'/inc/UserActionsBackend.php',
			dataType: 	'json',
			type: 		'post',
			data: 		{
				user: 		userId,
				setPick: 	jQuery(this).attr('action'),
				symbol: 	symbol
			},
			success: 	function(response) {
				if(response.success === true) {
					// Only set padding to 0 if not on feed
					var divStyle = jQuery('#feed').length === 1 ? '' : 'padding:0;';

					var button = response.position === 'Long' ? 'greenButton' : 'redButton';
					var position = response.position === 'Long' ? '<span>Buy</span>' : '<span>Sell</span>';
					var div = "" +
						"<input type='hidden' class='pickId' value='"+response.id+"' />" +
						"<a class='popout delete deletePick' href='#'>&nbsp;</a>" +
						"<div class='pick-text' style='"+divStyle+"'>" +
							"<strong>My Pick:</strong> <strong class='"+button+"'>"+position+"</strong> "+response.price+" on "+response.date +
						"</div>";
					jQuery(pickDiv).removeClass('unset').addClass('set');
					pickDiv.html(div);
				}
				else {
					pickDiv.html('<div class="error">An error occurred when setting your pick.</div>')
				}
			}
		});
	});

	jQuery('.useraction, #feed').on('click', '.deletePick', function(e) {
		e.preventDefault();

		var div = '<div>Are you sure you want to delete this pick?</div>';
		var button = jQuery(this);
		
		jQuery(div).dialog({
			buttons: 	{
				'Delete': 		function(){
					pickDiv = button.closest('.pick')[0];
					id = jQuery('.pickId').val();
					pickDiv.html("<img src='/shared/images/throbber2.gif'>");

					jQuery.ajax({
						url: 		'/inc/UserActionsBackend.php',
						type: 		'post',
						dataType: 	'json',
						data: 		{
							user: 			userId,
							deletePick: 	id
						},
						success: 	function(response) {
							if(response.success === true) {	
								var div = "" +
									"<input type='hidden' class='symbol' value='"+response.symbol+"' />" +
									"<div class='pick-text' style='padding:0;' tooltip='pick' text='Track your BUY and SELL trade picks. This symbol will be added to YOUR PICKS, where you can monitor their performance.'>" +
											"<strong>My long-term pick: </strong>" +
											"<a href='#' action='long' class='confirmPick greenButton'><span>BUY</span></a> or <a href='#' action='short' class='confirmPick redButton'><span>SELL</span></a>" +
									"</div>";
								pickDiv.html(div);
							}
							else {
								pickDiv.html('<span class="error" style="padding:3px;">There was an error removing your pick.</span>');
							}
						}
					});

					jQuery(this).dialog('close');

				},
				'Cancel': 		function(){
					jQuery(this).dialog('close');
				}
			},
			title: 		'Are you sure?',
			resizable: false
		});
	});

	// jQuery('.deletePick').tipsy({gravity: 's'});

	jQuery('.addAlert').live('click', function(e){
		jQuery('#save-alert').dialog({
			
			buttons: 	{
				'Save': 	function(){
					jQuery('#save-alert .alert-errors').text('');

					if(!jQuery('.alert-price').val()){
						jQuery('#save-alert .alert-errors').text('Please enter the alert price').slideDown();
						return false;
					}

					jQuery.ajax({
						url: 		'/inc/UserActionsBackend.php',
						type: 		'post',
						data: 		{
							symbol: 	jQuery('.alert .dialog .symbol').val(),
							setAlert: 	jQuery('.alert-price').val(),
							timeframe: 	jQuery('.alert-time:checked').val(),			
							user: 		userId
						},
						dataType: 	'json',
						success: 	function(response){
							if(response.success === false){
								jQuery('#save-alert .alert-errors').text(response.error);
							} else {
								jQuery('#save-alert').dialog('close');

								// hide the alerts button on the quotes page
								jQuery('.useraction.alert').fadeOut();

								symbol = jQuery(".alert .dialog .symbol").val();
								price = jQuery('.alert-price').val();
								timeframe = jQuery('.alert-time:checked').val() === 'intraday' ? 'ANYTIME' : 'END-OF-DAY';
								html = ''+
								'<div class="quote-alert">' +
                                    '<div class="error" style="display:none"></div>' +
                                    '<img src="/shared/images/Alert.png" class="alert-icon" alt="Alert"/>' +
                                    '<strong>MY ALERTS:  </strong> Alert me when ' +
                                    symbol + ' reaches ' +
                                    '<strong class="alert-value">'+ response.price + '</strong> at ' +
                                    '<strong class="alert-timeframe">'+ timeframe +'</strong>' +
                                    '<div class="options">' +
                                        '<input type="hidden" name="" class="alertId" value="'+ response.alertId +'" />' +
                                        '<input type="hidden" name="" class="alert-symbol" value="'+ symbol +'" />' +
                                        '<input type="hidden" name="" class="alert-quote" value="'+ price +'" />' +
                                        '<input type="hidden" name="" class="alert-timeframe" value="'+ jQuery('.alert-time:checked').val() +'" />' +
                                        '<a class="delete-alert"></a>' +
                                        '<a class="edit-alert"></a>' +
                                    '</div>' +
                                '</div>';
								jQuery('.alert').append(html);
							}
						}
					});
				},
				'Cancel': 	function(){
					jQuery(this).dialog('close');
				}
			},
			width: 		350,
			resizable: false
		});
	});


	jQuery('#alertsList').on('click', '.modify', function(e){
		jQuery('#delete-alert').dialog('close');

		jQuery('#alertsList .error').remove();

		if(location.hash.substring(1) === ''){
			var currentTab = 'triggered';
		} else {
			var currentTab = location.hash.substring(1);
		}

		var tr = jQuery(this).closest('tr.oddRow');
		var symbol = tr.attr('symbol');
		var button = jQuery(this);

		var quote = jQuery(this).siblings('.alert-quote').text();
		quote = quote;
		var timeframe = jQuery(this).siblings('.alert-timeframe-set').val();
		jQuery('#save-alert .alert-time').prop('checked',false);
		jQuery('#save-alert .alert-time[value="'+timeframe+'"]').prop('checked', true);

		// replace the dialog text
		jQuery('#save-alert .alert-symbol .warning .symbol').text(symbol);
		jQuery('.alert-price').val(quote);

		jQuery('#save-alert').dialog({
			buttons: 	{
				'Save': 	function(){
					jQuery('#save-alert .alert-errors').text('');

					if(!jQuery('.alert-price').val()){
						jQuery('#save-alert .alert-errors').text('Please enter the alert price').slideDown();
						return false;
					}

					jQuery.ajax({
						url: 		'/inc/UserActionsBackend.php',
						type: 		'post',
						data: 		{
							symbol: 		symbol,
							updateAlert: 	jQuery('.alert-price').val(),
							timeframe: 		jQuery('.alert-time:checked').val(),			
							user: 			userId
						},
						dataType: 	'json',
						success: 	function(response){
							if(response.success === false){
								if(response.errorNumber === 2) {
									jQuery('#save-alert').dialog('close');
									displayExpiredContract(function(){
										deleteAlert(button.siblings('.alertId').val());
										jQuery('#delete-expired').dialog('close');
										fadeOutRow(button);
									});
								}
								else {
									jQuery('#save-alert .alert-errors').text(response.error);									
								}
							}
							else{
								jQuery('#save-alert').dialog('close');
								button.siblings('.alert-timeframe-set').val(jQuery('.alert-time:checked').val());

								// Only update if the alert is changing tabs
								if (currentTab === 'triggered' || currentTab === 'acknowledged') {

									fadeOutRow(button);
								}
								// else change the current row text
								else{
									if(jQuery('.alert-time:checked').val() === 'intraday'){
										var timeframe = 'ANYTIME';
									} else {
										var timeframe = jQuery('.alert-time:checked').val();
									}

									button.siblings('.alert-quote').text(jQuery('.alert-price').val());
									button.siblings('.alert-timeframe').text(timeframe.toUpperCase());
								}
							}
						}
					});
				},
				'Cancel': 	function(){
					jQuery(this).dialog('close');
				}
			},
		width: 		350,
		resizable: false
		});
	});	

	jQuery('#alertsList').on('click', '.delete', function(){
		jQuery('#save-alert').dialog('close');		
		jQuery('#alertsList .error').remove();
		
		var button = jQuery(this);		
		alertId = jQuery(this).siblings('input.alertId').val();
		jQuery('#delete-alert').dialog({
			buttons: 	{
				'Delete': 	function(){
					jQuery.ajax({
						url: 		'/inc/UserActionsBackend.php',
						type: 		'post',
						dataType: 	'json',
						data: 		{
							removeAlert: 	true,
							alertId: 		alertId,
							user: 			userId
						},
						success: 	function(response){
							if(response.success === true){
								jQuery('#delete-alert').dialog('close');

								var curNum = jQuery('.tab.active a .alertNum').text();
								jQuery('.tab.active a .alertNum').text(curNum-1);
								// Fade out the alert 
								fadeOutRow(jQuery(button));
							} else {
								jQuery('.alert-errors').text(response.error);
							}
						}
					});
				},
				'Cancel': 	function(){
					jQuery(this).dialog('close');
				}
			},
			resizable: false
		});
	});

	jQuery('.alert').on('click','.delete-alert', function(){
		// Only open the dialog if the save alert dialog is not open
		if(jQuery('#save-alert').dialog('isOpen') === true) {
			return false;
		}

		alertId = jQuery(this).siblings('input.alertId').val();
		jQuery('#delete-alert').dialog({
			buttons: 	{
				'Delete': 		function(){
					deleteAlert(alertId);
				},
				'Cancel': 		function(){
					jQuery(this).dialog('close');
				}
			},
			resizable: false
		});
	});

	function deleteAlert (alertId) {
		jQuery.ajax({
			url: 		'/inc/UserActionsBackend.php',
			type: 		'post',
			dataType: 	'json',
			data: 		{
				removeAlert: 	true,
				alertId: 		alertId,
				user: 			userId
			},
			success: 	function(response){
				jQuery('#delete-alert').dialog('close');
				jQuery('.useraction').fadeIn();

				if(response.success === true){
					jQuery('.quote-alert, .triggered-alert, .alert .options').fadeOut();
							} else {
					jQuery('.error').text(response.error).show();
				}
			}
		});
	}

	// Edit the alert from the quotes page
	jQuery('.alert').on('click', '.edit-alert', function(){
		// Only open the dialog if the delete dialog is not open
		if(jQuery('#delete-alert').dialog('isOpen') === true) {
			return false;
		}

		var quote = jQuery(this).siblings('.alert-quote').val();
		var timeframe = jQuery(this).siblings('.alert-timeframe').val();
		var symbol = jQuery(this).siblings('.alert-symbol').val();
		var button = jQuery(this);

		jQuery('#save-alert .alert-time').prop('checked', false);
		jQuery('#save-alert .alert-time[value="'+timeframe+'"]').prop('checked', true);
		
		// replace the dialog text
		jQuery('#save-alert .alert-symbol .warning .symbol').text(symbol);
		jQuery('.alert-price').val(quote);

		jQuery('#save-alert').dialog({
			buttons: 	{
				'Save': 	function(){

					if (jQuery('.alert-price').val().length === 0 || jQuery('.alert-price').val() <= 0) {
						jQuery('.alert-errors').text('Please enter an alert value');
						return false;
					}

					jQuery.ajax({
						url: 		'/inc/UserActionsBackend.php',
						type: 		'post',
						dataType: 	'json',
						data: 		{
							symbol: 		symbol,
							updateAlert: 	jQuery('.alert-price').val(),
							timeframe: 		jQuery('.alert-time:checked').val(),			
							user: 			userId
						},
						success: 	function(response){
							if (response.success === true) {
								jQuery('#save-alert').dialog('close');
								
								// Converts alert-time to human readable form
								var timeframe = jQuery('.alert-time:checked').val() === 'intraday' ? 'ANYTIME' : 'END-OF-DAY';
								jQuery('input.alert-timeframe').val(jQuery('.alert-time:checked').val());

								// This alert has been triggered
								if(jQuery('.alert-status').val() === 'triggered') {
									// Change class to active alert default
									jQuery('.triggered-alert').attr('class', 'quote-alert');
									jQuery('.alert-status-text').text('MY ALERTS: ');
									jQuery('.alert-span').text('Alert me when ' + symbol + ' reaches');
									jQuery('.alert-value').text(response.price);
									jQuery('.alert-timeframe').text(timeframe);
									jQuery('.alert-quote').val(jQuery('.alert-price').val());
								}
								// This is an active alert
								else {
									jQuery('.alert-value').text(response.price);
									jQuery('.alert-timeframe').text(timeframe.toUpperCase());
									jQuery('.alert-quote').val(jQuery('.alert-price').val());
								}
							} else {
								jQuery('#save-alert .alert-errors').text(response.error);
							}
						}
					});
				},
				'Cancel': 	function(){
					jQuery(this).dialog('close');
				}
			},
			width: 		350,
			resizable: false
		});

	});

	jQuery('#acknowledgeGroup').die().live('click',function(){
		jQuery('#alertsList .error').remove();

		if(jQuery('.groupAction:checked').length === 0){
			jQuery('#deleteGroup').before('<div class="error">You did not select any alerts to acknowledge.</div>')
			return false;
		}

		var alertIds = new Array();
		jQuery('.groupAction:checked').each(function(){
			alertIds.push(jQuery(this).val());
		});

		jQuery.ajax({
			url: 		'/inc/UserActionsBackend.php',
			type: 		'post',
			dataType: 	'json',
			data: 		{
				acknowledgeAlert: 	true,
				alertIds: 			alertIds,
				user: 			userId
			},
			success: 	function(response){
				if(response.success === true){
					var cnt = 0;
					var curNum = jQuery('.tab.active a .alertNum').text();
					var curAck = jQuery('.tab a[rel="acknowledged"] .alertNum').text();
					jQuery('.groupAction:checked').each(function(){
						fadeOutRow(jQuery(this));
						cnt++;
					});
					jQuery('.tab.active a .alertNum').text(curNum-cnt);
					jQuery('.tab a[rel="acknowledged"] .alertNum').text(parseInt(curAck)+cnt);
				} else {
					jQuery('#deleteGroup').before(response.error);
				}
			}
		});
	});

	jQuery('#alertsList').on('click', '#deleteGroup', function(){
		jQuery('#alertsList .error').remove();

		if(jQuery('.groupAction:checked').length === 0){
			jQuery('#deleteGroup').before('<div class="error">You did not select any alerts to delete.</div>');
			return false;
		}

		var alertIds = new Array();
		jQuery('.groupAction:checked').each(function(){
			alertIds.push(jQuery(this).val());
		});

		jQuery.ajax({
			url: 		'/inc/UserActionsBackend.php',
			type: 		'post',
			dataType: 	'json',
			data: 		{
				deleteAlert: 		true,
				alertIds: 			alertIds,
				user: 				userId
			},
			success: 	function(response){
				if (response.success === true) {
					var cnt = 0;
					var curNum = jQuery('.tab.active a .alertNum').text();

					jQuery('.groupAction:checked').each(function(){
						fadeOutRow(jQuery(this));
						cnt++;
					});

					jQuery('.tab.active a .alertNum').text(curNum-cnt);
				} else {
					jQuery('#deleteGroup').before(response.error);
				}
			}
		});
	});
	
	jQuery('#update-feed').on('submit', function(e){
		e.preventDefault();

		fullfeed = [];
		newsfeed = [];
		// Empty default feeds, used when cancelling 
		defaultFeedItems = [];

		jQuery.each(jQuery('input[name="fullfeed[]"]:checked'), function(){
			fullfeed.push(jQuery(this).val());
			defaultFeedItems.push(jQuery(this).attr('id'));
		});

		jQuery.each(jQuery('input[name="newsfeed[]"]:checked'), function(){
			newsfeed.push(jQuery(this).val());
			defaultFeedItems.push(jQuery(this).attr('id'));
		});

		// Add Top AP if all other items unchecked
		if(fullfeed.length + newsfeed.length === 0) {
			newsfeed.push(jQuery('#feedSrc_news_feedAPflagtopf').val());
			jQuery('#feedSrc_news_feedAPflagtopf').prop('checked',true);
		}

		jQuery.ajax({
			url: 		'/feed/configureFeed.php',
			data: 		{
				fullfeed: 		fullfeed,
				newsfeed: 		newsfeed
			},
			dataType: 	'text',
			type: 		'post',
			success: 	function(response) {
				var div = '<div class="message">Feed settings saved!</div>';
				jQuery('.response').html(div).fadeIn();
				setTimeout(function(){
					jQuery('.response').fadeOut();
				}, 2500);
			}
		});
		return false;
	});

	jQuery('.feedConfig').on('click','.cancel-configure-feed',function(e){
		// Prevent form from submitting
		e.preventDefault();
		// Uncheck all checkboxes
		jQuery('.feedConfig input[type="checkbox"]').each(function(){
			jQuery(this).prop('checked', false);
		});
		// Check the original feed items
		defaultFeedItems.each(function(id){
			jQuery('#' + id).prop('checked', true);
		});
		feedElement = document.getElementById('feedConfigure');
		if(feedElement !== null) {			
			// Hide the configure popout
			$('feedConfigure').hide();
		}
	});

	jQuery('[tooltip]').live('mouseenter', function() {
		var title = { button: 'Never show again' };
		if(tooltips[jQuery(this).attr('tooltip')] == null)
			title = false;
		
		//Only show tooltip if user prefers to show it.
		if(!title || (tooltips[jQuery(this).attr('tooltip')] && !jQuery(this).qtip('api'))) {
			jQuery(this).qtip({
		        content: {
		        	attr: 'text',
		            title: title
		        },
		        position: {
		            my: 'top left',
		            at: 'bottom left',
		            adjust: {
		                x: 10
		            }
		        },
		        show: {
		        	solo: true,
		            ready: true
		        },
		        hide: {
		            fixed: true,
		            delay: 500
		        },
		        events: {
		            hide: function(event, api) {
		                if(event.originalEvent.type == 'click') { //never show again was clicked
		                    var type = jQuery(api.elements.target).attr('tooltip');
		                    jQuery.ajax({
		                        url:        '/inc/UserActionsBackend.php',
		                        type:       'post',
		                        dataType:   'json',
		                        data:       {
		                        	user: userId,
		                            hideToolTip:     type
		                        }
		                    });
		                    tooltips[type] = false;
		                    jQuery('[tooltip="' + type + '"]').each(function() {
		                    	if(jQuery(this).qtip('api'))
		                    		jQuery(this).qtip('api').destroy();
		                    });
		                }
		            }
		        }
		    });
		}
	});

	fixIEVoteSlider();

});

function fadeOutRow(element){
	element.closest('tr.oddRow').prev('.evenRow').fadeOut();
	element.closest('tr.oddRow').prev('.evenRow').prev('.paddingRow').fadeOut();
	element.closest('tr.oddRow').fadeOut(function() {
		// If there are no more visible alerts display, remove table
		if(jQuery('#alertsList .oddRow:visible').length === 0 ) {				
			jQuery('#alertsList').html('None Saved');
		}
	});
}

function displayExpiredContract (success) {
	var div = '<div id="delete-expired">You cannot reset an expired contract. Delete this alert instead.</div>';
	jQuery(div).dialog({
		buttons: 	{
			'Delete': 	success,
			'Cancel': 	function() {
				jQuery(this).dialog('close');
			}
		},
		resizable: false,
		title: 	'Contract has expired.'
	})
}

function formatAlert (alertValue) {
	return alertValue.replace('s','');
}

function fixIEVoteSlider() {
	jQuery('.voteResults').each(function(index, element) {
		var slideDiv = jQuery(element).find('.slide');

		if(slideDiv.length === 0) {
			// Equivalent to continue
			return true;
		}

		var margin = slideDiv.css('marginLeft');
		slideDiv.css('marginLeft', margin);
	});
}