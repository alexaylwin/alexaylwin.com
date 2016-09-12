$(document).ready(function(){
  $('.navlink').click(function(e){
    var linkId = $(this).attr('id');
	$('.navlink').removeClass('selected');
	switch(linkId) {
      case 'homelink':
        $("html, body").animate({ scrollTop: 0 }, "slow");
		    $(this).addClass("selected");
        break;
      case 'projectslink':
        var pos = $("#page-projects").position();
        $("html, body").animate({ scrollTop: pos.top }, "slow");
		    $(this).addClass("selected");
        break;
      case 'aboutmelink':
        var pos = $("#page-aboutme").position();
        $("html, body").animate({ scrollTop: pos.top }, "slow");
        $(this).addClass("selected");
        break;
      case 'skillsetlink':
        var pos = $("#page-skillset").position();
        $("html, body").animate({ scrollTop: pos.top }, "slow");
		    $(this).addClass("selected");
        break;		
    }
  })
});