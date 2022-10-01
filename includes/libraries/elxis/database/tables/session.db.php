<?php 
/**
* @version		$Id: session.db.php 1777 2016-02-11 19:37:00Z sannosi $
* @package		Elxis
* @subpackage	Database
* @copyright	Copyright (c) 2006-2016 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


class sessionDbTable extends elxisDbTable {


	/*************************************************/
	/* CONSTRUCT PARENT CLASS AND SET INITIAL VALUES */
	/*************************************************/
	public function __construct() {
		parent::__construct('#__session', 'session_id');

		$this->columns = array(
			'session_id' => array('type' => 'string', 'value' => null),
			'uid' => array('type' => 'integer', 'value' => 0),
			'gid' => array('type' => 'integer', 'value' => 7),
			'login_method' => array('type' => 'string', 'value' => null),
			'first_activity' => array('type' => 'integer', 'value' => null),
			'last_activity' => array('type' => 'integer', 'value' => null),
			'clicks' => array('type' => 'integer', 'value' => 0),
			'current_page' => array('type' => 'string', 'value' => null),
			'ip_address' => array('type' => 'string', 'value' => null),
			'user_agent' => array('type' => 'string', 'value' => null),
			'session_data' => array('type' => 'string', 'value' => null)
		);

		$ts = eFactory::getDate()->getTS();
		$this->setValue('current_page', eFactory::getURI()->getElxisUri());
		$this->setValue('first_activity', $ts);
		$this->setValue('last_activity', $ts);
	}


	/**********************/
	/* CHECK ROW VALIDITY */
	/**********************/
	public function check() {
		if (trim($this->session_id) == '') {
			$this->errorMsg = 'Session id can not be empty!';
			return false;
		}
		if ($this->gid < 1) {
			$this->errorMsg = 'Group id value is invalid!';
			return false;
		}

		if (trim($this->current_page) != '') {
			$this->current_page = trim(filter_var($this->current_page, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
			$pat = "#([\']|[\(]|[\)]|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\~]|[\`]|[\^]|[\|]|[\{]|[\}]|[\\\])#u";
			$this->current_page = eUTF::trim(preg_replace($pat, '', $this->current_page));

			if (eUTF::strlen($this->current_page) > 255) {
				$this->current_page = eUTF::substr($this->current_page, 0, 254);
			}
		}

		if (trim($this->login_method) != '') {
			if (!file_exists(ELXIS_PATH.'/components/com_user/auth/'.$this->login_method.'/'.$this->login_method.'.auth.php')) {
				$this->errorMsg = 'Invalid authentication method!';
				return false;
			}
		}

		return true;
	}


	/**********************/
	/* PURGE OLD SESSIONS */
	/**********************/
	public function purge($maxLifetime = 1440) {
		$past = eFactory::getDate()->getTS() - (int)$maxLifetime;
		$sql = "DELETE FROM ".$this->db->quoteId($this->table)." WHERE ".$this->db->quoteId('last_activity')." < :past";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':past', $past, PDO::PARAM_INT);
		return $stmt->execute();
	}


	/****************************************/
	/* UPDATE LAST ACTIVITY TIME AND OTHERS */
	/****************************************/
	public function refresh() {
		$this->current_page = eFactory::getURI()->getUriString();
		if (eUTF::strlen($this->current_page) > 255) {
			$this->current_page = eUTF::substr($this->current_page, 0, 254);
		}
		$this->clicks++;
		$this->last_activity = eFactory::getDate()->getTS();
		return $this->update();
	}


	/***********************************************************/
	/* DELETE OLD SESSION FROM DB (AFTER REGENERATE/FORCE NEW) */
	/***********************************************************/
	public function removeOld($old_session_id) {
		$sql = "DELETE FROM ".$this->db->quoteId($this->table)." WHERE ".$this->db->quoteId('session_id')." = :xsess";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xsess', $old_session_id, PDO::PARAM_STR);
		return $stmt->execute();
	}

}

?>