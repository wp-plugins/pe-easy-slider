jQuery(document).ready(
	function($)
	{
	    $('.slider-carousel-outer').carousel({
	      interval: 5000,
	      pause: "false"
	    })
	    $('.playButton').click(function () {
	        $('.slider-carousel-outer:hover').carousel('cycle');
	        $('.slider-carousel-outer:hover button.playButton').css("display", "none");
	        $('.slider-carousel-outer:hover button.pauseButton').css("display", "block");
	    });
	    $('.pauseButton').click(function () {
	        $('.slider-carousel-outer:hover').carousel('pause');
	        $('.slider-carousel-outer:hover button.playButton').css("display", "block");
	        $('.slider-carousel-outer:hover button.pauseButton').css("display", "none");
	    });
	}
);