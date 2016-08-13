<?php if(!defined('PLX_ROOT')) exit; ?>

<h2>Help</h2>
<p>Artgalerie plugin help file</p>

<h3>Installation</h3>
<P>To make this plugin work correctly, you must first install and activate the <b>Jquery plugin</b>.</P>
<p>&nbsp;</p>

<p>
	In your pictures directory (default: data/images), add a root directory (example: <i>Photos</i>).<BR />
	Inside <i>Photos</i> directory, add one subdirectory by gallery (example: <i>galerie01, galerie02</i>), that hold back your pictures.<BR />
</p>
<p>&nbsp;</p>
<p>
	In the plugin configuration, specify the root directory name (<i>Photos</i>)
</p>
<p>&nbsp;</p>
<p>Pictures should have been uploaded using Media Manager. Otherwise ensure to "re-create thumbnails"</p>

<p>&nbsp;</p>

<h3>Usage in articles</h3>
<p>
	Edit your article template (article.php). Add the following code where you want to see your gallery:</p>
<pre>
	&lt;?php eval($plxShow->callHook('ArtgalerieDisplay')); ?&gt;
</pre>
<p>
	When you edit your article, there is a new field "Galerie" in the sidebar. Indicate your gallery subdirectory name (<i>galerie01</i>).</p>

<p>&nbsp;</p>

<h3>Usage in a static page</h3>
<h4>Simple display of galeries</h4>
<p>In a static page add following code:</p>
<pre>
	&lt;?php
		global $plxShow;
		
		eval($plxShow->callHook('ArtgalerieDisplay', 'galerie01'));
	?&gt;
</pre>
<p>Second argument of callHook should contain name of the subdirectory of your gallery (<i>galerie01</i>).</p>
<p>&nbsp;</p>
<p>You can call several time the hook on one page, changing value of the galerie name.</p>

<p>&nbsp;</p>

<h4>Static page example:</h4>
<pre>
	&lt;p&gt;Page statique-1&lt;/p&gt;
	
	&lt;p&gt;Premiere galerie&lt;/p&gt;
	&lt;?php
		global $plxShow;
		
		eval($plxShow->callHook('ArtgalerieDisplay', 'demo1'));
	?&gt;
	
	&lt;p&gt;Deuxieme galerie&lt;/p&gt;
	&lt;?php
		eval($plxShow->callHook('ArtgalerieDisplay', 'demo2'));
	?&gt;
</pre>

<h4>Display galerie with icones</h4>

<p>In the root directory of your galery, (data/images/photos), create a directory (ex galeries) with your sub-galerie (galerie01, galerie02).
You will have something like that:
 data/images/photos/galeries
 data/images/photos/galeries/galerie01
 data/images/photos/galeries/galerie02
 ...
</p>

<p>In a static page add following code:</p>
<pre>
	&lt;?php
		global $plxShow;
		
		eval($plxShow->callHook('staticGalerieShow', 'galeries'));
	?&gt;
</pre>
<p>Second argument of callHook should contain name of the directory that contain all your gallery (<i>Photos</i>).</p>

<h3>Add a theme</h3>
<p>Theme are located in the folder "themes" inside the directory of the plugin.</p>
<p>Add a new folder which is the name of your theme. This new folder should contain 2 files:
	<pre>
	head.php
	galerie.php
	</pre>
</p>
<p><b>"head.php"</b> should contain value that will appear in the header of the HTML (CSS style, javascript). content is included during call of "ThemeEndHead".</p>
<br />
<p><b>"galerie.php"</b> will display your images. Content will be included during call of hook "ArtgalerieDisplay".<br />
You have a variable "$galerie" which is an array of all your images files:</p>
<pre>
	$galerie[]['thumb']: 	Thumbnail path.
	$galerie[]['file']: 	Full size image path.
	$galerie[]['title']:	Image description.
	$galerie[]['alt']:		Alternate text.
</pre>
<p>Have a look at the default theme files.</p>
