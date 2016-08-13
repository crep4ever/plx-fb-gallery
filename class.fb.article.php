<?php

require("core/lib/class.plx.utils.php");

/**
 * @brief Wrapper for PluXml article structure
 *
 * PluXml's articles are represented as an array with fields
 * such as "title", "author" etc.
 *
 * This class builds an empty PluXml article by default
 * and provides helper methods that will convert Facebook json data
 * into appropriated fields.
 *
 * @author Romain Goffe <romain.goffe@gmail.com>
 */
class Article
{
  private $m_fields;

  function __construct()
  {
    // Init PluXml article fields
    $this->m_fields["title"] = '';
    $this->m_fields["author"] = '001';
    $this->m_fields["publish"] = true;
    $this->m_fields["allow_com"] = false;
    $this->m_fields["artId"] = '';
    $this->m_fields["catId"] = '';
    $this->m_fields["url"] = '';
    $this->m_fields["galerie"] = '';
    $this->m_fields["thumbnail"] = '';
    $this->m_fields["thumbnail_alt"] = '';
    $this->m_fields["thumbnail_title"] = '';
    $this->m_fields["template"] = '';
    $this->m_fields["chapo"] = '';
    $this->m_fields["content"] = '';
    $this->m_fields["tags"] = '';
    $this->m_fields["meta_description"] = '';
    $this->m_fields["meta_keywords"] = '';
    $this->m_fields["title_htmltag"] = '';
    $this->m_fields["date_creation_year"] = '';
    $this->m_fields["date_creation_month"] = '';
    $this->m_fields["date_creation_day"] = '';
    $this->m_fields["date_creation_time"] = '';
    $this->m_fields["date_update_year"] = '';
    $this->m_fields["date_update_month"] = '';
    $this->m_fields["date_update_day"] = '';
    $this->m_fields["date_update_time"] = '';
    $this->m_fields["date_update_old"] = '';
    $this->m_fields["date_publication_year"] = '';
    $this->m_fields["date_publication_month"] = '';
    $this->m_fields["date_publication_day"] = '';
    $this->m_fields["date_publication_time"] = '';
  }

  public function content()
  {
    return $this->m_fields;
  }

  public function setTitle($p_value)
  {
    $this->m_fields["title"] = $p_value;
  }

  public function setDate($p_value)
  {
    $date = DateTime::createFromFormat(DateTime::ISO8601, $p_value);

    if ($date instanceof DateTime)
    {
      $year  = $date->format('Y');
      $month = $date->format('m');
      $day   = $date->format('d');
      $time  = $date->format('H:i');

      $this->m_fields["date_creation_year"]  = $year;
      $this->m_fields["date_creation_month"] = $month;
      $this->m_fields["date_creation_day"]   = $day;
      $this->m_fields["date_creation_time"]  = $time;

      $this->m_fields["date_publication_year"]  = $year;
      $this->m_fields["date_publication_month"] = $month;
      $this->m_fields["date_publication_day"]   = $day;
      $this->m_fields["date_publication_time"]  = $time;

      $this->m_fields["date_update_year"]  = $year;
      $this->m_fields["date_update_month"] = $month;
      $this->m_fields["date_update_day"]   = $day;
      $this->m_fields["date_update_time"]  = $time;
    }
  }

  public function setCoverPhoto($p_value, $p_title)
  {
    $this->m_fields["thumbnail"]       = $p_value;
    $this->m_fields["thumbnail_alt"]   = $p_title;
    $this->m_fields["thumbnail_title"] = $p_title;
  }

  public function setPhotoCount($p_value)
  {
    $this->m_fields["chapo"] .= "<p>$p_value photos</p>";
  }

  public function setDescription($p_value)
  {
    $this->m_fields["chapo"] .= $p_value;
  }

  public function setUrl($p_value)
  {
    $this->m_fields["content"] .= "<p><a href='$p_value'>Voir sur Facebook</a></p>";
  }

  public function startGallery()
  {
    $this->m_fields["content"] .= "<section class='album'>\n";
    $this->m_fields["content"] .= "\t<div class='gallery-thumbnails'>\n";
  }

  public function endGallery()
  {
    $this->m_fields["content"] .= "\t</div>\n";
    $this->m_fields["content"] .= "</section>\n";
    $this->m_fields["content"] .= "<br style='clear:both'/>\n";
  }

  public function addImage($p_title, $p_thumbnail, $p_target)
  {
    $this->m_fields["content"] .= "\t\t<div class='gallery-thumbnail'>\n";
    $this->m_fields["content"] .= "\t\t\t<div class='gallery-thumbnail-img'>\n";
    $this->m_fields["content"] .= "\t\t\t\t<a href='$p_target'>\n";
    $this->m_fields["content"] .= "\t\t\t\t\t<img class='photo-thumb' src='$p_thumbnail' alt='$p_title' />\n";
    $this->m_fields["content"] .= "\t\t\t\t</a>\n";
    $this->m_fields["content"] .= "\t\t\t</div>\n";
    $this->m_fields["content"] .= "\t\t</div>\n";
  }

  public function register($p_id, $p_filename)
  {
    if (!file_exists($p_filename))
    {
      $xml  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
      $xml .= "<document>\n";
      $xml .= "\t" . '<facebook_album>' . $p_id . '</facebook_album>' . "\n";
      $xml .= "</document>";
      plxUtils::write($xml, $p_filename);
    }
    else
    {
      $xml = simplexml_load_string(file_get_contents($p_filename));
      $xml->addChild("facebook_album", $p_id);
      plxUtils::write($xml->saveXML(), $p_filename);
    }
  }
}

?>
