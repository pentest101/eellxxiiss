<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Content Plugins / YouTube
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class youtubePlugin implements contentPlugin {


	/********************/
	/* MAGIC CONTRUCTOR */
	/********************/
	public function __construct() {
	}


	/***********************************/
	/* EXECUTE PLUGIN ON THE GIVEN ROW */
	/***********************************/
	public function process(&$row, $published, $params) {
		$regex = "#{youtube\s*(.*?)}(.*?){/youtube}#s";
		$regexno = "#{youtube\s*.*?}.*?{/youtube}#s";
		if (!$published) {
    		$row->text = preg_replace($regexno, '', $row->text);
    		return true;
		}

		$matches = array();
		preg_match_all($regex, $row->text, $matches, PREG_PATTERN_ORDER);
		if (!$matches) { return true; }

		$defwidth = (int)$params->get('defwidth', 640);
		if ($defwidth < 200) { $defwidth = 640; }
		$defheight = intval((9 * $defwidth) / 16);

		$ePlugin = eFactory::getPlugin();
		foreach ($matches[0] as $i => $match) {
			$videoid = trim($matches[2][$i]);
			if ($videoid == '') {
				$row->text = preg_replace("#".$match."#", '', $row->text);
				continue;
			}

			$attributes = $ePlugin->parseAttributes($matches[1][$i]);
			$html = $this->makeYoutubeHTML($videoid, $attributes, $defwidth, $defheight);
			$row->text = preg_replace("#".$match."#", $html, $row->text);
		}

		return true;
	}


	/************************/
	/* GENERIC SYNTAX STYLE */
	/************************/
	public function syntax() {
		return '{youtube width="640" height="360"}YouTube video ID{/youtube}';
	}


	/***********************/
	/* LIST OF HELPER TABS */
	/***********************/
	public function tabs() {
		$eLang = eFactory::getLang();
		return array($eLang->get('VIDEOID') , $eLang->get('HELP'));
	}


	/*****************/
	/* PLUGIN HELPER */
	/*****************/
	public function helper($pluginid, $tabidx, $fn) {
		switch ($tabidx) {
			case 1: $this->getVideoId(); break;
			case 2: $this->Help(); break;
			default: break;
		}
	}


	/***************************************************/
	/* RETURN REQUIRED CSS AND JS FILES FOR THE HELPER */
	/***************************************************/
	public function head() {
		$elxis = eFactory::getElxis();

		$response = array(
			'js' => array($elxis->secureBase().'/components/com_content/plugins/youtube/includes/youtube.js'),
			'css' => array()
		);

		return $response;
	}


	/*******************************/
	/* PLUGIN SPECIAL TASK HANDLER */
	/*******************************/
	public function handler($pluginid, $fn) {
		$elxis = eFactory::getElxis();
		$url = $elxis->makeAURL('content:plugin/', 'inner.php').'?id='.$pluginid.'&fn='.$fn;
		$elxis->redirect($url);
	}


	/********************************/
	/* MAKE YOUTUBE VIDEO HTML CODE */
	/********************************/
	private function makeYoutubeHTML($videoid, $attributes, $defwidth, $defheight) {
		$width = $defwidth;
		$height = $defheight;
		if ($attributes) {
			/* we dont use attribute height any more as video aspect ratio must always be 16:9 in order for responsive design to look nice */
			if (isset($attributes['width'])) {
				$w = (int)$attributes['width'];
				if ($w > 100) {
					$width = $w;
					$height = intval((9 * $w) / 16);
				}
			}
		}

		$out = '<div class="elx_ytvideo">'."\n";
		$out .= '<iframe width="'.$width.'" height="'.$height.'" src="//www.youtube.com/embed/'.$videoid.'?rel=0" frameborder="0" allowfullscreen="true"></iframe>'."\n";
		$out .= "</div>\n";

		return $out;
	}


	/******************/
	/* GET A VIDEO ID */
	/******************/
	private function getVideoId() {
		$eLang = eFactory::getLang();

		echo '<div class="elx5_sideinput_wrap">';
		echo '<div class="elx5_sideinput_value_end elx5_spad">';
		echo '<a href="javascript:void(null);" class="elx5_btn elx5_ibtn" title="'.$eLang->get('ADD').'" onclick="addYTVideoID();"><i class="fas fa-location-arrow"></i></a>';
		echo '</div>';
		echo '<div class="elx5_sideinput_input_front elx5_spad">';
		echo '<label class="elx5_label" for="youtube_videoid">'.$eLang->get('VIDEOID').'</label>';
		echo '<div class="elx5_labelside">';
		echo '<input type="text" name="youtube_videoid" value="" id="youtube_videoid" class="elx5_text" dir="ltr" placeholder="'.$eLang->get('VIDEOID').'" />';
		echo "</div></div></div>\n";
	}


	/***************/
	/* PLUGIN HELP */
	/***************/
	private function Help() {
?>
		<p><strong>Youtube Video</strong> plugin allows you to place a Youtube Video inside article . </p>
		<p><strong><em>How will i get a Youtube Video id?</em></strong><br />
		Each Video on Youtube have a specific URL structure. For example: An elxis video regarding the Elxis Download Center is placed on the url : 
		<em>https://www.youtube.com/watch?v=EZHR569uoew</em>. The video id for this URL is: <strong>EZHR569uoew</strong></p>
<?php 
	}

}

?>