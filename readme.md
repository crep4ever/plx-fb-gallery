## Description

This repo is a proof of concept to convert a Facebook album gallery
into [PluXml](http://www.pluxml.org) articles.

 - fetch data from a public Facebook page through the graph api
 - process data into PluXml article structure
 - save articles into PluXml `data/articles` directory
 - keep track of processed articles

All the content of the [demo website](http://demo.dve-club.fr/plx-fb-gallery) is automatically generated from this [Facebook public page](https://www.facebook.com/Drac-Vercors-Escalade-160141077367724/photos/?tab=albums).

## Overview

### Files

- `class.fb.gallery.php`: convert Facebook json data into PluXml's article format.

Note that you need a valid access token.
For example, log in your Facebook account and visit the
[graph api explorer tool page](https://developers.facebook.com/tools/explorer) to use your token.

```php
<?php
$access_token = "00000000000000"; // your facebook access token
$page_id      = "00000000000000"; // your facebook page id
$graph_api    = "v2.7";
$gallery = new FacebookGallery($page_id, $access_token, $graph_api);
?>
```

- `class.fb.article.php`: wrapper for PluXml's article format.

This class is used by `FacebookGallery::toArticles()` method.
Building an article from facebook json data would look something like this:

```php
<?php
$article = new Article();

// header
$article->setTitle($album['name']);
$article->setDate($album['created_time']);
$article->setDescription($album['description']);
$article->setPhotoCount($album['photo_count']);

// content
$article->startGallery();
$photos = $album['photos']['data'];
foreach ($photos as $photo)
{
  $article->addImage($photo['name'], $photo['source'], '');
}
$article->endGallery();
?>
```
- `index.php`: write Facebook albums as PluXml articles.

### PluXml configuration

  - `PluXml` directory is a default setup of [PluXml 5.5](http://www.pluxml.org) with the [artGalerie plugin](http://thepoulpe.net/index.php?article3/demo-plugin-artgalerie).
  - the artGalerie plugin is configured with [minimal css rules](http://github.com/crep4ever/plx-fb-gallery/blob/master/PluXml/data/configuration/plugins/artGalerie.site.css).
