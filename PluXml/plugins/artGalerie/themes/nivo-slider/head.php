<?php if(!defined('PLX_ROOT')) exit; ?>

<?php
	echo "\t".'<link rel="stylesheet" href="'.PLX_PLUGINS.'artGalerie/themes/nivo-slider/nivo-slider.css" type="text/css" media="screen" />'."\n";
	echo "\t".'<link rel="stylesheet" href="'.PLX_PLUGINS.'artGalerie/themes/nivo-slider/themes/default/default.css" type="text/css" media="screen" />'."\n";
	echo "\t".'<script type="text/javascript" src="'.PLX_PLUGINS.'artGalerie/themes/nivo-slider/jquery.nivo.slider.pack.js"></script>'."\n";
	echo '<script type="text/javascript">
			$(window).load(function() {
				$(\'#slider\').nivoSlider({
					controlNavThumbs: false,
					effect: \'fade\',
					animSpeed: 500,
					pauseTime: 5000
				});
			});
		</script>';
?>
