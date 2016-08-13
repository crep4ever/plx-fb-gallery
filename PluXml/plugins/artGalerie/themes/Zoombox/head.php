<?php if(!defined('PLX_ROOT')) exit; ?>

<?php
	echo "\t".'<link rel="stylesheet" href="'.PLX_PLUGINS.'artGalerie/themes/Zoombox/galerie.css" type="text/css" media="screen" />'."\n";
	echo "\t".'<link rel="stylesheet" href="'.PLX_PLUGINS.'artGalerie/themes/Zoombox/zoombox.css" type="text/css" media="screen" />'."\n";
	echo "\t".'<script type="text/javascript" src="'.PLX_PLUGINS.'artGalerie/themes/Zoombox/zoombox.js"></script>'."\n";
	echo '<script type="text/javascript">
	       jQuery(function($) { 
	        $("a.zoombox").zoombox();
		});
	</script>';
?>
