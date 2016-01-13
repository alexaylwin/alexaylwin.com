$(document).ready(function(){
  $('.navlink').click(function(e){
    var linkId = $(this).attr('id');
	
	$('.navlink').css('display', 'inline');
	
	
	switch(linkId) {
      case 'homelink':
        //$('.page').removeClass('page-active');
        //$('#page-home').addClass('page-active');
        $("html, body").animate({ scrollTop: 0 }, "slow");
        break;
      case 'projectslink':
        var pos = $("#page-projects").position();
        $("html, body").animate({ scrollTop: pos.top }, "slow");
        //$('.page').removeClass('page-active');
        //$('#page-projects').addClass('page-active');
        break;
    }
  })
});