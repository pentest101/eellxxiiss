<?php 
/**
* @version		$Id: agroups.php 2097 2019-02-24 08:24:14Z IOS $
* @package		Elxis
* @subpackage	Component User
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class agroupsUserController extends userController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $task='', $model=null) {
		if (eFactory::getElxis()->acl()->check('com_user', 'groups', 'manage') < 1) {
			exitPage::make('403', 'CUSE-0012');
		}
		parent::__construct($view, $task, $model);
	}


	/***************************************/
	/* PREPARE TO DISPLAY USER GROUPS LIST */
	/***************************************/
	public function listgroups() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();
		$pathway = eFactory::getPathway();

		$options = array('limit' => 1000, 'limitstart' => 0, 'sn' => 'level', 'so' => 'desc');
		$rows = $this->model->getGroups($options);

		$pathway->deleteAllNodes();
		$pathway->addNode($eLang->get('USERS_MANAGER'), 'user:users/');
		$pathway->addNode($eLang->get('USER_GROUPS'));

		$eDoc->setTitle($eLang->get('USER_GROUPS').' - '.$elxis->getConfig('SITENAME'));
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_user/inc/user.js');

		if ($rows) {
			$eDoc->addNativeDocReady('elx5DataTable(\'groupstbl\', false); elx5SortableTable(\'groupstbl\');');
		}

		$this->view->listgroups($rows, $elxis, $eLang);
	}


	/****************/
	/* DELETE GROUP */
	/****************/
	public function deletegroup() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'message' => '');

		if ($elxis->getConfig('SECURITY_LEVEL') > 0) {
			$response['message'] = 'The deletion of user groups is not allowed under the current security level!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$gid = (isset($_POST['elids'])) ? (int)$_POST['elids'] : 0;
		if ($gid < 1) {
			$response['message'] = 'Invalid group!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if (in_array($gid, array(1, 5, 6, 7))) {
			$response['message'] = $eLang->get('CNOT_DEL_GROUP');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$group = $this->model->getGroup($gid);
		if (!$group) {
			$response['message'] = $eLang->get('GROUPNFOUND');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($group['level'] >= $elxis->acl()->getLevel()) {
			$response['message'] = $eLang->get('NOTALLOWACTION');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($group['members'] > 0) {
			$response['message'] = $eLang->get('CNOT_DEL_GROUP_MEMBERS');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$ok = $this->model->deleteGroup($gid);
		if ($ok) {
			$response['success'] = 1;
		} else {
			$response['message'] = $eLang->get('ACTION_FAILED');
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/********************************/
	/* GET GROUP DATA (EDIT ACTION) */
	/********************************/
	public function getgroupdata() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$response = array('success' => 0, 'message' => '', 'gid' => 0, 'groupname' => '', 'level' => 0, 'members' => 0, 'gid' => 0, 'readonly' => 0, 'groupstree' => array());

		$response['gid'] = isset($_POST['gid']) ? (int)$_POST['gid'] : 0;
		if ($response['gid'] < 0) { $response['gid'] = 0; }
		
		if (in_array($response['gid'], array(1, 5, 6, 7))) { $response['readonly'] = 1; }

		if ($response['gid'] > 0) {
			$row = $this->model->getGroup($response['gid']);
			if (!$row) {
				$response['message'] = $eLang->get('GROUPNFOUND');
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}

			if ($row['level'] == $elxis->acl()->getLevel()) { $response['readonly'] = 1; }
			if ($row['level'] > $elxis->acl()->getLevel()) {
				$response['message'] = $eLang->get('NEED_HIGHER_ACCESS');
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}

			$response['level'] = $row['level'];
			$response['groupname'] = $row['groupname'];
			$response['members'] = $row['members'];
			unset($row);
		} else {
			$response['level'] = 2;
			$response['groupname'] = '';
			$response['members'] = 0;
		}

		$options = array('limitstart' => 0, 'limit' => 1000, 'sn' => 'level', 'so' => 'DESC');
		$groups = $this->model->getGroups($options);
		if ($groups) {
			$lastlevel = -1;
			$space = '';
			foreach ($groups as $group) {
				if ($group['gid'] == 1) {
					$groupname = $eLang->get('ADMINISTRATOR');
				} else if ($group['gid'] == 5) {
					$groupname = $eLang->get('USER');
				} else if ($group['gid'] == 6) {
					$groupname = $eLang->get('EXTERNALUSER');
				} elseif ($group['gid'] == 7) {
					$groupname = $eLang->get('GUEST');
				} else {
					$groupname = $group['groupname'];
				}

				if ($group['level'] != $lastlevel) {
					$space .= ($lastlevel == -1) ? '' : '&#160; &#160; ';
					$lastlevel = $group['level'];
				}
				if ($response['gid'] == $group['gid']) {
					$treename = $space.$group['level'].' - <strong>'.$groupname.'</strong>';
				} else {
					$treename = $space.$group['level'].' - '.$groupname;
				}
				$response['groupstree'][] = $treename;
				unset($item);
			}
		}
		unset($groups, $options);

		$response['success'] = 1;

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/*******************/
	/* SAVE USER GROUP */
	/*******************/
	public function savegroup() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'message' => '');

		$gid = isset($_POST['gid']) ? (int)$_POST['gid'] : 0;
		if ($gid < 0) { $gid = 0; }

		if (in_array($gid, array(1, 5, 6, 7))) {
			$response['message'] = $eLang->get('CNOT_MOD_GROUP');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$row = new groupsDbTable();
		if ($gid > 0) {
			if (!$row->load($gid)) {
				$response['message'] = $eLang->get('GROUPNFOUND');
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
		}

		$row->level = isset($_POST['level']) ? (int)$_POST['level'] : 0;
		$row->groupname = eUTF::trim(filter_input(INPUT_POST, 'groupname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if ($row->groupname == '') {
			$response['message'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('GROUP'));
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$row->level = (isset($_POST['level'])) ? (int)$_POST['level'] : 0;
		if (($row->level < 2) || ($row->level > 99)) {
			$response['message'] = 'Custom groups should have access from 2 to 99';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$mylevel = $elxis->acl()->getLevel();
		if ($row->level >= $mylevel) {
			$response['message'] = 'You can manage groups up to level of '.$mylevel.'!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$ok = ($gid > 0) ? $row->update() : $row->insert();
		if (!$ok) {
			$response['message'] = $row->getErrorMsg();
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($row->gid > 999) {
			$this->model->deleteGroup($row->gid);
			$response['message'] = 'A user group can not have an id greater than 999! Contact Elxis Team for support.';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$response['success'] = 1;

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}

}

?>