$(document).ready(function(){
  $('.navlink').click(function(e){
    var linkId = $(this).attr('id');
    switch(linkId) {
      case 'homelink':
        $('.page').removeClass('page-active');
        $('#page-home').addClass('page-active');
        break;
      case 'projectslink':
        $('.page').removeClass('page-active');
        $('#page-projects').addClass('page-active');
        break;
    }    
  })
});