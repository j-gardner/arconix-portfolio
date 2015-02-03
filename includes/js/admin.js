/**
 * Arconix Portfolio Admin JS
 * 
 * Show/Hide the "Optional Link" textbox based on value of Link Type select
 * 
 * @since   1.4.0
 */
jQuery(document).ready( function(){
    // Hide the textbox since the default link type is "image"
    jQuery(".cmb_id__acp_link_value").hide();
    
    jQuery('#_acp_link_type').on('change', function() {
        var value = jQuery(this).val();
        
        if( value === "external") {
            jQuery(".cmb_id__acp_link_value").show();
        }
        else {
            jQuery(".cmb_id__acp_link_value").hide();
        }
    });
});