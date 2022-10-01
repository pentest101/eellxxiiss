<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Database
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


class messagesDbTable extends elxisDbTable {


	/*************************************************/
	/* CONSTRUCT PARENT CLASS AND SET INITIAL VALUES */
	/*************************************************/
	public function __construct() {
		parent::__construct('#__messages', 'id');

		$this->columns = array(
			'id' => array('type' => 'integer', 'value' => null),
			'fromid' => array('type' => 'integer', 'value' => 0),
			'fromname' => array('type' => 'string', 'value' => null),
			'toid' => array('type' => 'integer', 'value' => 0),
			'toname' => array('type' => 'string', 'value' => null),
			'msgtype' => array('type' => 'string', 'value' => null),
			'message' => array('type' => 'text', 'value' => null),
			'created' => array('type' => 'string', 'value' => '1970-01-01 00:00:00'),
			'read' => array('type' => 'bit', 'value' => 0),
			'replyto' => array('type' => 'integer', 'value' => 0),
			'delbyfrom' => array('type' => 'bit', 'value' => 0),
			'delbyto' => array('type' => 'bit', 'value' => 0)
		);

		$this->setValue('created', eFactory::getDate()->getDate());
	}


	/**********************/
	/* CHECK ROW VALIDITY */
	/**********************/
	public function check() {
		$this->fromid = (int)$this->fromid;
		$this->toid = (int)$this->toid;
		$this->read = (int)$this->read;
		$this->replyto = (int)$this->replyto;
		$this->delbyfrom = (int)$this->delbyfrom;
		$this->delbyto = (int)$this->delbyto;

		if (trim($this->message) == '') {
			$this->errorMsg = 'Message can not be empty!';
			return false;
		}

		if ($this->fromid == 0) {
			$this->delbyfrom = 1;
			if ($this->fromname == '') {
				$this->errorMsg = 'You must specify the sender of the message!';
				return false;
			}
		}
		if ($this->toid == 0) {
			$this->delbyto = 1;
			if ($this->toname == '') {
				$this->errorMsg = 'You must specify a recipient for the message!';
				return false;
			}
		}
		if (($this->created == '') || ($this->created == '1970-01-01 00:00:00')) {
			$this->created = eFactory::getDate()->getDate();
		}

		return true;
	}

}

?>