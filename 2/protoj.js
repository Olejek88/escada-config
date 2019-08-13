/*
 * protoj.js
 * Revision: 080e30a77dcb90e326f19877e926c3d3a0431ce6
 * Website: http://github.com/manverualma/protoj/
 * Last Updated: May 24, 2011
 *
 * Copyright (C) 2011 by Michael Day
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

Element.addMethods({
	hide: function(element, speed, callback) {
		/* Allow callback to be the first and only parameter */
		if (Object.isFunction(speed)) {
			callback = speed;
			speed = false;
		}
		
		if (!(speed))
			speed = 0;
		
		/* Set speed if value is 'fast' or 'slow' */ 
		if (protoj.speeds[speed])
			speed = protoj.speeds[speed];
		else if (!Object.isNumber(speed))
			speed = protoj.speeds['_default'];
		
		
		if (speed == 0) {
			element.setStyle({display: 'none'});
			
			if (typeof callback == 'function')
				callback.call(element);
		} else {
			Effect.Fade(element, {
				duration: speed / 1000,
				afterFinish: function(){
					if (typeof callback == 'function')
						callback.call(element);
				}
			});
		}
		
		return element;
	},
	
	show: function(element, speed, callback) {
		/* Allow callback to be the first and only parameter */
		if (Object.isFunction(speed)) {
			callback = speed;
			speed = false;
		}
		
		if (!(speed))
			speed = 0;
		
		/* Set speed if value is 'fast' or 'slow' */ 
		if (protoj.speeds[speed])
			speed = protoj.speeds[speed];
		else if (!Object.isNumber(speed + 0))
			speed = protoj.speeds['_default'];
		
		if (speed == 0) {
			element.setStyle({display: ''});
			
			if (typeof callback == 'function')
				callback.call(element);
		} else {
			Effect.Appear(element, {
				duration: speed / 1000,
				afterFinish: function(){
					if (typeof callback == 'function')
						callback.call(element);
				}
			});
		}
		
		return element;
	},
	
	html: function(element, htmlString) {
		if (htmlString)
			element.innerHTML = htmlString;
		else
			return element.innerHTML;
		
		return element;
	}
})

window.$$ = function() {
  var expression = $A(arguments).join(', ');
  obj = protoj(Prototype.Selector.select(expression, document));
  obj.selector = expression;
  return obj;
};

var events = "blur focus click dblclick mousedown mouseup mousemove " +
             "mouseover mouseout mouseenter mouseleave change select " +
             "submit keydown keypress keyup";
events = events.split(' ');

protoj = function(data) {
	data.hide = function(speed, callback) {
		this.invoke('hide', speed, callback);
		return this;
	};
	
	data.show = function(speed, callback) {
		this.invoke('show', speed, callback);
		return this;
	};
	
	data.html = function(htmlString) {
		if (!htmlString)
			return data[0].html();
		else
			this.invoke('html', htmlString);
		
		return this;
	}
	
	for (i = 0; i < events.length; i++) {
		data[events[i]] = protoj.prototype[events[i]];
	}
	
	data.live = function(eventType, callback) {
		var selector = this.selector;

		document.observe(eventType, function(event, element) {
			if (element = event.findElement(selector)) {
				callback.call(element, event);
				event.stop();
			}
		});

		return this;
	}
	
	return data;
};

protoj.speeds = {
	fast: 200,
	slow: 600,
	_default: 400
};

events.each(function(name){
	protoj.prototype[name] = function(callback) {
		this.each(function(element){
			element.observe(name, function(event){
				callback.call(element, event);
			})
		})

		return this;
	}
})