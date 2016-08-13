<?php if(!defined('PLX_ROOT')) exit; ?>
<div class="Titre"><?php echo $galerieTitle; ?></div>
<div class="gallery-thumbnails">
	<?php	foreach($galerie as $galImg) {?>
	<div class="gallery-thumbnail">
		<div class="gallery-thumbnail-img">
			<a title="<?php echo $galImg['title']; ?>" rel="<?php echo "artgalerie-".$randstr; ?>" href="<?php echo $galImg['file']; ?>">
				<img alt="<?php echo $galImg['alt']; ?>" src="<?php echo $galImg['thumb']; ?>" />
			</a>
		</div>
		<?php if($showThumbDesc) { echo '<div class="gallery-thumbnail-desc">'.$galImg['title'].'</div>'; }?>
	</div>
	<?php	}?>
	<div style="clear:left;"></div>
</div>
