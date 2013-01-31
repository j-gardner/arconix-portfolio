<?php
echo '<div class="rss-widget">';

wp_widget_rss_output( array(
	'url' 			=> 'http://arconixpc.com/tag/arconix-portfolio/feed', // feed url
	'title' 		=> 'Arconix Portfolio Posts', // feed title
	'items' 		=> 3, //how many posts to show
	'show_summary' 	=> 1, // display excerpt
	'show_author' 	=> 0, // display author
	'show_date' 	=> 1 // display post date
) );

echo '<div class="acp-widget-bottom"><ul>';
?>
<li><a href="http://arcnx.co/apwiki"><img src="<?php echo ACP_IMAGES_URL . 'page-16x16.png'?>">Wiki Page</a></li>
<li><a href="http://arcnx.co/aphelp"><img src="<?php echo ACP_IMAGES_URL . 'help-16x16.png'?>">Support Forum</a></li>
<li><a href="http://arcnx.co/aptrello"><img src="<?php echo ACP_IMAGES_URL . 'trello-16x16.png'?>">Dev Board</a></li>
<li><a href="http://arcnx.co/apsource"><img src="<?php echo ACP_IMAGES_URL . 'github-16x16.png'?>">Source Code</a></li>
<?php
echo '</ul></div></div>';