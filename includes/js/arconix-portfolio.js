/**
 * jQuery Quicksand javascript file
 * 
 * @link http://wp.tutsplus.com/tutorials/theme-development/create-a-quicksand-portfolio-with-wordpress/
 * @since 1.0
 * @version 1.3.0
 */
function portfolio_quicksand() {
        
    // Setting Up Our Variables
    var $filter;
    var $container;
    var $containerClone;
    var $filterLink;
    var $filteredItems;
    
    // Set Our Filter
    $filter = jQuery('.arconix-portfolio-features li.active a').attr('class');
    
    // Set Our Filter Link
    $filterLink = jQuery('.arconix-portfolio-features li a');
    
    // Set Our Container
    $container = jQuery('ul.arconix-portfolio-grid');
    
    // Clone Our Container
    $containerClone = $container.clone();
    
    // Apply our Quicksand to work on a click function
    // for each of the filter li link elements
    $filterLink.click(function(e) {
        // Remove the active class
        jQuery('.arconix-portfolio-features li').removeClass('active');
        
        // Split each of the filter elements and override our filter
        $filter = jQuery(this).attr('class').split(' ');
        
        // Apply the 'active' class to the clicked link
        jQuery(this).parent().addClass('active');
        
        // If 'all' is selected, display all elements
        // else output all items referenced to the data-type
        if ($filter == 'all') {
            $filteredItems = $containerClone.find('li'); 
        }
        else {
            $filteredItems = $containerClone.find('li[data-type~=' + $filter + ']'); 
        }
        
        // Finally call the Quicksand function
        $container.quicksand($filteredItems, {
            
            // The Duration for animation
            duration: 500,
            // the easing effect when animation
            easing: 'easeInOutQuad',
            // height adjustment becomes dynamic
            adjustHeight: 'dynamic'

        }, function(
                // Callback function -- put functions here that need to be re-applied after sorting
                // i.e. some lightbox scripts, tooltips, etc...
            ) { }
        );
        
    });
}

jQuery(document).ready(function(){
    if(jQuery().quicksand) {
        portfolio_quicksand();	
    }
});