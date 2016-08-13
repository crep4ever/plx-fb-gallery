<?php if(!defined('PLX_ROOT')) exit; ?>

<div class="spacegallery" id="myGallery">
	<?php	foreach($galerie as $galImg) {?>
		<img alt="<?php echo $galImg['alt']; ?>" src="<?php echo $galImg['file']; ?>" />
	<?php	}?>
</div>
