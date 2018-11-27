var x=$(window);
var e=$("#shape");

$("html,body").ready(function(){
	var scrollbar=x.scrollTop();
	var isClick=0;

	(scrollbar<=0)?($("#shape").hide()):($("#shape").show());

	$(window).scroll(function(){
		scrollbar=x.scrollTop();
		(scrollbar<=0)?($("#shape").hide()):($("#shape").show());			
	})

	$("#shape").hover(
		function(){
			$(".shapeColor").show();
		},

		function(){
			$(".shapeColor").hide();
		}
	)

	$(".shapeColor").click(
		function(){
			$(".shapeFly").show();
			$("html,body").animate({scrollTop: '00px'},"slow");
			$("#shape").delay("200").animate({marginTop:"-100px"},"slow",function(){
				$("#shape").css("margin-top","0px");
				$(".shapeFly").hide();
			});
			
	})

})