jQuery(document).ready(function($){

	/* prepend menu icon */
	$('#menu-mobile').prepend('<div id="menu-mobile-icon"><img src="catalog/view/theme/mi-mi-store/image/menu-mobile-icon.png"/></div>');
	
	/* toggle nav */
	$("#menu-mobile-icon").on("click", function(){
		$("#menu-mobile-nav").slideToggle();
		$(this).toggleClass("active");
	});

});
