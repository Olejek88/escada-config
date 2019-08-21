Hovercard = function() {
	var e = document.createElement('div');
	e.id = 'hovercard';
	e.style.display = 'none';
	
	e.onmouseover = function() {
		hovercard.mouseover = true;
	}
	
	 $(e).observe('mouseleave', function(event){
		hovercard.hide();
	 });
	 
	jQuery('.maincol').append(e);
	
	this.container = $(e);
	
	this.throbberSrc = '/shared/images/throbber3.gif';
	
	i = document.createElement('img');
	i.src = this.throbberSrc;
}

Hovercard.prototype.cache = {}
Hovercard.prototype.active = false;
Hovercard.prototype.showing = false;
Hovercard.prototype.mouseover = false;

Hovercard.prototype.isDescendant = function(parent, child) {
    var node = child.parentNode;
    
    while (node != null) {
        if (node == parent)
            return true;

        node = node.parentNode;
    }
    
    return false;
}

Hovercard.prototype.show = function(elem, e, feedItemId) {
	id = elem.readAttribute('rel');
	var hovercardType = jQuery(elem).attr('hovercard');

	if (!id)
		return false;

	if (hovercardType !== undefined) {		
		parameters = {id: id, hovercardType: hovercardType};
	} else {		
		parameters = {id: id};
	}
	
	this.showThrobber();
	this.container.style.top = e.pageY + 'px';
	this.container.style.left = e.pageX + 'px';
	hovercard.container.show();
	
	if (hovercard.cache[id]) {
		hovercard.container.innerHTML = hovercard.cache[id];
	} else {
		new Ajax.Request('/hovercard.php', {
			method: 'get',
			parameters: parameters,
			evalJSON: true,
			onSuccess: function(t) {
				var html = t.responseJSON.html;
				html += '<span style="display:none;" class="feedItemId" value="'+ feedItemId +'"></span>';

				hovercard.cache[id] = html;
				hovercard.container.innerHTML = html;
			}
		})
	}

	return false;
}

Hovercard.prototype.hide = function() {
	this.container.hide();
	
	return false;
}

Hovercard.prototype.showThrobber = function() {
	this.container.innerHTML = "<img src='"+this.throbberSrc+"'>";
	
	return false;
}

document.observe("dom:loaded", function() {
	window.hovercard = new Hovercard();
});

$$('.user_name').live('mouseover', function(e){
	elem = this;

	// Don't show hovercard if the user is the current user
	if(jQuery(elem).prop('rel') == userId || jQuery(elem).hasClass('nohover'))
		return false;

	feedItemId =  jQuery(elem).closest('.feedItemTopContainer').attr('feedItemId');
	
	hovercard.active = true;

	elem.observe('mouseout', function(){
		elem.stopObserving('mouseout');
		hovercard.active = false;

		setTimeout(function(){
			if (hovercard.mouseover != true)
				hovercard.hide();
		}, 10);
	})

	setTimeout(function(){
		if (hovercard.active == true) {
			hovercard.show(elem, e, feedItemId);
		}
	}, 1000);
});