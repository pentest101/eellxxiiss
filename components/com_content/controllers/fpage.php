<?php 
/**
* @version		$Id: fpage.php 2127 2019-03-03 18:53:41Z IOS $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class fpageContentController extends contentController {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $model=null, $format='') {
		parent::__construct($view, $model, $format);
	}


	/*********************************/
	/* PREPARE TO GENERATE FRONTPAGE */
	/*********************************/
	public function frontpage() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		$layout = $this->getLayout();

		$metaKeys = array();
		$keys = explode(',', $elxis->getConfig('METAKEYS'));
		if ($keys) {
			foreach ($keys as $key) { $metaKeys[] = eUTF::trim($key); }
		}

		if (count($metaKeys) < 10) {
			$metaKeys[] = $eLang->get('HOME');
			$metaKeys[] = 'elxis';
		}

		$eDoc->setTitle($elxis->getConfig('SITENAME'));
		$eDoc->setDescription($elxis->getConfig('METADESC'));
		$eDoc->setKeywords($metaKeys);
		unset($keys, $metaKeys);

		$rsslink = $elxis->makeURL('content:rss.xml');
		$atomlink = $elxis->makeURL('content:atom.xml');
		
		$rtl = ($eLang->getinfo('DIR') == 'rtl') ? 1 : 0;
		$gridcsslink = $elxis->secureBase().'/components/com_content/css/grid.php?k='.$layout->wl.'-'.$layout->wc.'-'.$layout->wr.'-'.$rtl.'-'.$layout->reswidth;
		$eDoc->addLink($rsslink, 'application/rss+xml', 'alternate', 'title="'.$elxis->getConfig('SITENAME').' - RSS"');
		$eDoc->addLink($atomlink, 'application/rss+xml', 'alternate', 'title="'.$elxis->getConfig('SITENAME').' - ATOM"');
		$eDoc->addStyleLink($gridcsslink);
		unset($rsslink, $atomlink, $gridcsslink, $rtl);

		$this->view->showFrontpage($layout);
	}


	/**********************/
	/* GET CURRENT LAYOUT */
	/**********************/
	private function getLayout() {
		$layout = new stdClass;
		$layout->wl = 20;
		$layout->wc = 60;
		$layout->wr = 20;
		$layout->type = 'positions';
		$layout->reswidth = 650;
		$layout->items = array();
		for ($i=1; $i<18; $i++) {
			$property = 'c'.$i;
			$property2 = 'resbox'.$i;
			$layout->$property = array();
			$layout->$property2 = 1;
		}
		$layout->rowsorder = array('2', '4x5', '6x7', '8x9', '10', '11x12x13', '14', '15x16', '17');

		$rows = $this->model->getFrontpage();
		if ($rows) {
			foreach ($rows as $row) {
				$pname = $row['pname'];
				switch ($pname) {
					case 'wl': case 'wc': case 'wr': case 'reswidth': case 'resbox1': case 'resbox2': case 'resbox3': case 'resbox4': case 'resbox5': case 'resbox6': case 'resbox7': case 'resbox8': 
					case 'resbox9': case 'resbox10': case 'resbox11': case 'resbox12': case 'resbox13': case 'resbox14': case 'resbox15': case 'resbox16': case 'resbox17':  
						$layout->$pname = (int)$row['pval'];
					break;
					case 'type':
						$layout->type = trim($row['pval']);
						if ($layout->type != 'modules') { $layout->type = 'positions'; }
					break;
					case 'rowsorder':
						if (trim($row['pval']) != '') {
							$layout->rowsorder = explode(',', $row['pval']);
						}
					break;
					default:
						$pval = trim($row['pval']);
						if ($pval != '') {
							$layout->$pname = explode(',', $pval);
						}
					break;
				}
			}
		}

		return $layout;
	}

}

?>