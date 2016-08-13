<?php if(!defined('PLX_ROOT')) exit; ?>

<div class="zoombox-thumbnails">
	<?php	foreach($galerie as $galImg) {?>
	
	
		<a title="<?php echo $galImg['title']; ?>" class="zoombox zgallery<?php echo $randstr; ?>" href="<?php echo $galImg['file']; ?>">
			<img alt="<?php echo $galImg['alt']; ?>" src="<?php echo $galImg['thumb']; ?>" />
		</a>
	
	
	<?php	}?>
	<div style="clear:left;"></div>
</div>
