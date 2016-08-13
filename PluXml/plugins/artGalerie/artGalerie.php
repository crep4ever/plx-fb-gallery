<?php
/**
 * Plugin artGalerie
 *
 * @package	PLX
 * @version	4.4
 * @date	11/04/2016
 * @author	Rockyhorror
 **/
 
class artGalerie extends plxPlugin {

	public $ActiveGalerie = '';	# Galerie en cours d'edition
	public $aGalerieDesc = array();	# Tableau des fichiers avec leurs description
	private $aGalerie = array(); # Tableau du contenu du fichier XML
	public $aGalParametres = array(); # parametres de la galerie active
	public $galerieTitle = array(); # Tableau contenant le titre des galeries
	private $path = null; # chemin vers les médias
	private $thumbMotif = '/.tb.(jpg|gif|png|bmp|jpeg)$/i';
	
	/**
	 * Constructeur de la classe artGalerie
	 *
	 * @param	default_lang	langue par défaut utilisée par PluXml
	 * @return	null
	 * @author	Rockyhorror
	 **/
	public function __construct($default_lang) {

		# Appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);

		# Autorisation d'acces à la configuration du plugins
		$this-> setConfigProfil(PROFIL_ADMIN, PROFIL_MANAGER);

		# Autorisation d'accès à l'administration du plugin
		$this->setAdminProfil(PROFIL_ADMIN, PROFIL_MANAGER);
		
		# Déclarations des hooks
		$this->addHook('ArtgalerieDisplay','ArtgalerieDisplay');
		$this->addHook('ThemeEndHead', 'ThemeEndHead');
		$this->addHook('AdminArticleSidebar', 'AdminArticleSidebar');
		$this->addHook('plxAdminEditArticleXml', 'plxAdminEditArticleXml');
		$this->addHook('plxMotorParseArticle', 'plxMotorParseArticle');
		$this->addHook('staticGalerieShow', 'staticGalerieShow');
		$this->addHook('plxToolbarCustomsButtons', 'artGalerieButton');
		$this->addHook('AdminArticlePreview', 'AdminArticlePreview');
		$this->addHook('AdminArticlePostData', 'AdminArticlePostData');
		$this->addHook('AdminArticleParseData', 'AdminArticleParseData');
		$this->addHook('AdminArticleInitData', 'AdminArticleInitData');
		$this->addHook('plxShowStaticContent', 'plxShowStaticContent');
		$this->addHook('AdminStatic', 'AdminStatic');
		$this->addHook('plxAdminEditStatiquesXml', 'plxAdminEditStatiquesXml');
		$this->addHook('plxAdminEditStatique', 'plxAdminEditStatique');
		$this->addHook('plxAdminEditStatiquesUpdate', 'plxAdminEditStatiquesUpdate');
		$this->addHook('plxMotorGetStatiques', 'plxMotorGetStatiques');
		$this->addHook('plxMotorDemarrageEnd', 'plxMotorDemarrageEnd');
		$this->addHook('plxFeedRssArticlesXml', 'plxFeedRssArticlesXml');
	}


	public function OnActivate() {
		$plxAdmin = plxAdmin::getInstance();
		if (version_compare(PLX_VERSION, "5.1.7", ">=")) {
			if (!file_exists($this->plug['parameters.xml'])) {
				$this->setParam('root_dir', "photos", 'cdata');
				$this->setParam('show_thumb', 1, 'numeric');
				$this->setParam('enableManualMethod', 0, 'numeric');
				$this->setParam('theme', "default", 'cdata');
				$this->setParam('sortorder', "natural", 'cdata');
				$this->saveParams(); 
			}
		}
	}

	public function AdminArticlePostData () {
		echo '<?php $galerie = $_POST["galerie"]; ?>';
	}
	
	public function AdminArticleParseData () {
		echo '<?php $galerie = $result["galerie"]; ?>';
	}
	
	public function AdminArticleInitData () {
		echo '<?php $galerie = ""; ?>';
	}

	public function AdminArticlePreview () {
		echo '<?php if(!empty($_POST["galerie"])) { $art["galerie"] = $_POST["galerie"]; } ?>';
	}

	/**
	 * Méthode qui ajoute le champs 'Galerie' dans l'edition de l'article
	 *
	 * @return	stdio
	 * @author	Rockyhorror
	 **/
	public function AdminArticleSidebar(){
			echo '<div class="grid">
					<div class="col sml-12">
						<label for="id_galerie">
							'.$this->getlang('L_PATH') .'&nbsp;:&nbsp;
							<a class="hint"><span>'.$this->getlang('L_ARTICLE_GALERIE_FIELD_TITLE') .'</span></a>
						</label>
						<?php $plxAdmin->plxPlugins->aPlugins["artGalerie"]->ActiveGalerie = $galerie; ?>
						<?php echo $plxAdmin->plxPlugins->aPlugins["artGalerie"]->contentFolder(); ?>
					</div>
				</div>';
	}

	public function plxAdminEditArticleXml(){
		echo "<?php \$xml .= '\t'.'<galerie><![CDATA['.plxUtils::cdataCheck(trim(\$content['galerie'])).']]></galerie>'.'\n'; ?>";
	}

	public function plxMotorParseArticle(){
		echo "<?php	\$art['galerie'] = isset(\$iTags['galerie'])?plxUtils::getValue(\$values[\$iTags['galerie'][0]]['value']):''; ?>";
	}

	/*
	 * Fonction qui gère la mise à jour de la liste des pages statiques.
	 * 
	 * @return null
	 * @author Rockyhorror
	 * 
	 */ 
	public function plxAdminEditStatiquesUpdate() {
		echo "<?php \$this->aStats[\$static_id]['galerie'] = (isset(\$this->aStats[\$static_id]['galerie'])?\$this->aStats[\$static_id]['galerie']:''); ?>";
	}

	/**
	 * Méthode qui ajoute l'insertion du code javascript dans la partie <head> du site
	 *
	 * @return	stdio
	 * @author	Rockyhorror
	 **/	
	public function ThemeEndHead() {
		$theme = $this->getParam('theme');
		echo "\t".'<link rel="stylesheet" href="'.PLX_PLUGINS.'artGalerie/themes/default/artGalerie.css" type="text/css" media="screen" />'."\n";
		include(PLX_PLUGINS.'artGalerie/themes/'.$theme.'/head.php');
	}


	/**
	 * Méthode qui ajoute le champ 'galerie' dans la page d'édition de la page statique
	 *
	 * @return	stdio
	 * @author	Rockyhorror
	 **/
	public function AdminStatic() {
		echo '<?php
				$galerie = plxUtils::getValue($plxAdmin->aStats[$id]["galerie"]);
				$plxAdmin->plxPlugins->aPlugins["artGalerie"]->ActiveGalerie = $galerie;		
			?>
			<div class="grid">
				<div class="col sml-12">
					<label for="id_galerie">'.$this->getlang('L_PATH').'&nbsp;:&nbsp
						<a class="hint"><span>'.$this->getlang('L_ARTICLE_GALERIE_FIELD_TITLE') .'</span></a>
					</label>
					<?php echo $plxAdmin->plxPlugins->aPlugins["artGalerie"]->contentFolder(); ?>
				</div>
			</div>';
	}


	/**
	 * Méthode qui rajoute la galerie dans la chaine xml à sauvegarder
	 *
	 * @return	stdio
	 * @author	Rockyhorror
	 **/
    public function plxAdminEditStatiquesXml() {
		echo "<?php \$xml .= '<galerie><![CDATA['.plxUtils::cdataCheck(trim(\$static['galerie'])).']]></galerie>'; ?>";
    }
    
    /**
	 * Méthode qui récupère la galerie saisit lors de l'édition de la page statique
	 *
	 * @return	stdio
	 * @author	Rockyhorror
	 **/
    public function plxAdminEditStatique() {
		echo "<?php \$this->aStats[\$content['id']]['galerie'] = (!empty(\$content['galerie']) ? \$content['galerie'] : ''); ?>";
    }
    
    
    /**
	 * Méthode qui récupère la galerie stockée dans le fichier xml statiques.xml
	 *
	 * @return	stdio
	 * @author	Rockyhorror
	 **/
    public function plxMotorGetStatiques() {
		echo "<?php \$galerie = plxUtils::getValue(\$iTags['galerie'][\$i]); ?>";
		echo "<?php \$this->aStats[\$number]['galerie']=plxUtils::getValue(\$values[\$galerie]['value']); ?>";
	}
	
	/**
	 * Méthode qui parse le fichier XML de description des images de la galerie
	 *
	 * @return	stdio
	 * @author	Rockyhorror
	 **/
	private function parseXML($dir) {
		
		$galerie = substr($dir, strrpos($dir,'/'));
		$filename = $dir."/".$galerie.".xml";
		
		if (!file_exists($filename)) { return; }
		
		# Mise en place du parseur XML
		$data = implode('',file($filename));
		$parser = xml_parser_create(PLX_CHARSET);
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,0);
		xml_parse_into_struct($parser,$data,$values,$iTags);
		xml_parser_free($parser);

		if(isset($iTags['image'])) {
			foreach($iTags['image'] as $v) {
				$this->aGalerie[$values[$v]['attributes']['name']] = plxUtils::getValue($values[$v]['value']);
			}
		}
		if(isset($iTags['parametre'])) {
			foreach($iTags['parametre'] as $v) {
				$this->aGalParametres[ $values[$v]['attributes']['name'] ] = $values[$v]['value'];
			}
		}
	}
	
	private function myGetAllDirs($dir, $level=0) {
		
		# Initialisation
		$folders = array();
		
		$alldirs = scandir($dir);
		natsort($alldirs);
		
		foreach($alldirs as $folder) {
			if($folder[0] != '.') {
				if(is_dir(($dir!=''?$dir.'/':$dir).$folder)) {
					$dir = (substr($dir, -1)!='/' AND $dir!='') ? $dir.'/' : $dir;
					$path = str_replace($this->path, '',$dir.$folder);
					$folders[] = array(
							'level' => $level,
							'name' => $folder,
							'path' => $path
						);

					$folders = array_merge($folders, $this->myGetAllDirs($dir.$folder, $level+1) );
				}
			}
		}
		
		return $folders;
	}
	
	public function contentFolder() {
		$plxAdmin = plxAdmin::getInstance();
		
		$this->path = PLX_ROOT.$plxAdmin->aConf['medias'].$this->getParam('root_dir').'/';
		$this->aDirs = (is_dir($this->path)?$this->myGetAllDirs($this->path):"");
		$str  = "\n".'<select class="no-margin" id="id_galerie" size="1" name="galerie">'."\n";
		$selected = (empty($this->ActiveGalerie)?'selected="selected" ':'');
		$str .= '<option '.$selected.'value="">|. ('.L_PLXMEDIAS_ROOT.') &nbsp; </option>'."\n";
		# Dir non vide
		if(!empty($this->aDirs)) {
			foreach($this->aDirs as $k => $v) {
				$prefixe = '|&nbsp;&nbsp;';
				$i = 0;
				while($i < $v['level']) {
					$prefixe .= '&nbsp;&nbsp;';
					$i++;
				}
				$selected = ($v['path']==$this->ActiveGalerie?'selected="selected" ':'');
				$str .= '<option '.$selected.'value="'.$v['path'].'">'.$prefixe.$v['name'].'</option>'."\n";
			}
		}
		$str  .= '</select>'."\n";

		# On retourne la chaine
		return $str;
	}
	
	/**
	 * Méthode qui parse le contenue d'une galerie
	 *
	 * @return	stdio
	 * @author	Rockyhorror
	 **/
	public function parseGalerie($galerie){
		
		if(empty($galerie)) { return; }
		
		$galeriePath = PLX_ROOT.($this->sanitize_path($galerie));
		$glob = plxGlob::getInstance($galeriePath);
		$this->parseXML($galeriePath);
		$this->parseXMLTitle();
		if ($files = $glob->query($this->thumbMotif, '', 'sort')) {
			$num = 0;
				foreach($files as $file){
					$this->aGalerieDesc[$num]['img'] = $galeriePath.'/'.$file;
					$this->aGalerieDesc[$num]['titre'] = str_replace('.tb', '', $file);
					$this->aGalerieDesc[$num]['tb'] = $file;
					$this->aGalerieDesc[$num]['desc'] = (isset($this->aGalerie[$file])?$this->aGalerie[$file]:"");
					$num++;
				}
		}	
	}
	
	/*
	 *  Méthode qui lit le fichier de titre des galeries
	 * 
	 * @return stdio
	 * @author Rockyhorror
	 * 
	 */ 
	private function parseXMLTitle() {
		$filename = PLX_ROOT.PLX_CONFIG_PATH.'plugins/artGalerieTitle.xml';
		if(!file_exists($filename)) return;
			
		# Mise en place du parseur XML
		$data = implode('',file($filename));
		$parser = xml_parser_create(PLX_CHARSET);
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,0);
		xml_parse_into_struct($parser,$data,$values,$iTags);
		xml_parser_free($parser);
		
		if(isset($iTags['galerie'])) {
			foreach($iTags['galerie'] as $v) {
				$this->galerieTitle[$values[$v]['attributes']['name']] = plxUtils::getValue($values[$v]['value']);
			}
		}
	}
	
	/*
	 *  Methode qui ecrit le fichier XML de titre des galeries
	 * 
	 *  @return stdio
	 *  @author Rockyhorror
	 */ 
	 private function saveGalerieTitle($galerie, $title) {
		$filename = PLX_ROOT.PLX_CONFIG_PATH.'plugins/artGalerieTitle.xml';
		
		$this->parseXMLTitle();
		$galerie = plxUtils::strCheck($galerie);
		if(empty($title) && isset($this->galerieTitle[$galerie])) {
			unset($this->galerieTitle[$galerie]);
		}
		else {
			$this->galerieTitle[$galerie] = plxUtils::strCheck(trim($title));
		}
		
		$xml = "<?xml version=\"1.0\" encoding=\"".PLX_CHARSET."\"?>\n";
		$xml .= "<document>\n";
		foreach($this->galerieTitle as $k => $v){
			$xml .= "\t<galerie name=\"".$k."\"><![CDATA[".$v."]]></galerie>\n";
		}
		$xml .= "</document>";
		if(plxUtils::write($xml, $filename)) {
			return plxMsg::Info(L_SAVE_SUCCESSFUL);
		}
		else {
			return plxMsg::Error(L_SAVE_ERR.' '.$filename);
		}
	 }
	 
	 /*
	  * Méthode qui nettoie les galeries qui n'existe plus du fichier de configuration 
	  * 
	  * @return stido
	  * @author Rockyhorror
	  */
	  public function cleanOrphanTitle () {
		$plxAdmin = plxAdmin::getInstance();
		$filename = PLX_ROOT.PLX_CONFIG_PATH.'plugins/artGalerieTitle.xml';
		
		$this->parseXMLTitle();
		$xml = "<?xml version=\"1.0\" encoding=\"".PLX_CHARSET."\"?>\n";
		$xml .= "<document>\n";
		foreach($this->galerieTitle as $k => $v) {
			$path = PLX_ROOT.$plxAdmin->aConf['medias'].$this->getParam('root_dir').'/'.$k;
			if (is_dir($path)) {
				$xml .= "\t<galerie name=\"".$k."\"><![CDATA[".$v."]]></galerie>\n";
			}
		}
		$xml .= "</document>";
		
		if(plxUtils::write($xml, $filename)) {
			return plxMsg::Info(L_SAVE_SUCCESSFUL);
		}
		else {
			return plxMsg::Error(L_SAVE_ERR.' '.$filename);
		}
	  }
	
	/**
	 * Méthode qui ecrit le fichier XML de description des galeries
	 *
	 * @return	stdio
	 * @author	Rockyhorror
	 **/
	public function editGaleries($content){
		if(isset($content['galerie_title'])) {
			$this->saveGalerieTitle($content['galerie'], $content['galerie_title']);
		}
		if(!empty($content['imgNum'])){
			# On génére le fichier XML
			$xml = "<?xml version=\"1.0\" encoding=\"".PLX_CHARSET."\"?>\n";
			$xml .= "<document>\n";
			$xml .= isset($content['thumbdesc'])? "\t<parametre name=\"thumbdesc\">1</parametre>\n":"<parametre name=\"thumbdesc\">0</parametre>\n";			
			foreach($content['imgNum'] as $v) {
				if (!empty($content[$v.'_desc'])){
					$xml .= "\t<image name=\"".plxUtils::cdataCheck($content[$v.'_tb'])."\"><![CDATA[".plxUtils::strCheck(trim($content[$v.'_desc']))."]]></image>\n";
				}
			}
			$xml .= "</document>";
			
			# On écrit le fichier
			$galName = substr($content['galerie'], strrpos($content['galerie'],'/'));
			$filename = PLX_ROOT.($this->sanitize_path($content['galerie']).'/'.$galName.'.xml');
			if(plxUtils::write($xml, $filename))
				return plxMsg::Info(L_SAVE_SUCCESSFUL);
			else {
				return plxMsg::Error(L_SAVE_ERR.' '.$filename);
			}
			
		}
	}
	
	/*
	 * Méthode qui affiche la galerie
	 * 
	 * @return	stdio
	 * @author	Rockyhorror
	 * 
	 */
	private function galerieDisplay($galerie_path) {
		$glob = plxGlob::getInstance($galerie_path);
		
		if ($files = $glob->query($this->thumbMotif, '', 'sort')) {
			$galerie = array();
			$randstr = mt_rand(1000, 9999);
			$this->parseXML($galerie_path); 
			$galerieTitle = empty($this->galerieTitle[$this->ActiveGalerie])?'':$this->galerieTitle[$this->ActiveGalerie];
			$showThumbDesc = isset($this->aGalParametres['thumbdesc'])?$this->aGalParametres['thumbdesc']: 0;
			foreach($files as $idx => $filename) {
				$basename = str_replace('.tb', '', $filename);
				$galerie[$idx]['thumb'] = $galerie_path.'/'.$filename;
				$galerie[$idx]['file'] = $galerie_path.'/'.$basename;
				$galerie[$idx]['alt'] = substr($basename, 0,strrpos($basename,'.'));
				$galerie[$idx]['title'] = isset($this->aGalerie[$filename]) ? $this->aGalerie[$filename] :$galerie[$idx]['alt'];
			}
			
			/*
			 * Variables disponible pour les fichiers du thème
			 * $showThumbDesc: True/false, faut il afficher les descriptions pour les vignettes
			 * $galerieTitle : Titre de la galerie
			 * $galerie : tableau contenant les paramètres des images de la galerie.
			 */ 
			$theme = $this->getParam('theme');
			include(PLX_PLUGINS.'artGalerie/themes/'.$theme.'/galerie.php');
		}
	}
	
	public function scansubdir($dir, $tri='natural') {
		$aDirs = array();
		
		if(is_dir($dir)) {
			# On ouvre le repertoire
			if($dh = opendir($dir)) {
				# Pour chaque entree du repertoire
				while(false !== ($file = readdir($dh))) {
					if($file[0]!='.') {
						if(is_dir($dir.'/'.$file)) {
							if ($tri == 'mtime' or $tri == 'mtime_r'){
								$aDirs[filectime($dir.'/'.$file)] = $file;
							}
							else {
								$aDirs[] = $file;
							}
						}
					}
				}
				# On ferme la ressource sur le repertoire
				closedir($dh);
			}
			switch($tri) {
				case 'natural':
					natsort($aDirs);
					break;
				case 'mtime':
					ksort($aDirs);
					break;
				case 'mtime_r':
					krsort($aDirs);
					break;
			}
		}
		return $aDirs;
	}
	
	private function s_glob($dir, $regx){
		$files = array();
		if(is_dir($dir)){
			if($dh=opendir($dir)){
				while(($file = readdir($dh)) !== false){
					if (preg_match($regx, $file)) {
						$files[]=$dir.$file;
					}
				}
			}
		}
		return $files;
	}
	
	/*
	 * Methode pour nettoyer un chemin des '/./' '/../' '//'
	 * 
	 * @input string	chemin relatif à nettoyer
	 * @return string	chemin absolue propre
	 * @author Rockyhorror
	 * 
	 */ 
	public function sanitize_path($path) {
		if (defined('PLX_ADMIN')) {
			$plxMotor = plxAdmin::getInstance();
		}
		else {
			$plxMotor = plxMotor::getInstance();
		}
		
		$parts = explode('/', plxUtils::strCheck($path));
		foreach($parts as $idx => $part) {
			if($part == '.' | $part == '..' | empty($part)) { unset($parts[$idx]); }
		}
		$safePath = $plxMotor->aConf['medias'].$this->getParam('root_dir').'/'.implode('/', $parts);
		return $safePath;
	}
	
	/*
	 * Méthode qui affiche la galerie d'une statique
	 * 
	 * @return stdio
	 * @author Rockyhorror
	 * 
	 */ 
	public function plxShowStaticContent() {
		echo '<?php
			$galerie = isset($this->plxMotor->aStats[ $this->plxMotor->cible ][\'galerie\'])?$this->plxMotor->aStats[ $this->plxMotor->cible ][\'galerie\']:"";
			if(empty($galerie)) { return; }
			$plxPlugin = $this->plxMotor->plxPlugins->getInstance(\'artGalerie\');
			ob_start();
			$plxPlugin->ArtgalerieDisplay($galerie);
			$galcontent = ob_get_clean();
			$output .= $galcontent;
		?>';
	}
	
	
	/**
	 * Méthode qui affiche la galerie dans un article ou une page statique
	 *
	 * @return	stdio
	 * @author	Rockyhorror
	 **/
	public function ArtgalerieDisplay($static_path) {
		$plxMotor = plxMotor::getInstance();
		
		if ($plxMotor->mode == 'article'){
			$galerie_path = (!empty($static_path))?$static_path :$plxMotor->plxRecord_arts->f('galerie');
		}
		elseif($plxMotor->mode == 'static' OR $plxMotor->mode == 'home' OR $plxMotor->mode == 'categorie' OR $plxMotor->mode == 'archives' OR $plxMotor->mode == 'tags') {
			$galerie_path = (!empty($static_path))?$static_path :'';
		}
		else {
			$galerie_path = '';
		}
		
		if(empty($galerie_path)){
			return;
		}
		
		$this->ActiveGalerie = $galerie_path;
		$root_dir = $this->sanitize_path($galerie_path);
		if(!is_dir($root_dir)) {
			return;
		}
		$this->parseXMLTitle();
		$this->galerieDisplay($root_dir);
	}
	
	/**
	 * Méthode qui liste les galeries et les affiches sous forme d'icone
	 *
	 * @return	stdio
	 * @author	Rockyhorror
	 **/
	public function staticGalerieShow($static_path) {
		$plxMotor = plxMotor::getInstance();
		
		if($plxMotor->mode != 'static' or empty($static_path)) {
			return;
		}
		
		$path = !empty($_GET['galerie']) ? $_GET['galerie'] : $static_path;
		$root_dir = $this->sanitize_path($path);
		if(!is_dir($root_dir)) {
			return;
		}
		$dirs = $this->scansubdir($root_dir, $this->getParam('sortorder'));
		$this->parseXMLTitle();
		if (count($dirs) > 0) {
			$showThumb = $this->getParam('show_thumb');
			$i = 0;
			foreach ($dirs as $dir){
				$this->ActiveGalerie = $path."/".$dir;
				$galeries[$i]['titre'] = empty($this->galerieTitle[$this->ActiveGalerie])?$dir:$this->galerieTitle[$this->ActiveGalerie];
				$galeries[$i]['url'] = $plxMotor->urlRewrite('?static'.intval($plxMotor->cible).'/'.$plxMotor->aStats[$plxMotor->cible]['url'].'&galerie='.$path.'/'.$dir);
				if ($showThumb) {
					$imgFiles = $this->s_glob($root_dir.'/'.$dir.'/', $this->thumbMotif);
					$galeries[$i]['icon'] = (empty($imgFiles)?PLX_PLUGINS.'artGalerie/gallery-icon.png':$imgFiles[array_rand($imgFiles)]);
				}
				else {
					$galeries[$i]['icon'] = PLX_PLUGINS.'artGalerie/gallery-icon.png';
				}
				$i++;
			}
			include(PLX_PLUGINS.'artGalerie/themes/static-galeries.php');
		}
		else {
			$this->ActiveGalerie = $path;
			$this->galerieDisplay($root_dir);
		}

	}


	/*
	 * Méthode qui retourne le contenu de la galerie à afficher dans le corps de l'article
	 * 
	 * @return: stdio
	 * @author: rockyhorror
	 * 
	 */ 
	public function replace_callback($matches) {
		
		$galerie = (empty($matches[1]))?'':plxUtils::strCheck($matches[1]);
		ob_start();
		$this->ArtgalerieDisplay($galerie);
		$galcontent = ob_get_clean();
		return $galcontent;
	}


	/*
	 * Méthode qui cherche dans le contenu d'un article, et affiche la galerie à l'endroit voulu
	 * 
	 * @return stdio
	 * @author: Rockyhorror
	 */ 
	public function plxMotorDemarrageEnd() {
		
		if (!$this->getParam('enableManualMethod')) { return; }
		
		$plxMotor = plxMotor::getInstance();
		if($plxMotor->mode == 'article'){
			$text = preg_replace_callback('/\(artgalerie#([a-zA-Z0-9-_\/]*)\)/', array($this, 'replace_callback'), $plxMotor->plxRecord_arts->f('content'));
			if (!is_null($text)) {
				$plxMotor->plxRecord_arts->result[0]['content'] = $text;
			}
		}
		elseif($plxMotor->mode == 'home' OR $plxMotor->mode == 'categorie' OR $plxMotor->mode == 'archives' OR $plxMotor->mode == 'tags') {
			while($plxMotor->plxRecord_arts->loop()){
				if($plxMotor->plxRecord_arts->f('chapo') == ''){
					$text = preg_replace_callback('/\(artgalerie#([a-zA-Z0-9-_\/]*)\)/', array($this, 'replace_callback'), $plxMotor->plxRecord_arts->f('content'));
					if (!is_null($text)) {
						$plxMotor->plxRecord_arts->result[$plxMotor->plxRecord_arts->i]['content'] = $text;
					}
				}
			}
		}
	}
	
	
	
	/*
	 * Méthode qui supprime la référence à la galerie dans le fil des articles
	 * 
	 * @return stdio
	 * @author: rockyhorror
	 * 
	 */ 
	public function plxFeedRssArticlesXml() {
		if (!$this->getParam('enableManualMethod')) { return; }
		echo "<?php
			\$text = preg_replace('/\(artgalerie#([a-zA-Z0-9-_\/]*)\)/', '', \$entry);
			if (!is_null(\$text)) {
				\$entry = \$text;
			}
			?>";	
	}

	public function artGalerieButton() { ?>
		<script type="text/javascript">
			<!--
			plxToolbar.addButton( {
				icon : '<?php echo PLX_PLUGINS ?>artGalerie/lightbox.png',
				title : 'artGalerie',
				onclick : function(textarea) { 
					plxToolbar.openPopup('<?php echo PLX_PLUGINS ?>artGalerie/medias.php?id='+textarea, 'Medias', '750', '580');
					return '';
					}
			});
			-->
		</script>
		<?php
	}

}
?>
