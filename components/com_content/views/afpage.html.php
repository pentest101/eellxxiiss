<?php 
/**
* @version		$Id: afpage.html.php 2127 2019-03-03 18:53:41Z IOS $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class afpageContentView extends contentView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/***************************/
	/* HTML FRONTPAGE DESIGNER */
	/***************************/
	public function design($layout, $items, $type, $ordering) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$deflang = $elxis->getConfig('LANG');

		$accordion = $elxis->obj('accordion', 'helper', true);
		$accordion->setCollapsible(true);
?>

		<h2><?php echo $eLang->get('FRONTPAGE_DESIGNER'); ?></h2>
		<p class="fpsmallreswarn">Frontpage designer requires a larger screen resolution.</p>

		<div class="fpwrap">
			<div class="fpboxeswrap">
<?php 
			$accordion->open(true);

			$accordion->openItem($eLang->get('OPTIONS'), true);

			echo '<div class="elx5_formrow">'."\n";
			echo '<label class="fp5_label" for="fptype">'.$eLang->get('TYPE').'</label><select name="fptype" id="fptype" class="elx5_select elx5_inselect" onchange="fpSwitchType()">'."\n";
			$sel = ($type == 'positions') ? ' selected="selected"' : '';
			echo '<option value="positions"'.$sel.'>'.$eLang->get('POSITIONS')."</option>\n";
			$sel = ($type == 'modules') ? ' selected="selected"' : '';
			echo '<option value="modules"'.$sel.'>'.$eLang->get('MODULES')."</option>\n";
			echo "</select>\n</div>\n";
			echo '<a href="javascript:void(null);" onclick="elx5Toggle(\'respoopthelp\');" class="fp5_biglink"><i class="fas fa-info-circle"></i> '.$eLang->get('RESPONSIVE_OPTIONS').'</a>'."\n";
			echo '<div class="elx5_invisible" id="respoopthelp"><div class="elx5_tip elx5_dspace">'.$eLang->get('RESPO_GRID_HELP').'</div></div>'."\n";

			$widths = range(400, 950, 50);
			echo '<div class="elx5_formrow"><label class="fp5_label" for="fpreswidth">'.$eLang->get('WIDTH').'</label>';
			echo '<select name="fpreswidth" id="fpreswidth" class="elx5_select elx5_inselect">'."\n";
			foreach ($widths as $width) {
				$sel = ($layout->reswidth == $width) ? ' selected="selected"' : '';
				echo '<option value="'.$width.'"'.$sel.'>'.$width." px</option>\n";
			}
			echo "</select>\n</div>\n";

			for($i = 1; $i < 18; $i++) {
				$idx = 'resbox'.$i;

				echo '<div class="elx5_formrow">';
				if ($layout->$idx == 1) {
					echo '<label id="fprboxlab'.$i.'" class="fp5_label fpshow">'.$eLang->get('BOX').' '.$i.'</label>';
					echo '<select name="fpresbox'.$i.'" id="fpresbox'.$i.'" class="elx5_select elx5_inselect" onchange="fpResboxSwitch('.$i.');">'."\n";
					echo '<option value="0">'.$eLang->get('HIDE')."</option>\n";
					echo '<option value="1" selected="selected">'.$eLang->get('STRETCH')."</option>\n";
				} else {
					echo '<label id="fprboxlab'.$i.'" class="fp5_label fphide">'.$eLang->get('BOX').' '.$i.'</label>';
					echo '<select name="fpresbox'.$i.'" id="fpresbox'.$i.'" class="elx5_select elx5_inselect" onchange="fpResboxSwitch('.$i.');">'."\n";
					echo '<option value="0" selected="selected">'.$eLang->get('HIDE')."</option>\n";
					echo '<option value="1">'.$eLang->get('STRETCH')."</option>\n";
				}
				echo "</select>\n</div>\n";
			}
			$accordion->closeItem();

			$title = ($type == 'modules') ? $eLang->get('MODULES') : $eLang->get('POSITIONS');
			$accordion->openItem($title, true);
?>
			<ul class="fpboxes" id="fpboxes">
<?php 
			if ($items) {
				foreach ($items as $item) {
					if ($type == 'positions') {
						if ($item->position == 'hidden') { continue; }
						if ($item->position == 'tools') { continue; }
						if ($item->position == 'menu') { continue; }
						if (strpos($item->position, 'category') === 0) { continue; }
						if (in_array($item->position, $layout->items)) { continue; }
						echo '<li class="laybox" id="'.$item->position.'">'.$item->position.' ('.$item->modules.")</li>\n";
					} else {
						$mod_withid = $item->module.':'.$item->id;
						if (in_array($mod_withid, $layout->items)) { continue; }
						echo '<li class="laybox" id="'.$mod_withid.'"><a href="javascript:void(null);" title="'.$eLang->get('VIEW').'" onclick="fpModPreview('.$item->id.')">'.$item->title."</a></li>\n";
					}
				}
			}
?>
			</ul>
<?php 
			$accordion->closeItem();
			$accordion->openItem($eLang->get('DIMENSIONS'), false);

			$range = range(0, 100, 5);

			echo '<div class="elx5_formrow">'."\n";
			echo '<label class="fp5_label" for="fpwleft">'.$eLang->get('LEFT').'</label>'."\n";
			echo '<select name="fpwleft" id="fpwleft" class="elx5_select elx5_inselect" onchange="fpcalculateCols(1);">'."\n";
			foreach ($range as $w) {
				$sel = ($layout->wl == $w) ? ' selected="selected"' : '';
				echo '<option value="'.$w.'"'.$sel.'>'.$w.'%</option>'."\n";
			}
			echo "</select>\n";
			echo "</div>\n";

			echo '<div class="elx5_formrow">'."\n";
			echo '<label class="fp5_label" for="fpwcenter">'.$eLang->get('CENTER').'</label>'."\n";
			echo '<select name="fpwcenter" id="fpwcenter" class="elx5_select elx5_inselect" onchange="fpcalculateCols(2);">'."\n";
			foreach ($range as $w) {
				$sel = ($layout->wc == $w) ? ' selected="selected"' : '';
				echo '<option value="'.$w.'"'.$sel.'>'.$w.'%</option>'."\n";
			}
			echo "</select>\n";
			echo "</div>\n";

			echo '<div class="elx5_formrow">'."\n";
			echo '<label class="fp5_label" for="fpwright">'.$eLang->get('RIGHT').'</label>'."\n";
			echo '<select name="fpwright" id="fpwright" class="elx5_select elx5_inselect" onchange="fpcalculateCols(2);">'."\n";
			foreach ($range as $w) {
				$sel = ($layout->wr == $w) ? ' selected="selected"' : '';
				echo '<option value="'.$w.'"'.$sel.'>'.$w.'%</option>'."\n";
			}
			echo "</select>\n";
			echo "</div>\n";

?>
			<a href="javascript:void(null);" onclick="applyWidth();" class="elx5_btn"><?php echo $eLang->get('APPLY'); ?></a>
<?php 
			$accordion->closeItem();
			$accordion->openItem($eLang->get('FINISH'), true);
			echo '<a href="javascript:void(null);" onclick="saveLayout();" class="elx5_btn elx5_sucbtn">'.$eLang->get('SAVE')."</a>\n";
			echo '<div id="fp_message" class="fpmessage" style="display:none;"></div>'."\n";
			$accordion->closeItem();
			$accordion->close();
?>
			</div>
			<div class="fplayout" id="fplayout">
				<div id="fpleftcol">
					<div class="layrow_top">
						<div class="layrow_labels">1</div>
					</div>
					<ul class="lay100" id="lay1">
						<?php $this->populateCell($layout->c1, $items, $type); ?>
					</ul>
				</div>
				<div id="fpmidcol">
				<div class="laymidflex">
					<div class="layrow layorder<?php echo $this->fpRowOrder($ordering, '2'); ?>" id="layrow2" data-order="<?php echo $ordering['2']; ?>">
						<div class="layrow_top">
							<div class="layrow_down"><a href="javascript:void(null);" onclick="fpMoveRow('2', 'down');" title="Down">&#xf078;</a></div>
							<div class="layrow_labup">
								<div class="layrow_up"><a href="javascript:void(null);" onclick="fpMoveRow('2', 'up');" title="Up">&#xf077;</a></div>
								<div class="layrow_labels">2</div>
							</div>
						</div>
						<ul class="lay100" id="lay2">
							<?php $this->populateCell($layout->c2, $items, $type); ?>
						</ul>
					</div>
					<div class="layrow layorder<?php echo $this->fpRowOrder($ordering, '4x5'); ?>" id="layrow4x5" data-order="<?php echo $ordering['4x5']; ?>">
						<div class="layrow_top">
							<div class="layrow_down"><a href="javascript:void(null);" onclick="fpMoveRow('4x5', 'down');" title="Down">&#xf078;</a></div>
							<div class="layrow_labup">
								<div class="layrow_up"><a href="javascript:void(null);" onclick="fpMoveRow('4x5', 'up');" title="Up">&#xf077;</a></div>
								<div class="layrow_labels"><div class="layrow_label2">4</div><div class="layrow_label2">5</div></div>
							</div>
						</div>
						<ul class="lay240" id="lay4">
							<?php $this->populateCell($layout->c4, $items, $type); ?>
						</ul>
						<ul class="lay240" id="lay5">
							<?php $this->populateCell($layout->c5, $items, $type); ?>
						</ul>
						<div class="clear"></div>
					</div>
					<div class="layrow layorder<?php echo $this->fpRowOrder($ordering, '6x7'); ?>" id="layrow6x7" data-order="<?php echo $ordering['6x7']; ?>">
						<div class="layrow_top">
							<div class="layrow_down"><a href="javascript:void(null);" onclick="fpMoveRow('6x7', 'down');" title="Down">&#xf078;</a></div>
							<div class="layrow_labup">
								<div class="layrow_up"><a href="javascript:void(null);" onclick="fpMoveRow('6x7', 'up');" title="Up">&#xf077;</a></div>
								<div class="layrow_labels"><div class="layrow_label3">6</div><div class="layrow_label1">7</div></div>
							</div>
						</div>
						<ul class="lay320" id="lay6">
							<?php $this->populateCell($layout->c6, $items, $type); ?>
						</ul>
						<ul class="lay160" id="lay7">
							<?php $this->populateCell($layout->c7, $items, $type); ?>
						</ul>
						<div class="clear"></div>
					</div>
					<div class="layrow layorder<?php echo $this->fpRowOrder($ordering, '8x9'); ?>" id="layrow8x9" data-order="<?php echo $ordering['8x9']; ?>">
						<div class="layrow_top">
							<div class="layrow_down"><a href="javascript:void(null);" onclick="fpMoveRow('8x9', 'down');" title="Down">&#xf078;</a></div>
							<div class="layrow_labup">
								<div class="layrow_up"><a href="javascript:void(null);" onclick="fpMoveRow('8x9', 'up');" title="Up">&#xf077;</a></div>
								<div class="layrow_labels"><div class="layrow_label1">8</div><div class="layrow_label3">9</div></div>
							</div>
						</div>
						<ul class="lay160" id="lay8">
							<?php $this->populateCell($layout->c8, $items, $type); ?>
						</ul>
						<ul class="lay320" id="lay9">
							<?php $this->populateCell($layout->c9, $items, $type); ?>
						</ul>
						<div class="clear"></div>
					</div>
					<div class="layrow layorder<?php echo $this->fpRowOrder($ordering, '10'); ?>" id="layrow10" data-order="<?php echo $ordering['10']; ?>">
						<div class="layrow_top">
							<div class="layrow_down"><a href="javascript:void(null);" onclick="fpMoveRow('10', 'down');" title="Down">&#xf078;</a></div>
							<div class="layrow_labup">
								<div class="layrow_up"><a href="javascript:void(null);" onclick="fpMoveRow('10', 'up');" title="Up">&#xf077;</a></div>
								<div class="layrow_labels">10</div>
							</div>
						</div>
						<ul class="lay100" id="lay10">
							<?php $this->populateCell($layout->c10, $items, $type); ?>
						</ul>
					</div>
					<div class="layrow layorder<?php echo $this->fpRowOrder($ordering, '11x12x13'); ?>" id="layrow11x12x13" data-order="<?php echo $ordering['11x12x13']; ?>">
						<div class="layrow_top">
							<div class="layrow_down"><a href="javascript:void(null);" onclick="fpMoveRow('11x12x13', 'down');" title="Down">&#xf078;</a></div>
							<div class="layrow_labup">
								<div class="layrow_up"><a href="javascript:void(null);" onclick="fpMoveRow('11x12x13', 'up');" title="Up">&#xf077;</a></div>
								<div class="layrow_labels"><div class="layrow_label1m">11</div><div class="layrow_label1">12</div><div class="layrow_label1m">13</div></div>
							</div>
						</div>
						<ul class="lay160" id="lay11">
							<?php $this->populateCell($layout->c11, $items, $type); ?>
						</ul>
						<ul class="lay160" id="lay12">
							<?php $this->populateCell($layout->c12, $items, $type); ?>
						</ul>
						<ul class="lay160" id="lay13">
							<?php $this->populateCell($layout->c13, $items, $type); ?>
						</ul>
						<div class="clear"></div>
					</div>
					<div class="layrow layorder<?php echo $this->fpRowOrder($ordering, '14'); ?>" id="layrow14" data-order="<?php echo $ordering['14']; ?>">
						<div class="layrow_top">
							<div class="layrow_down"><a href="javascript:void(null);" onclick="fpMoveRow('14', 'down');" title="Down">&#xf078;</a></div>
							<div class="layrow_labup">
								<div class="layrow_up"><a href="javascript:void(null);" onclick="fpMoveRow('14', 'up');" title="Up">&#xf077;</a></div>
								<div class="layrow_labels">14</div>
							</div>
						</div>
						<ul class="lay100" id="lay14">
							<?php $this->populateCell($layout->c14, $items, $type); ?>
						</ul>
					</div>
					<div class="layrow layorder<?php echo $this->fpRowOrder($ordering, '15x16'); ?>" id="layrow15x16" data-order="<?php echo $ordering['15x16']; ?>">
						<div class="layrow_top">
							<div class="layrow_down"><a href="javascript:void(null);" onclick="fpMoveRow('15x16', 'down');" title="Down">&#xf078;</a></div>
							<div class="layrow_labup">
								<div class="layrow_up"><a href="javascript:void(null);" onclick="fpMoveRow('15x16', 'up');" title="Up">&#xf077;</a></div>
								<div class="layrow_labels"><div class="layrow_label2">15</div><div class="layrow_label2">16</div></div>
							</div>
						</div>
						<ul class="lay240" id="lay15">
							<?php $this->populateCell($layout->c15, $items, $type); ?>
						</ul>
						<ul class="lay240" id="lay16">
							<?php $this->populateCell($layout->c16, $items, $type); ?>
						</ul>
						<div class="clear"></div>
					</div>
					<div class="layrow layorder<?php echo $this->fpRowOrder($ordering, '17'); ?>" id="layrow17" data-order="<?php echo $ordering['17']; ?>">
						<div class="layrow_top">
							<div class="layrow_down"><a href="javascript:void(null);" onclick="fpMoveRow('17', 'down');" title="Down">&#xf078;</a></div>
							<div class="layrow_labup">
								<div class="layrow_up"><a href="javascript:void(null);" onclick="fpMoveRow('17', 'up');" title="Up">&#xf077;</a></div>
								<div class="layrow_labels">17</div>
							</div>
						</div>
						<ul class="lay100" id="lay17">
							<?php $this->populateCell($layout->c17, $items, $type); ?>
						</ul>
					</div>
				</div>
				</div>
				<div id="fprightcol">
					<div class="layrow_top">
						<div class="layrow_labels">3</div>
					</div>
					<ul class="lay100" id="lay3">
						<?php $this->populateCell($layout->c3, $items, $type); ?>
					</ul>
				</div>
				<div class="clear"></div>
			</div>
			<div class="clear"></div>
		</div>
		<div class="elx5_invisible" id="fp_lng_w100"><?php echo $eLang->get('WIDTHS_SUM_100'); ?></div>
		<div class="elx5_invisible" id="fp_lng_wait"><?php echo $eLang->get('PLEASE_WAIT'); ?></div>
		<div class="elx5_invisible" id="fp_saveurl" dir="ltr"><?php echo $elxis->makeAURL('content:fpage/save', 'inner.php'); ?></div>
		<div class="elx5_invisible" id="fp_baseurl" dir="ltr"><?php echo $elxis->makeAURL('content:fpage/'); ?></div>
		<div class="elx5_invisible" id="fp_modpvurl" dir="ltr"><?php echo $elxis->makeURL($deflang.':content:modpreview', 'inner.php'); ?></div>
		<div class="elx5_invisible" id="fp_saves" dir="ltr">
<?php 
		for ($i=1; $i < 18; $i++) {
			$prop = 'c'.$i;
			echo 'lay'.$i.'='.implode(',', $layout->$prop).'!';
		}
?>
		</div>
		<div class="elx5_invisible" id="fp_ordering" dir="ltr"><?php echo implode(',', array_keys($ordering)); ?></div>

<?php 
	}


	/***************************/
	/* GET FRONTPAGE ROW ORDER */
	/***************************/
	private function fpRowOrder($ordering, $key) {
		$order = 999;
		if (!$ordering) { return $order; }
		foreach ($ordering as $k => $v) {
			if ($k == $key) { $order = $v; break; }
		}
		return $order;
	}



	/************************/
	/* POPULATE LAYOUT CELL */
	/************************/
	private function populateCell($laycell, $items, $type) {
		if (count($laycell) == 0) { return; }
		$eLang = eFactory::getLang();
		foreach ($laycell as $layitem) {
			if ($type == 'positions') {
				$nummods = 0;
				if ($items) {
					foreach ($items as $item) {
						if ($item->position == $layitem) {
							$nummods = $item->modules;
							break;
						}
					}
				}
				echo '<li class="laybox" id="'.$layitem.'">'.$layitem.' ('.$nummods.")</li>\n";
			} else {
				$modid = 0;
				$title = '';
				if ($items) {
					foreach ($items as $item) {
						$mod_withid = $item->module.':'.$item->id;
						if ($mod_withid == $layitem) {
							$modid = $item->id;
							$title = $item->title;
							break;
						}
					}
				}
				if ($modid > 0) {
					echo '<li class="laybox" id="'.$layitem.'"><a href="javascript:void(null);" title="'.$eLang->get('VIEW').'" onclick="fpModPreview('.$modid.')">'.$title."</a></li>\n";
				} else {
					echo '<li class="laybox" id="'.$layitem.'">'.$layitem.' (NOT FOUND!)'."</li>\n";
				}
			}
		}
	}
		
}

?>