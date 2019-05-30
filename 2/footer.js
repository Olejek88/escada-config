jQuery(document).ready(function(){
	jQuery('#newsletter-signup-email').focusin(function(){
		if(jQuery(this).val() == 'Email Address'){
			jQuery(this).val('');
		}
	});
	jQuery('#newsletter-signup-email').focusout(function(){
		if(jQuery(this).val() == ''){
			jQuery(this).val('Email Address');
		}
	});

	jQuery('#newsletter-submit-button').click(function(e){
		// prevent the default behavoir of the form
		e.preventDefault();

		jQuery('#newsletters .errors').text('');

		var errors = true;
		jQuery('.newsletter-checkbox').each(function(){
			if(jQuery(this).is(":checked") === true){
				errors = false;
			}
		});
		if(errors === true){
			jQuery('#newsletters .errors').text('A newsletter is required.');
			return false;
		}

		if(jQuery('#newsletter-signup-email').length === 0 || 
			jQuery('#newsletter-signup-email').val().toLowerCase() === 'email address'){

			jQuery('#newsletters .errors').text('A valid email is required.');
			return false;
		}

		if(jQuery('#newsletter-signup-country').val() === '0'){
			jQuery('#newsletters .errors').text('Your country is required.');
			return false;
		}

		jQuery('#newsletter-submit-button').attr('disabled','disabled');
		jQuery('#loader').html('<img src="/shared/images/ajax-loader.gif" id="ajax-loader" alt="" />');
				
		jQuery.ajax({
			type: 'get',
			url: '/register.php',
			data: jQuery('#newsletter-form').serialize(),
			dataType: "json",
			success: function(response){
				if(response.success === true){
					jQuery('#newsletter-signup-form').fadeOut(function(){
						jQuery(this).html('<p class="response">Thank you for registering.</p>').fadeIn();
						jQuery(this).css({'margin-top':'20px'})
					});
				}
				else{
					// if the form data was not valid resdisplay it
					if(response.redisplayForm === true){
						jQuery('#ajax-loader').hide();
						jQuery('#newsletter-submit-button').removeAttr('disabled');
						jQuery('#newsletters .errors').text(response.message);
					}
					else{
						jQuery('#newsletter-signup-form').fadeOut(function(){
							jQuery(this).html(response.message).fadeIn();
							jQuery(this).css({'margin-top':'20px'})
						});					
					}					
				}				
			}
		});
	});
});