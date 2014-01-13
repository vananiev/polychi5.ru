/**
 * This file is part of the MobilizeToday.com mobile optimization core.
 * Copyright (c) 2011-2012 Mobile Web Solutions Inc, d/b/a MobilizeToday.com
 * All rights reserved.
 */

jQuery(document).ready(initPage);

var body, bodyStyle, cssTransitionsSupported = false;


function initPage()
{
	jQuery("header.mt-main-header[style='text-align:center;'] > a.mt-link-back").each(function(){
		jQuery(this).clone(false).addClass('mt-link-back-hidden').prependTo(jQuery(this).parent());
	});

	jQuery("header.mt-main-header[style='text-align:center;'] > a.mt-link-home").each(function(){
		jQuery(this).clone(false).addClass('mt-link-home-hidden').prependTo(jQuery(this).parent());
	});

	if(jQuery('.mt-fontresize').length) {
		jQuery('body').addClass('set-fontresize');
		jQuery("a.mt-font-increase").fontresize("increase");
		jQuery("a.mt-font-decrease").fontresize("decrease");
		jQuery("a.mt-font-reset").fontresize("reset","reset");
	}

	var animation = 'auto';
	
	var ua = navigator.userAgent.toLowerCase();
	if (ua.indexOf('blackberry') != -1 && ua.indexOf('/5.')) {
		animation = false;
		jQuery('body').addClass('blackberry5');
	}
	if (ua.indexOf('blackberry') != -1 && ua.indexOf('/6.')) {
		jQuery('body').addClass('blackberry6');
	}

	//template:navigation init accordion menus
	initMenus();

	//template:breadcrumbs
	jQuery('.mt-breadcrumbs').touchSlider({animation: animation, mode: 'auto',prevLink: 'a.mt-prev',nextLink: 'a.mt-next',item: 'li',holder: 'nav',box: 'ol', lockScroll: true});

	//template:tabs
	jQuery('.mt-tab-nav').touchSlider({animation: animation, mode: 'auto',prevLink: 'a.mt-prev',nextLink: 'a.mt-next',item: 'li',holder: 'nav',box: 'ul', center:true, lockScroll: true});
	jQuery('.mt-tab-nav').mtTabs({item: '.mt-tabs_holder a'});

	//template:image-gallery
	jQuery('.mt-gallery').each(function(i){
		var $this = jQuery(this);
		var counter = 'counter'+i;
		var tcontrol = 'tcontrol'+i;
		$this.touchSlider({
			animation: animation, mode: 'auto',prevLink: 'a.mt-prev',nextLink: 'a.mt-next',item: 'ul.mt-list > li.mt-list-item',holder: 'div.holder',box: 'ul.mt-list',
			center:jQuery(this).attr('data-center') ? true : false,
			single:jQuery(this).attr('data-single') ? true : false,
			mode:jQuery(this).attr('data-mode') ? jQuery(this).attr('data-mode') : 'auto',
			onStart:
				jQuery(this).attr('data-controls') == 'counter' ? function() {
					jQuery('#'+counter, $this).remove();
					$this.append('<div  class="mt-counter" id="'+counter+'"></div>');
					jQuery('#'+counter).html('1/' + $this.get(0).getCount());
				} : ( jQuery(this).attr('data-controls') == 'tabs' ? function(){
					jQuery('#'+tcontrol, $this).remove();
					$this.append('<div  class="mt-tcontrol" id="'+tcontrol+'"></div>');
					for (var i = 0; i < $this.get(0).getCount(); i++) {
						var el = jQuery('<a href="#" class="tablink">'+(i+1)+'</a>');
						el.attr('index', i);
						jQuery('#'+tcontrol).append(el);

						el.bind('click', function(){
							$this.get(0).moveTo(jQuery(this).attr('index'));
							return false;
						});
					}
				} : null),
			onCheckItems:
				jQuery(this).attr('data-controls') == 'counter' ? function() {
					jQuery('#'+counter, $this).remove();
					$this.append('<div  class="mt-counter" id="'+counter+'"></div>');
					jQuery('#'+counter).html('1/' + $this.get(0).getCount());
				} : ( jQuery(this).attr('data-controls') == 'tabs' ? function(){
					jQuery('#'+tcontrol, $this).remove();
					$this.append('<div  class="mt-tcontrol" id="'+tcontrol+'"></div>');
					for (var i = 0; i < $this.get(0).getCount(); i++) {
						var el = jQuery('<a href="#" class="tablink">'+(i+1)+'</a>');
						el.attr('index', i);
						jQuery('#'+tcontrol).append(el);

						el.bind('click', function(){
							$this.get(0).moveTo(jQuery(this).attr('index'));
							return false;
						});
					}
				} : null),
			onChange:
				jQuery(this).attr('data-controls') == 'counter' ? function(prev, curr) {
					jQuery('#'+counter).html((curr+1)+'/'+ $this.get(0).getCount());
				} : ( jQuery(this).attr('data-controls') == 'tabs' ? function(prev, curr){
					jQuery('#'+tcontrol+' a.tablink').removeClass('active');
					jQuery('#'+tcontrol+' a.tablink').filter(function(i){return i == curr}).addClass('active');
				} : null)
		});
	});

	//template:footer
	jQuery('.mt-footer').touchSlider({animation: animation, mode: 'auto',prevLink: 'a.mt-prev',nextLink: 'a.mt-next',item: 'li',holder: 'nav',box: 'ul',center:true, lockScroll: false});

	//template:pagination
	jQuery('.mt-paging').touchSlider({animation: animation, mode: 'auto',prevLink: 'a.mt-prev',nextLink: 'a.mt-next',item: 'li',holder: 'nav',box: 'ul',center:true, lockScroll: true});

	//template:expandable-block
	jQuery('.mt-expandable-content .mt-content').hide().parent().removeClass("mt-open");
	jQuery('.mt-expandable-content .mt-opener').unbind("click").click(function(){
			jQuery(this).next().slideToggle("normal",function(){
					jQuery(this).parent().toggleClass("mt-open");
				});
		});

	//template:ajax
	jQuery('.mt-ajax-link').unbind("click").click(function(){
		var _href = this.href;
		jQuery(this).parent().load(_href, function () {
			//re-init all event handlers
			initPage();
		});
		return false;
	});

	//template:google-map - one instance per page!
	jQuery('.mt-google-map-link').unbind("click").click(loadGMScript);

	//template:search-box
	jQuery('.mt-search [type="text"]').focus(function() {
		if (this.value == this.defaultValue){
				this.value = '';
		}
		if(this.value != this.defaultValue){
				this.select();
		}
	})
	.blur(function() {
		if (this.value == ''){
				this.value = (this.defaultValue ? this.defaultValue : '');
		}
	});

	//template:form-brutal-compact
	jQuery('.mt-form [type="text"], .mt-form textarea').focus(function() {
		if (this.value == this.defaultValue){
				this.value = '';
		}
		if(this.value != this.defaultValue){
				this.select();
		}
	})
	.blur(function() {
		if (this.value == ''){
				this.value = (this.defaultValue ? this.defaultValue : '');
		}
	});

	//template:accordion
	jQuery('.mt-accordion .mt-content').hide();
	jQuery('.mt-showfirst .mt-content:first').show();
	jQuery('.mt-accordion header a').unbind("click").click(function() {
			var checkElement = jQuery(this).parent().next();
			if((checkElement.is('div.mt-content')) && (checkElement.is(':visible')))
			{
				checkElement.slideUp('normal');
				return false;
			}
			if((checkElement.is('div.mt-content')) && (!checkElement.is(':visible')))
			{
				jQuery('.mt-accordion div.mt-content:visible').slideUp('normal');
				checkElement.slideDown('normal');
				return false;
			}
	});

	//jQuery validate
	jQuery('form').attr('novalidate', 'novalidate');
	jQuery('form').each(function(){
		jQuery(this).validate({
			errorElement: 'p',
			errorClass: "mt-invalid",
			errorPlacement: function(error, element) {
				if(element.attr('type') == 'checkbox' || element.attr('type') == 'radio')
					error.insertBefore(element.parent());
				else
					error.insertBefore(element);
			}
		});
	});

}

function initMenus()
{
	jQuery('.mt-menu-btn').unbind("click").click(function() {
			jQuery(this).toggleClass("open");
			jQuery(this).next().slideToggle();
			return false;
		}).next().hide();

	jQuery('.mt-menu-accordion ul').hide();
	jQuery('.mt-menu-accordion li a').unbind("click").click(function() {
			var checkElement = jQuery(this).next();
			if((checkElement.is('ul')) && (checkElement.is(':visible')))
			{
				checkElement.slideUp('normal');
				return false;
			}
			if((checkElement.is('ul')) && (!checkElement.is(':visible')))
			{
				jQuery('.mt-menu-accordion ul:visible').slideUp('normal');
				checkElement.slideDown('normal');
				return false;
			}
	});
}

function initGoogleMap() {
	var opts = jQuery(".mt-google-map-link").attr("href").substring(1).split(":");
	var caption = jQuery(".mt-google-map-link").attr("title");

	var location = new google.maps.LatLng(opts[1], opts[2]);
	var iw = new google.maps.InfoWindow();
	var myOptions = {
		zoom: 14,
		center: location,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	}

	jQuery(".mt-google-map").addClass("mt-google-map-show");
	var map = new google.maps.Map(jQuery(".mt-google-map").get(0), myOptions);

	if (opts[0])
	{
		var address = opts[0];
		var geocoder = new google.maps.Geocoder();
		geocoder.geocode( { 'address': address}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				map.setCenter(results[0].geometry.location);
				iw.setContent(caption);
				iw.setPosition(results[0].geometry.location);
				iw.open(map);
			} else {
				alert("Geocode was not successful for the following reason: " + status);
			}
		});
	}
	else
	{
		iw.setContent(caption);
		iw.setPosition(location);
		iw.open(map);
	}
}

function loadGMScript() {
	var script = document.createElement("script");
	script.type = "text/javascript";
	script.src = "http://maps.google.com/maps/api/js?sensor=false&callback=initGoogleMap";
	document.body.appendChild(script);
	return false;
}
