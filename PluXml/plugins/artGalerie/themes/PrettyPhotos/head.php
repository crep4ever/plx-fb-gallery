<?php if(!defined('PLX_ROOT')) exit; ?>

<?php
	echo "\t".'<link rel="stylesheet" href="'.PLX_PLUGINS.'artGalerie/themes/PrettyPhotos/galerie.css" type="text/css" media="screen" />'."\n";
	echo "\t".'<link rel="stylesheet" href="'.PLX_PLUGINS.'artGalerie/themes/PrettyPhotos/css/prettyPhoto.css" type="text/css" media="screen" />'."\n";
	echo "\t".'<script type="text/javascript" src="'.PLX_PLUGINS.'artGalerie/themes/PrettyPhotos/js/jquery.prettyPhoto.js"></script>'."\n";
	echo '<script type="text/javascript">
	       jQuery(function($) { 
	         $("a[rel^=\'prettyPhoto\']").prettyPhoto({social_tools: false});
		});
	</script>';
?>
