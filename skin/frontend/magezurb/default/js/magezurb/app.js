// Foundation JavaScript
// Documentation can be found at: http://foundation.zurb.com/docs
jQuery(document).ready(function(){
    jQuery(document).foundation(function (response) {
        console.log(response.errors);
        alert(response.errors);
    });
});