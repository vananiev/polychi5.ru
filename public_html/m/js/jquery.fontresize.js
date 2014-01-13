/**
 * This file is part of the MobilizeToday.com mobile optimization core.
 * Copyright (c) 2011 Mobile Web Solutions Inc, d/b/a MobilizeToday.com
 * All rights reserved.
 */
(function($) {
  $.fn.fontresize = function(adj) {

    var options = $.extend({
		cookieName: 'mt-fontresize',
		cookieParams: {
			expires: 30,
			path: "/"
		}
	}, options);

	var size = $.cookie( options.cookieName );
	if (size) {
		$.fn.fontresize.scale($.cookie( options.cookieName ), options);
	}

    $(this).each( function() {
		  $(this).bind('click', function() {
			$.fn.fontresize.scale( adj, options );
			return false;
		  });
		});
		return this;
  };

	$.fn.fontresize.reset = function( options ) {
		$.cookie( options.cookieName, null, options.cookieParams );
	}

	$.fn.fontresize.scale = function( adj, options ) {
		var delta, s = 0;
		var custom = false;
		var body = $('body');
		
		if (adj == "increase") delta = 3;
		else if (adj == "decrease") delta = -3;
		else if (adj == "reset") {$.fn.fontresize.reset(options);body.css("font-size", ""); return;}
		else {delta = parseFloat(adj);custom = true}

		
		var currentSize = parseFloat(body.css("font-size"));
		
		if (custom) {
			s = delta;
		} else {
			s = currentSize + delta;
		}
		body.css("font-size", s);
		$.cookie( options.cookieName, s, options.cookieParams);

		return;
};

})(jQuery);
