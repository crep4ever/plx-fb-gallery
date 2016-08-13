<?php if(!defined('PLX_ROOT')) exit; ?>

<div class="slider-wrapper theme-default">
    <div id="slider" class="nivoSlider">
		<?php	foreach($galerie as $galImg) {?>
		<a href="<?php echo $galImg['file']; ?>" onclick="window.open(this.href);return false;" ><img src="<?php echo $galImg['file']; ?>" data-thumb="<?php echo $galImg['thumb']; ?>" alt="<?php echo $galImg['alt']; ?>" title="<?php echo $galImg['title']; ?>"/></a>
		<?php	}?>
    </div>
</div>
