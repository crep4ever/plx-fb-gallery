<?php
/**
 * Plugin artGalerie
 *
 * @package     PLX
 * @version     4.4
 * @date        11/04/2016
 * @author      rockyhorror
 **/


if(!defined('PLX_ROOT')) exit;
# Control du token du formulaire
plxToken::validateFormToken($_POST);

	
	if(!empty($_POST['save'])) {
		$plxPlugin->setParam('root_dir', $_POST['root_dir'], 'cdata');
		$plxPlugin->setParam('show_thumb', isset($_POST['show_thumb'])?1:0, 'numeric');
		$plxPlugin->setParam('enableManualMethod', isset($_POST['enableManualMethod'])?1:0, 'numeric');
		$plxPlugin->setParam('theme', $_POST['theme'], 'cdata');
		$plxPlugin->setParam('sortorder', $_POST['sortorder'], 'cdata');
		$plxPlugin->saveParams();
		header('Location: parametres_plugin.php?p=artGalerie');
		exit;
	}
	elseif(isset($_POST['clean'])) {
		$plxPlugin->cleanOrphanTitle();
	}
	elseif(isset($_POST['migrate'])){
		include("utils.php");
		$plxAdmin = plxAdmin::getInstance();
		$convert = new galerieUtils();
		$convert->convertgaleries(PLX_ROOT.(empty($plxAdmin->aConf['medias'])?$plxAdmin->aConf['images']:$plxAdmin->aConf['medias']).$plxPlugin->getParam('root_dir'));
	}

 ?>

	<form action="parametres_plugin.php?p=artGalerie" method="post">
	
	<fieldset class="withlabel">
		<p><?php echo $plxPlugin->getLang('L_CONFIG_ROOT_DIR') ?></p>
		<?php plxUtils::printInput('root_dir', $plxPlugin->getParam('root_dir'), 'text'); ?>
		
		<p><?php echo $plxPlugin->getLang('L_CONFIG_GAL_THEME') ?></p>
		<?php
			$themes_dir = $plxPlugin->scansubdir(PLX_PLUGINS.'artGalerie/themes');
			foreach($themes_dir as $theme_dir) {
				$theme_list[$theme_dir] = $theme_dir;
			}
			plxUtils::printSelect('theme', $theme_list, $selected=$plxPlugin->getParam('theme'));
		?>
		
		<p><?php echo $plxPlugin->getLang('L_CONFIG_SHOW_THUMB') ?>
		<input type="checkbox" name="show_thumb" value="True" <?php if($plxPlugin->getParam('show_thumb')) { echo 'checked="true"'; }?>/></p>
		
		<p><?php echo $plxPlugin->getLang('L_CONFIG_SORT_ORDER');
		$sortorder['natural'] = 'natural';
		$sortorder['mtime'] = 'mtime';
		$sortorder['mtime_r'] = 'mtime_r';
		plxUtils::printSelect('sortorder', $sortorder, $selected=$plxPlugin->getParam('sortorder')); ?>
		</p>
		
		<p>
			<?php echo $plxPlugin->getLang('L_MANUAL_METHOD'); ?>
			<input type="checkbox" name="enableManualMethod" value="True" <?php if($plxPlugin->getParam('enableManualMethod')) { echo 'checked="true"'; }?>/></p>
		</p>
		
		<p>
			<?php $plxPlugin->lang('L_CLEAN_GALLERY'); ?>
			<input type="submit" name="clean" value="<?php $plxPlugin->lang('L_CLEAN'); ?>">
		</p>
		<p>
			<?php $plxPlugin->lang('L_MIGRATE_GALLERY'); ?>
			<input type="submit" name="migrate" value="Go">
		</p>
	</fieldset>
	<br />
	<?php echo plxToken::getTokenPostMethod() ?>
	<input type="submit" name="save" value="<?php echo $plxPlugin->getLang('L_SAVE') ?>" />

	</form>
