
jQuery(document).ready ( function() {
    // make header link to home
    jQuery('#headimg')
        .click( 
            function() { 
                location.href=EnchantData.home_url;
            })
        .css( 'cursor', 'pointer' );
});
