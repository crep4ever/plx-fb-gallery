<?php if(!defined('PLX_ROOT')) exit; ?>

<table class="thumbs" cellspacing="0" cellpadding="0" border="0">
	<tbody>
		<?php	
			$i = 0;
			foreach($galerie as $galImg) {
				if($i == 0) {
					echo "<tr>";
				}
		?>
			<td>
				<a title="<?php echo $galImg['title']; ?>" rel="<?php echo "shadowbox[".$randstr."]"; ?>" href="<?php echo $galImg['file']; ?>">
					<img class="border" alt="<?php echo $galImg['alt']; ?>" src="<?php echo $galImg['thumb']; ?>" />
				</a>
			</td>
		<?php	
				if($i == 2) { echo "</tr>"; $i = 0; }
				else { $i++; }
			}
		
		?>
	</tbody>
</table>
