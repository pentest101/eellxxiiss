<?php 
/**
* @version		$Id: aaccess.php 2345 2020-03-08 18:24:58Z IOS $
* @package		Elxis
* @subpackage	Component User
* @copyright	Copyright (c) 2006-2020 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class aaccessUserController extends userController {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $task='', $model=null, $json=false) {
		$elxis = eFactory::getElxis();
		if ($elxis->acl()->check('com_user', 'acl', 'manage') < 1) {
			if ($json) {
				$response = array('success' => 0, 'message' => 'You are not allowed to manage ACL!');
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			} else {
				exitPage::make('403', 'CUSE-0014');
			}
		}

		if ($elxis->getConfig('SECURITY_LEVEL') > 0) {
			if ($elxis->user()->gid <> 1) {
				$msg = eFactory::getLang()->get('SECLEVEL_ACC_ADMIN');
				if ($json) {
					$response = array('success' => 0, 'message' => $msg);
					$this->ajaxHeaders('application/json');
					echo json_encode($response);
					exit;
				} else {
					$redirurl = $elxis->makeAURL('cpanel:/');
					$elxis->redirect($redirurl, $msg, true);					
				}
			}
		}

		parent::__construct($view, $task, $model);
	}


	/*******************************/
	/* PREPARE TO DISPLAY ACL LIST */
	/*******************************/
	public function listacl() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$pathway = eFactory::getPathway();
		$eDoc = eFactory::getDocument();

		$options = array('limit' => 20, 'page' => 1, 'maxpage' => 1, 'sn' => 'category', 'so' => 'asc', 'limitstart' => 0, 'total' => 0, 'minlevel' => -1, 'gid' => -1, 'uid' => -1);
		if (isset($_GET['minlevel'])) {
			if ($_GET['minlevel'] != '') { $options['minlevel'] = (int)$_GET['minlevel']; }
		}
		if (isset($_GET['gid'])) {
			if ($_GET['gid'] != '') { $options['gid'] = (int)$_GET['gid']; }
		}
		if (isset($_GET['uid'])) {
			if ($_GET['uid'] != '') { $options['uid'] = (int)$_GET['uid']; }
		}
		$querycols = array('category', 'element', 'action');
		foreach ($querycols as $col) {
			$options[$col] = eUTF::trim(filter_input(INPUT_GET, $col, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		}

		$options['limit'] = (isset($_GET['limit'])) ? (int)$_GET['limit'] : 20;
		if ($options['limit'] < 1) { $options['limit'] = 20; }
		$elxis->updateCookie('limit', $options['limit']);

		$options['page'] = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
		if ($options['page'] < 1) { $options['page'] = 1; }
		$options['sn'] = (isset($_GET['sn'])) ? trim($_GET['sn']) : 'category';
		if ($options['sn'] == '') { $options['sn'] = 'category'; }
		if (!in_array($options['sn'], array('category', 'element', 'action', 'minlevel', 'gid', 'uid', 'aclvalue'))) { $options['sn'] = 'category'; }
		$options['so'] = (isset($_GET['so'])) ? trim($_GET['so']) : 'asc';
		if ($options['so'] != 'desc') { $options['so'] = 'asc'; }

		$options['total'] = $this->model->countACL($options);

		$rows = array();
		$options['maxpage'] = ceil($options['total']/$options['limit']);
		if ($options['maxpage'] < 1) { $options['maxpage'] = 1; }
		if ($options['page'] > $options['maxpage']) { $options['page'] = $options['maxpage']; }
		$options['limitstart'] = (($options['page'] - 1) * $options['limit']);
		if ($options['total'] > 0) {
			$rows = $this->model->getACL($options);
		}

		$acldata = $this->model->getACLcea();
		$groups = $this->model->getGroupsList();

		$pathway->deleteAllNodes();
		$pathway->addNode($eLang->get('USERS_MANAGER'), 'user:users/');
		$pathway->addNode($eLang->get('ACL'));

		$eDoc->setTitle($eLang->get('ACL').' - '.$elxis->getConfig('SITENAME'));
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_user/inc/user.js');
		if ($rows) {
			$eDoc->addNativeDocReady('elx5DataTable(\'acltbl\', true);');
		}

		$this->view->listacl($rows, $options, $acldata, $groups, $elxis, $eLang);
	}


	/*************************/
	/* DELETE ACL ELEMENT(S) */
	/*************************/
	public function deleteacl() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'message' => '');

		if ($elxis->getConfig('SECURITY_LEVEL') > 0) {
			$response['message'] = 'The deletion of ACL elements is not allowed under the current security level!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$ids = array();
		$elids = isset($_POST['elids']) ? trim($_POST['elids']) : '';//multiple select
		if ($elids != '') {
			$parts = explode(',', $elids);
			foreach ($parts as $part) {
				$id = (int)$part;
				if ($id > 0) { $ids[] = $id; }
			}
		}

		if (!$ids) {
			$response['message'] = $eLang->get('NO_ITEMS_SELECTED');
			$this->sendHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$delete_ids = (count($ids) == 1) ? (int)$ids[0] : $ids;
		$ok = $this->model->deleteACL($delete_ids);
		if (!$ok) {
			$response['message'] = $eLang->get('ACTION_FAILED');
		} else {
			$response['success'] = 1;
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/******************************/
	/* GET ACL DATA (EDIT ACTION) */
	/******************************/
	public function getacldata() {
		$response = array(
			'success' => 0, 'message' => '', 'aclid' => 0, 'category' => '', 'element' => '', 'identity' => 0,
			'aclaction' => '', 'minlevel' => 0, 'gid' => 0, 'uid' => 0, 'aclvalue' => 0
		);

		$response['aclid'] = isset($_POST['aclid']) ? (int)$_POST['aclid'] : 0;
		if ($response['aclid'] < 1) {
			$response['message'] = 'Invalid ACL item!';
		} else {
			$row = new aclDbTable();
			if (!$row->load($response['aclid'])) {
				$response['message'] = 'ACL item not found!';
			}
		}
		if ($response['message'] != '') {
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$response['category'] = $row->category;
		$response['element'] = $row->element;
		$response['identity'] = $row->identity;
		$response['aclaction'] = $row->action;
		$response['minlevel'] = $row->minlevel;
		$response['gid'] = $row->gid;
		$response['uid'] = $row->uid;
		$response['aclvalue'] = $row->aclvalue;
		$response['success'] = 1;

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/*****************/
	/* SAVE ACL RULE */
	/*****************/
	public function save() {
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'message' => '');

		$id = isset($_POST['aclid']) ? (int)$_POST['aclid'] : 0;
		$row = new aclDbTable();
		if ($id > 0) {
			if (!$row->load($id)) {
				$response['message'] = 'ACL rule with ID '.$id.' not found!';
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
		}

		$row->gid = isset($_POST['gid']) ? (int)$_POST['gid'] : 0;
		$row->uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
		$row->minlevel = isset($_POST['minlevel']) ? (int)$_POST['minlevel'] : 0;
		$row->aclvalue = isset($_POST['aclvalue']) ? (int)$_POST['aclvalue'] : 0;

		if ($row->uid > 0) {
			$row->gid = 0;
			$row->minlevel = -1;
		} elseif ($row->gid > 0) {
			$row->uid = 0;
			$row->minlevel = -1;
		} else {
			$row->gid = 0;
			$row->uid = 0;
			if ($row->minlevel < 0) { $row->minlevel = 0; }
		}

		if ($id < 1) {
			$row->identity = isset($_POST['identity']) ? (int)$_POST['identity'] : 0;
			$row->category = isset($_POST['category']) ? trim(strtolower($_POST['category'])) : '';
			$category_sanitized = preg_replace('/[^A-Z\-\_0-9]/i', '', $row->category);
			$row->element = isset($_POST['element']) ? trim(strtolower($_POST['element'])) : '';
			$element_sanitized = preg_replace('/[^A-Z\-\_0-9]/i', '', $row->element);
			$row->action = isset($_POST['aclaction']) ? trim(strtolower($_POST['aclaction'])) : '';
			$action_sanitized = preg_replace('/[^A-Z\-\_0-9]/i', '', $row->action);

			if ($row->category == '') {
				$response['message'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('CATEGORY'));
			} else if ($row->element == '') {
				$response['message'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('ELEMENT'));
			} else if ($row->action == '') {
				$response['message'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('ACTION'));
			} else if ($row->category != $category_sanitized) {
				$response['message'] = sprintf($eLang->get('FIELDNOACCCHAR'), $eLang->get('CATEGORY'));
			} else if ($row->element != $element_sanitized) {
				$response['message'] = sprintf($eLang->get('FIELDNOACCCHAR'), $eLang->get('ELEMENT'));
			} else if ($row->action != $action_sanitized) {
				$response['message'] = sprintf($eLang->get('FIELDNOACCCHAR'), $eLang->get('ACTION'));
			} else if (($row->category == 'module') && ($row->identity == 0)) {
				$response['message'] = $eLang->get('MODID_NOTSET');
			}

			if ($response['message'] != '') {
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
		}

		$count_mr = $this->model->countMatchRules($id, $row->category, $row->element, $row->identity, $row->action, $row->minlevel, $row->gid, $row->uid);
		if ($count_mr > 0) {
			$response['message'] = $count_mr.' :: '.$eLang->get('ACCRULE_EXISTS');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}
		if ($row->minlevel > -1) {
			$count_lr = $this->model->countLevelRules($id, $row->category, $row->element, $row->identity, $row->action);
			if ($count_lr > 0) {
				$response['message'] = $eLang->get('ACCRULE_MINLEVEL_EXISTS');
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
		}

		$ok = ($id > 0) ? $row->update() : $row->insert();
		if (!$ok) {
			$response['message'] = $row->getErrorMsg();
		} else {
			$response['success'] = 1;
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/***************************************************************************/
	/* SAVE ACL FROM A POST REQUEST AND REPLY AS JSON (used by com_extmanager) */
	/***************************************************************************/
	public function savejson() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		if ($id < 0) { $id = 0; }

		$response = array();
		$response['error'] = 0;
		$response['errormsg'] = '';

		$row = new aclDbTable();
		if ($id > 0) {
			if (!$row->load($id)) {
				$response['error'] = 1;
				$response['errormsg'] = 'Could not load ACL element!';
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
		}

		$minlevel = (isset($_POST['minlevel'])) ? (int)$_POST['minlevel'] : 0;
		$gid = (isset($_POST['gid'])) ? (int)$_POST['gid'] : 0;
		$uid = (isset($_POST['uid'])) ? (int)$_POST['uid'] : 0;
		$aclvalue = (isset($_POST['aclvalue'])) ? (int)$_POST['aclvalue'] : 1;
		$identity = (isset($_POST['identity'])) ? (int)$_POST['identity'] : 0;
		if ($aclvalue < 0) { $aclvalue = 0; }
		if ($identity < 0) { $identity = 0; }
		if ($uid > 0) {
			$user = $this->model->getUser($uid);
			if (!$user) {
				$response['error'] = 1;
				$response['errormsg'] = $eLang->get('USERNFOUND');
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
			$gid = 0;
			$minlevel = -1;			
		} elseif ($gid > 0) {
			$group = $this->model->getGroup($gid);
			if (!$group) {
				$response['error'] = 1;
				$response['errormsg'] = $eLang->get('GROUPNFOUND');
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
			$uid = 0;
			$minlevel = -1;
		} else {
			$gid = 0;
			$uid = 0;
			if ($minlevel < 0) { $minlevel = 0; }
		}

		$row->minlevel = $minlevel;
		$row->gid = $gid;
		$row->uid = $uid;
		$row->aclvalue = $aclvalue;
	
		if ($id == 0) {
			$row->identity = $identity;
			$row->category = strtolower(trim($_POST['category']));
			$row->element = strtolower(trim($_POST['element']));
			$row->action = strtolower(trim($_POST['action']));
			$category_sanitized = preg_replace('/[^A-Z\-\_0-9]/i', '', $row->category);
			$element_sanitized = preg_replace('/[^A-Z\-\_0-9]/i', '', $row->element);
			$action_sanitized = preg_replace('/[^A-Z\-\_0-9]/i', '', $row->action);
		} else {
			$category_sanitized = $row->category;
			$element_sanitized = $row->element;
			$action_sanitized = $row->action;
		}

		$errormsg = '';
		if ($row->category == '') {
			$errormsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('CATEGORY'));
		} else if ($row->element == '') {
			$errormsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('ELEMENT'));
		} else if ($row->action == '') {
			$errormsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('ACTION'));
		} else if ($row->category != $category_sanitized) {
			$errormsg = sprintf($eLang->get('FIELDNOACCCHAR'), $eLang->get('CATEGORY'));
		} else if ($row->element != $element_sanitized) {
			$errormsg = sprintf($eLang->get('FIELDNOACCCHAR'), $eLang->get('ELEMENT'));
		} else if ($row->action != $action_sanitized) {
			$errormsg = sprintf($eLang->get('FIELDNOACCCHAR'), $eLang->get('ACTION'));
		} else if (($row->category == 'module') && ($row->identity == 0)) {
			$errormsg = $eLang->get('MODID_NOTSET');
		} else {
			$continue = true;
			$count_mr = $this->model->countMatchRules($id, $row->category, $row->element, $row->identity, $row->action, $row->minlevel, $row->gid, $row->uid);
			if ($count_mr > 0) {
				$continue = false;
				$errormsg = $eLang->get('ACCRULE_EXISTS');
			}
			if ($row->minlevel > -1) {
				$count_lr = $this->model->countLevelRules($id, $row->category, $row->element, $row->identity, $row->action);
				if ($count_lr > 0) {
					$continue = false;
					$errormsg = $eLang->get('ACCRULE_MINLEVEL_EXISTS');
				}
			}
			if ($continue) {
				$ok = ($id > 0) ? $row->update() : $row->insert();
				if (!$ok) {
					$errormsg = $row->getErrorMsg();
				}
			}
		}

		if ($errormsg != '') {
			$response['error'] = 1;
			$response['errormsg'] = $errormsg;
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		} else {
			switch ($row->gid) {
				case 0: $gidtext = $eLang->get('NONE'); break;
				case 1: $gidtext = $eLang->get('ADMINISTRATOR').' (1)'; break;
				case 5: $gidtext = $eLang->get('USER').' (5)'; break;
				case 6: $gidtext = $eLang->get('EXTERNALUSER').' (6)'; break;
				case 7: $gidtext = $eLang->get('GUEST').' (7)'; break;
				default: $gidtext = $group['groupname'].' ('.$row->gid.')'; break;
			}

			if ($row->uid > 0) {
				$uidtext = ($elxis->getConfig('REALNAME') == 1) ? $user->firstname.' '.$user->lastname : $user->uname;
				$uidtext .= ' ('.$row->uid.')';
			} else {
				$uidtext = $eLang->get('NOONE');
			}

			$response['id'] = $row->id;
			$response['category'] = $row->category;
			$response['element'] = $row->element;
			$response['elementtext'] = $eLang->silentGet($row->element, true);
			$response['action'] = $row->action;
			$response['actiontext'] = $eLang->silentGet($row->action, true);
			$response['minlevel'] = $row->minlevel;
			$response['minleveltext'] = $row->minlevel;
			$response['gid'] = $row->gid;
			$response['gidtext'] = $gidtext;
			$response['uid'] = $row->uid;
			$response['uidtext'] =  $uidtext;
			$response['aclvalue'] = $row->aclvalue;
			//$response['editicon'] = $elxis->icon('edit', 16);
			//$response['deleteicon'] = $elxis->icon('delete', 16);

			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}
	}

}

?>