<?php if(!defined('PLX_ROOT')) exit; ?>

<div class="gallery-thumbnails">
	
	<?php foreach ($galeries as $galerie){ ?>
		<div class="gallery-thumbnail">
			<div class="gallery-thumbnail-img">
				<a href="<?php echo $galerie['url']; ?>"><img src="<?php echo $galerie['icon']; ?>" alt="gallery-icon" /></a>
			</div>
			<div class="gallery-thumbnail-desc"><?php echo $galerie['titre']; ?></div>
		</div>

	<?php } ?>
	<div style="clear:left;"></div>
</div>
