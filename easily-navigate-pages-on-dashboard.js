jQuery(function($){
content = "Empty";
count = 0;
//Hide all the sub items

$("#easily-navigate-pages-on-dashboard li").find("ul").css('display', 'none');

$("#easily-navigate-pages-on-dashboard li").each(
   	function () {
		//START Select only pages with no children
		if ( $(this).children().size() < 3 ) {
			$(this).addClass('not-folder');
			 $(this).children('a').removeClass('expand');
		}
		//END Select only pages with no children
	}
);

$('#easily-navigate-pages-on-dashboard li a.expand').toggle(function() {
	if ( $(this).parent().children().size() > 1 ) {
		$(this).parent().addClass('open');
		$(this).parent().children('ul').css('display', 'block');
	}
}, function() {
  $(this).parent().removeClass('open');
  $(this).parent().children('ul').css('display', 'none');
  //$(this).parent().children('a').css('display', 'block');
});



//END only work where the div is
});

