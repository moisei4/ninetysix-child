"use strict";

jQuery(document).ready(function() { 
	
	(function ($) { 
		$(window).load(function () { 
			$('.sf-mobile-menu .menu-item-has-children.current-menu-ancestor').addClass('active');
		});
		
		
		$('.sf-mobile-menu .menu-item-has-children > a').on('click', function () { 
			var activeButton = $(this), 
				activeButtonLi = activeButton.closest('.menu-item-has-children'), 
				menuWrap = activeButton.closest('.sf-mobile-menu');
			
			
			if (activeButtonLi.hasClass('active')) {
				
				activeButtonLi.removeClass('active').addClass('not_active');
				
				if ($('.sf-mobile-menu > .menu-item-has-children').hasClass('active')) {
					menuWrap.addClass('menu_opened');
				} else {
					menuWrap.removeClass('menu_opened');
				}
			} else {
				activeButtonLi.addClass('active').removeClass('not_active');
				
				if ($('.sf-mobile-menu > .menu-item-has-children').hasClass('active')) {
					menuWrap.addClass('menu_opened');
				} else {
					menuWrap.removeClass('menu_opened');
				}
			}
			
			
			return false;
		} );
		
		$('.single_add_to_cart_button').on('click', function () { 
			$('.single_add_to_cart_button').addClass('load');
			setTimeout(function () { 
				$('.single_add_to_cart_button').removeClass('load');
			}, 2000);
		} );
	} )(jQuery);
	
	
	/* Header Small Menu Button Toggle */
	(function ($) { 
		$('.gentle_menu_button').on('click', function () { 
			if ($(window).width() < 1025) {
				var menuButton = $(this), 
					bodyClass = $('body');
				
				
				if (bodyClass.hasClass('small_menu_opened')) {
					bodyClass.removeClass('small_menu_opened');
				} else {
					bodyClass.addClass('small_menu_opened');
				}
				
				$(document).click(function(event) {
					if ($(event.target).closest('.waves-header').length) return;
					
					bodyClass.removeClass('small_menu_opened');
					
					event.stopPropagation();
				} );
				
				return false;
			}
		} );
		
		$(window).on('debouncedresize', function () { 
			if ($(window).width() >= (1025)) {
				if ($('body').hasClass('small_menu_opened')) {
					$('body').removeClass('small_menu_opened');
				}
			}
		} );
	} )(jQuery);
} );