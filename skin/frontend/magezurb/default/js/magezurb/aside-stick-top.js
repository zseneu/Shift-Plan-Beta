jQuery( document ).ready(function( $ ) {
    var asideStickTop = function(){
        var closed = true;
        jQuery('#ast-control').on('click', function(e){
            var $this = jQuery(this);
            if (closed){
                jQuery('#ast-container').addClass('ast-make-large');
                closed = false;
                $this.find('span').html('Close');
            } else {
                jQuery('#ast-container').removeClass('ast-make-large');
                closed = true;
                $this.find('span').html('Show');
            }
            e.preventDefault();
        });
    }
    asideStickTop();


    // Remove decimels from pricing
    jQuery(".price").each(function(){
        var span = jQuery(this).text();
        var done = span.replace(".00", "");
        
        jQuery(this).text(done);
    });

    

});