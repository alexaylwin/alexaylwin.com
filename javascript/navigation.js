$(document).ready(function(){
  $('.navlink').click(function(e){
    var linkId = $(this).attr('id');
	$('.navlink').removeClass('selected');
	switch(linkId) {
      case 'homelink':
        //$('.page').removeClass('page-active');
        //$('#page-home').addClass('page-active');
        $("html, body").animate({ scrollTop: 0 }, "slow");
		$(this).addClass("selected");
        break;
      case 'projectslink':
        var pos = $("#page-projects").position();
        $("html, body").animate({ scrollTop: pos.top }, "slow");
		$(this).addClass("selected");
        //$('.page').removeClass('page-active');
        //$('#page-projects').addClass('page-active');
        break;
      case 'aboutmelink':
        var pos = $("#page-aboutme").position();
        $("html, body").animate({ scrollTop: pos.top }, "slow");
		$(this).addClass("selected");
        break;		
    }
  })
});