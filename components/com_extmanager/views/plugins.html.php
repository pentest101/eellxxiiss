<?php 
/**
* @version		$Id: plugins.html.php 2393 2021-04-07 19:54:28Z IOS $
* @package		Elxis
* @subpackage	Component Extensions Manager
* @copyright	Copyright (c) 2006-2021 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class pluginsExtmanagerView extends extmanagerView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/************************/
	/* DISPLAY PLUGIN USAGE */
	/************************/
	public function pluginusageHTML($plugin, $plugin_title, $usage, $elxis, $eLang) {
		$title = sprintf($eLang->get('USAGE_OF'), $plugin_title);

		$total_counter = 0;
?>
		<div class="elx5_mpad">
		<div class="elx5_box elx5_border_blue">
			<div class="elx5_box_body">
				<div class="elx5_dataactions elx5_spad">
					<h3 class="elx5_box_title"><?php echo $title; ?> <span>(plugin <?php echo $plugin; ?>)</span></h3>
				</div>
				<div class="elx5_actionsbox elx5_dspace">
					<table class="elx5_datatable">
						
<?php 
					$total = $usage['articles'] ? count($usage['articles']) : 0;
					$total_counter += $total;
					echo '<tr><th>'.$eLang->get('ID').'</th><th>'.$eLang->get('EXTMAN_ARTICLES').' <span class="elx5_orange" dir="ltr">('.$total.')</span></th></tr>'."\n";
					if ($usage['articles']) {
						$link = $elxis->makeAURL('content:articles/');
						$k = 0;
						foreach ($usage['articles'] as $id => $title) {
							if ($k > 9) { break; }
							echo '<tr><td>'.$id.'</td><td><a href="'.$link.'edit.html?id='.$id.'" title="'.$eLang->get('EDIT').'" target="_blank">'.$title.'</a></td></tr>'."\n";
							$k++;
						}
						if ($total > 10) {
							$rest = $total - 10;
							echo '<tr><td colspan="2">and in other <strong>'.$rest.'</strong> articles</td></tr>'."\n";
						}
					} else {
						echo '<tr class="elx5_rowwarn"><td class="elx5_center" colspan="2">'.$eLang->get('NO_RESULTS')."</td></tr>\n";
					}

					$total = $usage['modules'] ? count($usage['modules']) : 0;
					$total_counter += $total;
					echo '<tr><th>'.$eLang->get('ID').'</th><th>'.$eLang->get('MODULE').' mod_content <span class="elx5_orange" dir="ltr">('.$total.')</span></th></tr>'."\n";
					if ($usage['modules']) {
						$link = $elxis->makeAURL('extmanager:modules/');
						$k = 0;
						foreach ($usage['modules'] as $id => $title) {
							if ($k > 9) { break; }
							echo '<tr><td>'.$id.'</td><td><a href="'.$link.'edit.html?id='.$id.'" title="'.$eLang->get('EDIT').'" target="_blank">'.$title.'</a></td></tr>'."\n";
							$k++;
						}
						if ($total > 10) {
							$rest = $total - 10;
							echo '<tr><td colspan="2">and in other <strong>'.$rest.'</strong> modules</td></tr>'."\n";
						}
					} else {
						echo '<tr class="elx5_rowwarn"><td class="elx5_center" colspan="2">'.$eLang->get('NO_RESULTS')."</td></tr>\n";
					}

					if (file_exists(ELXIS_PATH.'/components/com_shop/shop.php')) {
						$total = $usage['products'] ? count($usage['products']) : 0;
						$total_counter += $total;
						echo '<tr><th>'.$eLang->get('ID').'</th><th>Open shop products <span class="elx5_orange" dir="ltr">('.$total.')</span></th></tr>'."\n";
						if ($usage['products']) {
							$link = $elxis->makeAURL('shop:products/');
							$k = 0;
							foreach ($usage['products'] as $id => $title) {
								if ($k > 9) { break; }
								echo '<tr><td>'.$id.'</td><td><a href="'.$link.'edit.html?id='.$id.'" title="'.$eLang->get('EDIT').'" target="_blank">'.$title.'</a></td></tr>'."\n";
								$k++;
							}
							if ($total > 10) {
								$rest = $total - 10;
								echo '<tr><td colspan="2">and in other <strong>'.$rest.'</strong> open shop products</td></tr>'."\n";
							}
						} else {
							echo '<tr class="elx5_rowwarn"><td class="elx5_center" colspan="2">'.$eLang->get('NO_RESULTS')."</td></tr>\n";
						}
					}
?>
					</table>

<?php 
				if ($total_counter == 0) {
					echo '<div class="elx5_info elx5_tlspace">'.$eLang->get('PLUGIN_USAGE_ADVISOR')."</div>\n";
				}
?>

				</div>
			</div>
		</div>
		</div>

<?php 
	}
}

?>