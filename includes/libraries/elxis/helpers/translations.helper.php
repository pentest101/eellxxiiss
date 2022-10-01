<?php 
/**
* @version		$Id: translations.helper.php 2123 2019-03-01 18:07:30Z IOS $
* @package		Elxis
* @subpackage	Helpers
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisTranslationsHelper {

	private $db = null;


	/***************/
	/* CONSTRUCTOR */
	/***************/
	public function __construct() {
		$this->db = eFactory::getDB();
	}


	/*****************************/
	/* SAVE ELEMENT TRANSLATIONS */
	/*****************************/
	public function saveElementTranslations($cat, $element, $elid, $translations) {
		$adds = array();
		$deletes = array();
		$updates = array();
		$sames = array();

		$sql = "SELECT ".$this->db->quoteId('trid').", ".$this->db->quoteId('language').", ".$this->db->quoteId('translation')." FROM ".$this->db->quoteId('#__translations')
		."\n WHERE ".$this->db->quoteId('category').' = :xcat AND '.$this->db->quoteId('element').' = :xelem AND '.$this->db->quoteId('elid').' = :xelid';
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xcat', $cat, PDO::PARAM_STR);
		$stmt->bindParam(':xelem', $element, PDO::PARAM_STR);
		$stmt->bindParam(':xelid', $elid, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if ($rows) {
			foreach ($rows as $row) {
				$lng = $row['language'];
				if (isset($translations[$lng])) {
					if ($translations[$lng] == '') {//delete
						$deletes[] = $row['trid'];
					} else if ($translations[$lng] != $row['translation']) {//update
						$updates[] = array($row['trid'], $lng, $translations[$lng]);
					} else {//exactly the same, no change
						$sames[] = array($row['trid'], $lng, $translations[$lng]);
					}
				} else {//delete
					$deletes[] = $row['trid'];
				}
			}
		}

		if ($translations) {
			foreach ($translations as $lng => $translation) {
				if ($translation == '') { continue; }
				if ($updates) {
					$is_update = false;
					foreach ($updates as $update) {
						if ($lng == $update[1]) { $is_update = true; break; }
					}
					if ($is_update) { continue; }
				}
				if ($sames) {
					$is_same = false;
					foreach ($sames as $same) {
						if ($lng == $same[1]) { $is_same = true; break; }
					}
					if ($is_same) { continue; }
				}
				$adds[] = array($lng, $translation);
			}
		}

		if ($deletes) {
			$sql = "DELETE FROM ".$this->db->quoteId('#__translations')." WHERE ".$this->db->quoteId('trid')." IN (".implode(',', $deletes).")";
			$stmt = $this->db->prepare($sql);
			$stmt->execute();
		}
		if ($updates) {
			$sql = "UPDATE ".$this->db->quoteId('#__translations')
			."\n SET ".$this->db->quoteId('translation')." = :xtr WHERE ".$this->db->quoteId('trid')." = :xtrid";
			$stmt = $this->db->prepare($sql);
			foreach ($updates as $update) {
				$stmt->bindParam(':xtr', $update[2], PDO::PARAM_STR);
				$stmt->bindParam(':xtrid', $update[0], PDO::PARAM_INT);
				$stmt->execute();
			}
		}

		if ($adds) {
			elxisLoader::loadFile('includes/libraries/elxis/database/tables/translations.db.php');
			foreach ($adds as $add) {
				$row = new translationsDbTable();
				$row->category = $cat;
				$row->element = $element;
				$row->language = $add[0];
				$row->elid = $elid;
				$row->translation = $add[1];
				$row->insert();
				unset($row);
			}
		}
	}


	/*******************************/
	/* DELETE ELEMENT TRANSLATIONS */
	/*******************************/
	public function deleteElementTranslations($cat, $element, $elid) {
		$sql = "DELETE FROM ".$this->db->quoteId('#__translations')
		."\n WHERE ".$this->db->quoteId('category').' = :xcat AND '.$this->db->quoteId('element').' = :xelem AND '.$this->db->quoteId('elid').' = :xelid';
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xcat', $cat, PDO::PARAM_STR);
		$stmt->bindParam(':xelem', $element, PDO::PARAM_STR);
		$stmt->bindParam(':xelid', $elid, PDO::PARAM_INT);
		$stmt->execute();
	}

}

?>