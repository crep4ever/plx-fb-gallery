<?php

require("class.fb.article.php");

function get($p_array, $p_value)
{
  return isset($p_array[$p_value]) ? $p_array[$p_value] : '';
}

/**
* @brief Convert a public Facebook gallery into PluXml articles
*
* This class fetches albums from a public Facebook page
* and converts them into PluXml articles.
*
* The article header (chapo) corresponds to the
* description of the album and the article content
* corresponds to the gallery of photos.
*
* @author Romain Goffe <romain.goffe@gmail.com>
*/
class FacebookGallery
{
  private $m_page_id;
  private $m_access_token;
  private $m_graph_version;
  private $m_ignore_list;
  private $m_cache_file;

  function __construct($p_page_id, $p_access_token, $p_graph_version)
  {
    $this->m_page_id = $p_page_id;
    $this->m_access_token = $p_access_token;
    $this->m_graph_version = $p_graph_version;
    $this->m_ignore_list = array();
    $this->m_cache_file = "registered_articles.xml";
  }

  public function setIgnoreList($p_array)
  {
    $this->m_ignore_list = $p_array;
  }

  private function getUrl($p_url)
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $p_url);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);  // Return contents only
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);  // Return results instead of outputting
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // Give up after connecting for 10 seconds
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);  // Only execute 60s at most
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  // Don't verify SSL certificate
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
  }

  private function fetch()
  {
    $fields = "name,albums{id,name,description,link,created_time,cover_photo{source},photo_count,photos{name,source,images}}";
    $content = $this->getUrl("https://graph.facebook.com/{$this->m_graph_version}/{$this->m_page_id}?fields={$fields}&access_token={$this->m_access_token}");
    $obj = json_decode($content, true, 512, JSON_BIGINT_AS_STRING);
    return $obj;
  }

  private function filter($p_albums)
  {
    $res = array();
    $size = count($p_albums);
    $count = 0;
    for ($i = 0; $i < $size; $i++)
    {
      if (in_array(get($p_albums[$i], 'name'), $this->m_ignore_list))
      {
        continue;
      }

      $res[$count++] = $p_albums[$i];
    }
    return $res;
  }

  public function readRegisteredArticles($p_filename)
  {
    if (!file_exists($p_filename))
    {
      return array();
    }

    $xml = simplexml_load_string(file_get_contents($p_filename));
    return (array) $xml->facebook_album;
  }

  public function toArticles()
  {
    // Fetch all page albums from Facebook
    $data = $this->fetch();

    // Skip some albums according to m_ignore_list
    $albums = $this->filter($data['albums']['data']);

    // Get all facebook albums already registered as pluxml articles
    $registered = $this->readRegisteredArticles($this->m_cache_file);

    $articles = array();
    $count = 0;
    foreach ($albums as $album)
    {
      if (in_array(get($album, 'id'), $registered))
      {
        continue;
      }

      $article = new Article();

      // Article header
      $article->setTitle(get($album, 'name'));
      $article->setDate(get($album, 'created_time'));
      $article->setDescription(get($album, 'description'));

      $cover = get($album, 'cover_photo');
      $article->setCoverPhoto(get($cover, 'source'), get($cover, 'name'));
      $article->setPhotoCount(get($album, 'photo_count'));

      // Article content
      $article->startGallery();
      $photos = $album['photos']['data'];
      foreach ($photos as $photo)
      {
        $title     = get($photo, 'name');
        $thumbnail = get($photo, 'source');
        $article->addImage($title, $thumbnail, $thumbnail);
      }
      $article->endGallery();

      // Link to original Facebook album
      $article->setUrl(get($album, 'link'));

      // Keep track of article so that it is not processed next time
      $article->register(get($album, 'id'), $this->m_cache_file);

      $articles[$count++] = $article;
    }

    return $articles;
  }
}
?>
