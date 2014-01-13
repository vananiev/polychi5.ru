/**
 * This file is part of the MobilizeToday.com mobile optimization core.
 * Copyright (c) 2011 Mobile Web Solutions Inc, d/b/a MobilizeToday.com
 * All rights reserved.
 */
(function($) {
	
	$.fn.mtTabs = function(options){
		
		var options = $.extend({
			item: 'a',
			activeTabClass: 'selected',
			onStart: null
		}, options);

		var $this = $(this);
		
		this.each(function() {

			var $this = $(this);
			var items = $(options.item, $this);
			if (!items.length) return;

			 items.bind('click', function () {
				showTab($(this));
				return false;
			});

			if ($.isFunction(options.onStart)) options.onStart();

			items.filter(':first').click();

			 function showTab(link) {
				var tab = link.attr('href');
				items.each(function() {
					if (link.attr('href') != $(this).attr('href')) {
						$(this).parent().removeClass(options.activeTabClass);
					} else {
						$(this).parent().addClass(options.activeTabClass);
					}
					if ($(this).attr('href') != '#') {
						$($(this).attr('href')).hide();
					}
				});
				jQuery(tab).show();
			}
		});
		return this;
	}
})(jQuery);