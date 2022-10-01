<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Database
* @copyright	Copyright (c) 2006-2016 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


class bookmarksDbTable extends elxisDbTable {


	/*************************************************/
	/* CONSTRUCT PARENT CLASS AND SET INITIAL VALUES */
	/*************************************************/
	public function __construct() {
		parent::__construct('#__bookmarks', 'id');

		$this->columns = array(
			'id' => array('type' => 'integer', 'value' => null),
			'uid' => array('type' => 'integer', 'value' => 0),
			'cid' => array('type' => 'integer', 'value' => 0),
			'created' => array('type' => 'string', 'value' => '1970-01-01 00:00:00'),
			'reminderdate' => array('type' => 'string', 'value' => '1970-01-01 00:00:00'),
			'remindersent' => array('type' => 'bit', 'value' => 0),
			'title' => array('type' => 'string', 'value' => null),
			'link' => array('type' => 'string', 'value' => null),
			'note' => array('type' => 'text', 'value' => null)
		);

		$elxis = eFactory::getElxis();
		$uid = (int)$elxis->user()->uid;
		$this->setValue('created', eFactory::getDate()->getDate());
		$this->setValue('uid', $uid);
		$this->setValue('cid', 1);
	}


	/**********************/
	/* CHECK ROW VALIDITY */
	/**********************/
	public function check() {
		$this->uid = (int)$this->uid;
		$this->cid = (int)$this->cid;
		$this->remindersent = (int)$this->remindersent;

		if ($this->uid < 1) {
			$this->errorMsg = 'Bookmarks must be assigned to users!';
			return false;
		}
		if ($this->cid < 1) {
			$this->errorMsg = 'Please select a category!';
			return false;
		}

		if (trim($this->created) == '') {
			$this->created = eFactory::getDate()->getDate();
		} else if (strlen($this->created) != 19) {
			$this->created = eFactory::getDate()->getDate();
		}

		return true;
	}

}

?>