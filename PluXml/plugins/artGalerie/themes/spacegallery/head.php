<?php if(!defined('PLX_ROOT')) exit; ?>

<?php
	echo "\t".'<link rel="stylesheet" href="'.PLX_PLUGINS.'artGalerie/themes/spacegallery/css/spacegallery.css" type="text/css" media="screen" />'."\n";
	echo "\t".'<link rel="stylesheet" href="'.PLX_PLUGINS.'artGalerie/themes/spacegallery/css/custom.css" type="text/css" media="screen" />'."\n";
	echo "\t".'<script type="text/javascript" src="'.PLX_PLUGINS.'artGalerie/themes/spacegallery/js/eye.js"></script>'."\n";
	echo "\t".'<script type="text/javascript" src="'.PLX_PLUGINS.'artGalerie/themes/spacegallery/js/utils.js"></script>'."\n";
	echo "\t".'<script type="text/javascript" src="'.PLX_PLUGINS.'artGalerie/themes/spacegallery/js/spacegallery.js"></script>'."\n";
	echo '<script>
		(function($){
				
				var initLayout = function() {
					$(\'#myGallery\').spacegallery({loadingClass: \'loading\'});
				};
				
				EYE.register(initLayout, \'init\');
			})(jQuery)
	</script>';
?>
