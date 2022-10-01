<?php 
/**
* @version		$Id: aplugin.html.php 2264 2019-05-01 18:42:00Z IOS $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class apluginContentView extends contentView {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/***********************/
	/* CUSTOM PAGE HEADERS */
	/***********************/
	private function sendHeaders($type='text/html') {
		if(ob_get_length() > 0) { ob_end_clean(); }
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-type:'.$type.'; charset=utf-8');
	}


	/********************************/
	/* DISPLAY AN ERROR PAGE (AJAX) */
	/********************************/
	public function errorResponse($message, $type='text/html') {
		$this->sendHeaders($type);
		if ($type == 'text/plain') {
			echo $message;
		} else {
			echo '<div class="elx5_error">'.$message."</div>\n";
		}
		exit;
	}


	/**********************************/
	/* PLUGIN IMPORTER HTML INTERFACE */
	/**********************************/
	public function interfaceHTML($id, $fn, $plugins, $iPlugin) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');
		$action = $elxis->makeAURL('content:plugin/', 'inner.php');
?>
		<div class="elx5_mpad">
		<div class="elx5_box elx5_border_blue">
			<div class="elx5_box_body">
				<div class="elx5_dataactions elx5_spad">
					<h3 class="elx5_box_title"><?php echo $eLang->get('IMPORT_ELXIS_PLUGIN'); ?></h3>
				</div>
				<div class="elx5_actionsbox elx5_dspace">
<?php 
				$form = new elxis5Form(array('idprefix' => 'plg', 'labelclass' => 'elx5_label', 'sideclass' => 'elx5_labelside'));
				$form->openForm(array('name' => 'fmplgimport', 'method' => 'post', 'action' => $action, 'id' => 'fmplgimport'));
				
				$soptions = array();
				$soptions[] = $form->makeOption(0, '- '.$eLang->get('SELECT').' -');
				if ($plugins) {
					foreach ($plugins as $plg) {
						$soptions[] = $form->makeOption($plg['id'], $plg['title'].' ('.$plg['plugin'].')');
					}
				}
				$form->addSelect('plugin', $eLang->get('PLUGIN'), $id, $soptions, array('id' => 'plugin', 'onchange' => 'loadPlugin(\''.$fn.'\');'));
				$html = '<div class="elx5_sideinput_wrap">';
				$html .= '<div class="elx5_sideinput_value_end elx5_spad"><button type="button" name="impplugin" id="impplugin" class="elx5_btn elx5_ibtn elx5_sucbtn" title="'.$eLang->get('IMPORT').'" onclick="plugImportCode(\''.$fn.'\');"><i class="fas fa-file-import"></i></button></div>';
				$html .= '<div class="elx5_sideinput_input_front elx5_spad"><label class="elx5_label" for="plugincode">'.$eLang->get('CODE').'</label>
				<div class="elx5_labelside"><input type="text" name="plugincode" value="" id="plugincode" class="elx5_text" dir="ltr" placeholder="'.$eLang->get('CODE').'" /></div></div>';
				$html .= '</div>';
				$form->addHTML($html);
				$form->closeForm();
?>
				</div>
				<div id="plug_load"><?php if ($id > 0) { $this->pluginHTML($iPlugin['row'], $iPlugin['info'], $iPlugin['plugObj'], $fn); } ?></div>
			</div>
		</div>
		</div>
		<div id="plugbase" class="elx5_invisible" dir="ltr"><?php echo $elxis->makeAURL('content:plugin/', 'inner.php'); ?></div>
		<div id="lng_wait" class="elx5_invisible" dir="ltr"><?php echo $eLang->get('PLEASE_WAIT'); ?></div>

<?php 
	}


	/**********************/
	/* LOADED PLUGIN HTML */
	/**********************/
	public function pluginHTML($row, $info, $plugObj, $fn) {
		$eLang = eFactory::getLang();

		$this->xmlDetails($info, $eLang);
		$this->pluginSyntax($plugObj, $eLang);

		$tabs = $plugObj->tabs();
		if (is_array($tabs) && (count($tabs) > 1)) {
			$this->startTabs($tabs);
			$max = count($tabs) + 1;
			for ($idx = 1; $idx < $max; $idx++) {
				$this->openTab($idx);
				$plugObj->helper($row->id, $idx, $fn);
				$this->closeTab();
			}
			$this->endTabs();
		} else {
			$plugObj->helper($row->id, 1, $fn);
		}
	}


	/**************/
	/* START TABS */
	/**************/
	private function startTabs($tabs) {
		$total = count($tabs);
		$k = 0;
		echo '<ul class="elx5_tabs" id="elx5_plug_tabs">'."\n";
		foreach ($tabs as $tab) {
			$title = (string)$tab;
			$class_str = ($k == 0) ? ' class="elx5_tab_open"' : '';
			echo "\t".'<li><a href="javascript:void(null);" id="elx5pluglink_'.$k.'"'.$class_str.' onclick="elx5PlugTabSwitch('.$k.', '.$total.');">';
			echo '<i class="elx5_tab_num" aria-hidden="false">'.($k + 1).'</i><span class="elx5_lmobhide">'.$title.'</span>';
			echo "</a></li>\n";
			$k++;
		}
		echo "</ul>\n";
		echo '<div class="elx5_tab_container">'."\n";
	}


	/************/
	/* END TABS */
	/************/
	private function endTabs() {
		echo "</div>\n";//tabs_container_class
	}


	/******************/
	/* OPEN A NEW TAB */
	/******************/
	private function openTab($idx) {
		$idx = $idx - 1;
		$class = ($idx == 0) ? 'elx5_tab_content' : 'elx5_invisible';
		echo '<div id="elx5plugtab_'.$idx.'" class="'.$class.'">'."\n";
	}


	/*************/
	/* CLOSE TAB */
	/*************/
	private function closeTab() {
		echo "</div>\n";
	}


	/*************************************/
	/* SHOW PLUGIN DETAILS FROM XML FILE */
	/*************************************/
	private function xmlDetails($info, $eLang) {
		$eDate = eFactory::getDate();

		echo '<div class="elx5_plug_head">';
		echo '<strong>'.$info['title'].'</strong> v'.$info['version'].' '.$eLang->get('BY').' '.$info['author'].'. '.$eLang->get('DATE').' '.$eDate->formatDate($info['created'], $eLang->get('DATE_FORMAT_3'));
		echo "</div>\n";
	}


	/*********************************/
	/* DISPLAY PLUGIN GENERIC SYNTAX */
	/*********************************/
	private function pluginSyntax($plugObj, $eLang) {
		$syntax = $plugObj->syntax();
		echo '<div class="elx5_plug_syntax"><div class="elx5_plug_label">'.$eLang->get('GENERIC_SYNTAX').'</div>';
		if ($syntax == '') {
			echo '<div class="elx5_plug_code">'.$eLang->get('NOT_AVAILABLE').'</div>';
		} else {
			echo '<div class="elx5_plug_code">'.htmlspecialchars($syntax).'</div>';
		}
		echo "</div>\n";
	}

}

?>