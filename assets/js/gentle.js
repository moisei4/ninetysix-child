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
			
			return false;
		} );
	} )(jQuery);
} );