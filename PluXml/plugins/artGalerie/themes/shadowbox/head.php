<?php if(!defined('PLX_ROOT')) exit; ?>

<?php
	echo "\t".'<link rel="stylesheet" href="'.PLX_PLUGINS.'artGalerie/themes/shadowbox/galerie.css" type="text/css" media="screen" />'."\n";
	echo "\t".'<link rel="stylesheet" href="'.PLX_PLUGINS.'artGalerie/themes/shadowbox/shadowbox.css" type="text/css" media="screen" />'."\n";
	echo "\t".'<script type="text/javascript" src="'.PLX_PLUGINS.'artGalerie/themes/shadowbox/shadowbox.js"></script>'."\n";
	echo '<script>
		Shadowbox.init({ overlayOpacity: 0.8 });
	</script>';
?>
