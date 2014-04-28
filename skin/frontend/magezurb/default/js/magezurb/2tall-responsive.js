
jQuery( document ).ready(function( $ ) { // code thanks to someone else

  var leftMenuJqueryAnimation = function(){

    $('#small-menu-left').click(function (e) {
      var $navigaciaBody = $('#small-menu-off-canvas-container') 
      ,   $navigaciaDiv  = $('#slide-menu-left') 
      ,   valBodyRight   = $navigaciaBody.css('right') === '0px'   ? '-250px' : '0px'
      ,   valBodyLeft    = $navigaciaBody.css('left')  === '250px' ? '0px' : '250px'
      ,   valDiv         = $navigaciaDiv.css('left')   === '0px' ? '250px' : '0px';

      $navigaciaDiv.animate({
        left: valDiv
      }, 300);
      $navigaciaBody.animate({
        right: valBodyRight,
        left:  valBodyLeft
      }, 300)
      return false;
    });
  }

  var rightMenuJqueryAnimation = function(){


    $('#small-menu-right').click(function (e) {
      var $navigaciaBody = $('#small-menu-off-canvas-container')
      ,   $navigaciaDiv  = $('#slide-menu-right')
      ,   valBodyRight   = $navigaciaBody.css('right') === '0px'    ? '250px'  : '0px'
      ,   valBodyLeft    = $navigaciaBody.css('left')  === '0px'    ? '-250px' : '0px'
      ,   valDiv         = $navigaciaDiv.css('right')  === '-250px' ? '0px'    : '-250px';

      $navigaciaDiv.animate({
        right: valDiv
      }, 300);

      $navigaciaBody.animate({
        right: valBodyRight,
        left:  valBodyLeft
      }, 300)

      return false;
    });
  }

  var rightMenuCssTransition = function(){

    var rightLogic = 0 // declared once on runtime

    $('#small-menu-right').click(function (e) { 
      var $slideMenu = $('#slide-menu-right'); // 
      var $slideBody = $('#small-menu-off-canvas-container');
      
      if (rightLogic === 0){
        $slideMenu.addClass('slide-menu-right-active');
        $slideBody.addClass('right-menu-active');
        rightLogic = 1;
      }
      else {
        $slideMenu.removeClass('slide-menu-right-active');
        $slideBody.removeClass('right-menu-active');
        rightLogic = 0;
      }
    });
  }

  var leftMenuCssTransition = function(){

    var leftLogic = 0 // declared once on runtime

    $('#small-menu-left').click(function (e) { 
      var $slideMenu = $('#slide-menu-left'); // 
      var $slideBody = $('#small-menu-off-canvas-container');
      
      if (leftLogic === 0){
        $slideMenu.addClass('slide-menu-left-active');
        $slideBody.addClass('left-menu-active');
        leftLogic = 1;
      }
      else {
        $slideMenu.removeClass('slide-menu-left-active');
        $slideBody.removeClass('left-menu-active');
        leftLogic = 0;
      }
    });
  }

  if(!Modernizr.csstransitions) {
    rightMenuJqueryAnimation();
    leftMenuJqueryAnimation();
  }
  else {
    rightMenuCssTransition();
    leftMenuCssTransition();
  } 




    $(window).setBreakpoints({
        breakpoints: [
            767
        ] 
    }); 

    $(window).bind('enterBreakpoint767',function() { //big
      var $navigaciaBody  = $('#small-menu-off-canvas-container') 
      ,   $navigaciaLeft  = $('#slide-menu-left') 
      ,   $navigaciaRight = $('#slide-menu-right');

      if ($navigaciaBody.css('left') !== '0px'){
        // alert('boo');
      }
      else if ($navigaciaBody.css('left') !== '0px'){
        // call reset function
      }
    });

    


});




// $('#small-menu-left, #close').click(function (e) {
//       console.log(this);
//       var $navigacia = $('body, #slide-menu-left'),
//       val = $navigacia.css('left') === '250px' ? '0px' : '250px';
//       $navigacia.animate({
//       left: val
//       }, 300)
//       return false;
//     });
//     $('#small-menu-right, #close').click(function (e) {
//       var $navigaciaBody = $('body'),
//           $navigaciaDiv  = $('#slide-menu-right'),
//           val            = $navigaciaDiv.css('right') === '-250px' ? '0px' : '-250px',
//           val2           = $navigaciaDiv.css('right') === '-250px' ? '-250px' : '0px';
//           console.log(val);
//       $navigaciaDiv.animate({
//       right: val
//       }, 300);
//       $navigaciaBody.animate({
//       left: val2
//       }, 300)
//       return false;
//     });


//     $('#push-left, #close').click(function (e) {
//       var $navigacia = $('body, #slide-menu-left'),
//       val = $navigacia.css('left') === '250px' ? '0px' : '250px';
//       $navigacia.animate({
//       left: val
//       }, 300)
//       return false;
//     });