<?php if(!defined('PLX_ROOT')) exit; ?>

<div class="prettyphotos-thumbnails">
	<?php	foreach($galerie as $galImg) {?>
		<a href="<?php echo $galImg['file']; ?>" rel="<?php echo "prettyPhoto[".$randstr."]"; ?>" title="<?php echo $galImg['title']; ?>"><img src="<?php echo $galImg['thumb']; ?>" alt="<?php echo $galImg['alt']; ?>" /></a>
	<?php	}?>
	<div style="clear:left;"></div>
</div>
