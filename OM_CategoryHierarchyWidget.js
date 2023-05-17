jQuery(function ($) {
	$(document).ready(function () {
		// Open the first level
		if ( $('body').hasClass('tax-product_cat')) {
			console.log('hasclass');

			// open the first level
			var mainItem = $('.widget_category_hierarchy_widget > .om_childrenWrapper > li.om_HasChildren ');
			mainItem.toggleClass("om_openedElemt");
			mainItem.find('.om_labelWRapper:first .om_cat_toggler').text('-');

			// open all the path to the current element
			$('.om_CurrentCat_item').parents('.item.om_HasChildren').addClass('om_openedElemt');
			$('.om_openedElemt > .om_labelWRapper > .om_cat_toggler').text('-');
		}
	 
		$(".om_cat_toggler").on('click', function(){
			console.log("er");
			var parentElem = $(this).closest('.om_HasChildren');
			parentElem.toggleClass("om_openedElemt");
			// parentElem.find('.om_childrenWrapper:first').toggle();
			
			if (parentElem.hasClass('om_openedElemt')) {
				parentElem.find('.om_labelWRapper:first .om_cat_toggler').text('-');
			}else{
				parentElem.find('.om_labelWRapper:first .om_cat_toggler').text('+');
			}
		});

	});
});
