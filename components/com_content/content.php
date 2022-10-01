<?php 
/**
* @version		$Id: content.php 2027 2018-12-21 12:08:41Z IOS $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


elxisLoader::loadFile('components/com_content/controllers/base.php');
elxisLoader::loadFile('components/com_content/views/base.html.php');


class contentRouter extends elxisRouter {

	private $controller = 'fpage';
	private $task = 'frontpage';
	private $format = 'html';


	/**********************************************/
	/* ROUTE THE REQUEST TO THE PROPER CONTROLLER */
	/**********************************************/
	public function route() {
		if (defined('ELXIS_ADMIN')) {
			$this->makeAdminRoute();
		} else {
			$this->makeRoute();
		}
		require(ELXIS_PATH.'/components/com_'.$this->component.'/controllers/'.$this->controller.'.php');
		if (($this->format != 'html') && file_exists(ELXIS_PATH.'/components/com_'.$this->component.'/views/'.$this->controller.'.'.$this->format.'.php')) {
			require(ELXIS_PATH.'/components/com_'.$this->component.'/views/'.$this->controller.'.'.$this->format.'.php');
		} else {
			require(ELXIS_PATH.'/components/com_'.$this->component.'/views/'.$this->controller.'.html.php');
		}
		if ($this->controller == 'aplugin') {
			require(ELXIS_PATH.'/components/com_'.$this->component.'/models/plugins.model.php');
		} else {
			require(ELXIS_PATH.'/components/com_'.$this->component.'/models/'.$this->component.'.model.php');
		}
		$class = $this->controller.ucfirst($this->component).'Controller';
		$viewclass = $this->controller.ucfirst($this->component).'View';
		$task = $this->task;
		if (!class_exists($class, false)) {
			exitPage::make('error', 'CCON-0001', 'Class '.$class.' was not found in file '.$this->controller.'.php');
		}
		if (!method_exists($class, $task)) {
			exitPage::make('error', 'CCON-0002', 'Task '.$task.' was not found in class '.$class.' in file '.$this->controller.'.php');
		}
		$view = new $viewclass();
		$model = new contentModel();
		$controller = new $class($view, $model, $this->format);
		unset($view, $model);
		$controller->$task();
	}


	/**************/
	/* MAKE ROUTE */
	/**************/
	private function makeRoute() {
		$n = count($this->segments);
		if ($n == 0) {
			$this->controller = 'fpage';
			$this->task = 'frontpage';
			return;
		}

		$eURI = eFactory::getURI();
		$fp = ($eURI->getUriString() == eFactory::getElxis()->getConfig('DEFAULT_ROUTE')) ? true : false;

		if ($eURI->isDir()) {
			if ($this->segments[0] == 'archive') { //archive
				$this->controller = 'generic';
				$this->task = 'archive';
			} else { //category
				$this->controller = 'category';
				$this->task = 'viewcategory';
			}
			return;
		} else if ($fp && preg_match('#\/$#', $eURI->getUriString())) { //a category as frontpage
			$this->controller = 'category';
			$this->task = 'viewcategory';
			return;
		} else {
			if ($this->segments[0] == 'minify') {
				if (isset($this->segments[1])) {
					$this->controller = 'generic';
					$this->task = 'minify';
					return;
				}
			}
			if ($this->segments[0] == 'archive') {
				if (isset($this->segments[1])) {
					$this->controller = 'generic';
					$this->task = 'archive';
					return;
				}
			}
			if ($this->segments[0] == 'feeds.html') {
				$this->controller = 'generic';
				$this->task = 'feeds';
				return;
			}
			if ($this->segments[0] == 'rss.xml') {
				$this->controller = 'generic';
				$this->task = 'rssfeed';
				$this->format = 'xml';
				return;
			}
			if ($this->segments[0] == 'atom.xml') {
				$this->controller = 'generic';
				$this->task = 'atomfeed';
				$this->format = 'xml';
				return;
			}
			if ($this->segments[0] == 'contenttools') {//AJAX
				$this->controller = 'generic';
				$this->task = 'contenttools';
				return;
			}
			if ($this->segments[0] == 'ajax') {//ajax
				$this->controller = 'generic';
				$this->task = 'genericajax';
				return;
			}
			if ($this->segments[0] == 'captchagen') {//ajax
				$this->controller = 'generic';
				$this->task = 'captchagenerator';
				return;
			}
			if ($this->segments[0] == 'tags.html') {
				$this->controller = 'generic';
				$this->task = 'tags';
				return;
			}
			if ($this->segments[0] == 'send-to-friend.html') {
				$this->controller = 'generic';
				$this->task = 'sendtofriend';
				return;
			}
			if ($this->segments[0] == 'modpreview') {
				$this->controller = 'generic';
				$this->task = 'modulepreview';
				return;
			}

			$last = $n - 1;
			if (($n > 1) && (preg_match('/(\.xml)$/i', $this->segments[$last]))) { //category rss/atom feed
				if (!in_array($this->segments[$last], array('rss.xml', 'atom.xml'))) {
					exitPage::make('404', 'CCON-0003');
				}
				$this->controller = 'category';
				$this->task = 'viewcategory';
				$this->format = 'xml';
				return;
			}

			if (!preg_match('/(\.html)$/i', $this->segments[$last])) { //format=mobile?
				exitPage::make('404', 'CCON-0004');
			}
			$this->controller = 'article';
			$this->task = 'viewarticle';
		}
	}


	/********************/
	/* MAKE ADMIN ROUTE */
	/********************/
	private function makeAdminRoute() {
		$this->task = '';

		$c = count($this->segments);
		if ($c == 0) { //alias of content/categories/
			$this->controller = 'acategory';
			$this->task = 'listcategories';
			return;
		}

		if ($this->segments[0] == 'categories') {
			$this->controller = 'acategory';
			if (!isset($this->segments[1])) { $this->task = 'listcategories'; return; }
			switch ($this->segments[1]) {
				case 'move': $this->task = 'movecategory'; break;
				case 'togglestatus': $this->task = 'togglecategory'; break;
				case 'delete': $this->task = 'deletecategory'; break;
				case 'add.html': $this->task = 'addcategory'; break;
				case 'edit.html': $this->task = 'editcategory'; break;
				case 'suggest': $this->task = 'suggestcategory'; break;
				case 'validate': $this->task = 'validatecategory'; break;
				case 'save.html': $this->task = 'savecategory'; break;
				default: break;
			}
			if ($this->task != '') { return; }
			exitPage::make('404', 'CCON-0005');
		}

		if ($this->segments[0] == 'articles') {
			$this->controller = 'aarticle';
			if (!isset($this->segments[1])) { $this->task = 'listarticles'; return; }
			switch ($this->segments[1]) {
				case 'setordering': $this->task = 'setordering'; break;
				case 'togglestatus': $this->task = 'togglearticle'; break;
				case 'toggleimpstatus': $this->task = 'toggleimparticle'; break;
				case 'delete': $this->task = 'deletearticles'; break;
				case 'copy': $this->task = 'copyarticles'; break;
				case 'move': $this->task = 'movearticles'; break;
				case 'publishcomment': $this->task = 'publishcomment'; break;
				case 'deletecomment': $this->task = 'deletecomment'; break;
				case 'suggest': $this->task = 'suggestarticle'; break;
				case 'validate': $this->task = 'validatearticle'; break;
				case 'share.html': $this->task = 'sharearticle'; break;
				case 'add.html': $this->task = 'addarticle'; break;
				case 'edit.html': $this->task = 'editarticle'; break;
				case 'save.html': $this->task = 'savearticle'; break;
				default: break;
			}
			if ($this->task != '') { return; }
			exitPage::make('404', 'CCON-0006');
		}

		if ($this->segments[0] == 'fpage') {
			$this->controller = 'afpage';
			if (!isset($this->segments[1])) { $this->task = 'design'; return; }
			switch ($this->segments[1]) {
				case 'save': $this->task = 'savelayout'; break;
				default: break;
			}
			if ($this->task != '') { return; }
			exitPage::make('404', 'CCON-0007');
		}

		if ($c == 1) {
			if ($this->segments[0] == 'plugin') {
				$this->controller = 'aplugin';
				$this->task = 'import';
				return;
			}
		}

		exitPage::make('404', 'CCON-0008');
	}

}

?>