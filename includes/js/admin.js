/**
 * Arconix Portfolio Admin JS
 * 
 * Show/Hide the "External Link" textbox based on value of Link Type select box
 * 
 * @since   1.4.0
 */
jQuery(document).ready(function(){
    // Hide the textbox since the default link type is "image"
    jQuery('.cmb2-id--acp-link-value').hide();
    jQuery('#_acp_link_value').hide();
    // Show textbox for existing external links
    if(jQuery('#_acp_link_type').val() === 'external') {
        jQuery('.cmb2-id--acp-link-value').show();
        jQuery('#_acp_link_value').show();
    }
    
    // Show/Hide the textbox when the link type changes
    jQuery('#_acp_link_type').on('change', function() {
        if(jQuery(this).val() === 'external') {
            jQuery('.cmb2-id--acp-link-value').show();
            jQuery('#_acp_link_value').show();
        } 
        else {
            jQuery('.cmb2-id--acp-link-value').hide();
            jQuery('#_acp_link_value').hide();
        }
    });
});