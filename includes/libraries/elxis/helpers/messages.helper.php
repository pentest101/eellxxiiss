<?php 
/**
* @version		$Id: messages.helper.php 1826 2016-05-26 17:55:28Z sannosi $
* @package		Elxis
* @subpackage	Helpers
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisMessagesHelper {


	private $total_threads = 0;


	/***************/
	/* CONSTRUCTOR */
	/***************/
	public function __construct() {
	}
	

	/******************/
	/* COUNT MESSAGES */
	/******************/
	public function countMessages($options=array()) {
		$db = eFactory::getDB();

		if (!is_array($options)) { $options = array(); }

		$binds = $this->bindOptions($options);

		$sql = "SELECT COUNT(".$db->quoteId('id').") FROM ".$db->quoteId('#__messages');
		if ($binds) {
			foreach ($binds as $k => $bind) {
				if ($k == 0) {
					$sql .= ' WHERE '.$db->quoteId($bind[0]).' '.$bind[1].' :xval'.$k."\n";
				} else {
					$sql .= ' AND '.$db->quoteId($bind[0]).' '.$bind[1].' :xval'.$k."\n";
				}
			}
		}
		$stmt = $db->prepare($sql);
		if ($binds) {
			foreach ($binds as $k => $bind) {
				$stmt->bindParam(':xval'.$k, $bind[2], $bind[3]);
			}
		}
		$stmt->execute();
		$num = (int)$stmt->fetchResult();

		return $num;
	}


	/****************/
	/* GET MESSAGES */
	/****************/
	public function getMessages($options=array()) {
		$db = eFactory::getDB();

		if (!is_array($options)) { $options = array(); }

		$binds = $this->bindOptions($options);

		$orderby = isset($options['orderby']) ? trim($options['orderby']) : 'created';
		$orderdir = isset($options['orderdir']) ? strtoupper($options['orderdir']) : 'DESC';
		$limit = isset($options['limit']) ? (int)$options['limit'] : 0;
		$limitstart = isset($options['limitstart']) ? (int)$options['limitstart'] : 0;
		$userdata = isset($options['userdata']) ? (int)$options['userdata'] : 0;
		if ($orderby == '') { $orderby = 'created'; }
		if ($orderdir != 'ASC') { $orderdir = 'DESC'; }

		$sql = "SELECT * FROM ".$db->quoteId('#__messages');
		if ($binds) {
			foreach ($binds as $k => $bind) {
				if ($k == 0) {
					$sql .= ' WHERE '.$db->quoteId($bind[0]).' '.$bind[1].' :xval'.$k."\n";
				} else {
					$sql .= ' AND '.$db->quoteId($bind[0]).' '.$bind[1].' :xval'.$k."\n";
				}
			}
		}
		$sql .= ' ORDER BY '.$db->quoteId($orderby).' '.$orderdir;
		if ($limit > 0) {
			$stmt = $db->prepareLimit($sql, $limitstart, $limit);
		} else {
			$stmt = $db->prepare($sql);
		}
		if ($binds) {
			foreach ($binds as $k => $bind) {
				$stmt->bindParam(':xval'.$k, $bind[2], $bind[3]);
			}
		}

		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
		if (!$rows) { return false; }

		$convertlinks = isset($options['convertlinks']) ? (int)$options['convertlinks'] : 1;
		if ($convertlinks) {
			$elxis = eFactory::getElxis();

			foreach ($rows as $i => $row) {
				//convert URLs to links
				$rows[$i]->message = preg_replace("~[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]~", "<a href=\"\\0\" target=\"_blank\">\\0</a>", $row->message);

				//convert Elxis URIs to links
				preg_match_all('/(#elink:[a-z0-9\:\.\_\-\/]+)/i', $row->message, $matches, PREG_PATTERN_ORDER);
				if ($matches) {
					foreach ($matches[0] as $k => $match) {
						$elxis_uri = preg_replace('/^(\#elink\:)/i', '', $match);
						$url = $elxis->makeURL($elxis_uri);
						$parts = preg_split('@\/@', $url, -1, PREG_SPLIT_NO_EMPTY);
						$c = count($parts) - 1;
						$href = '<a href="'.$url.'" target="_blank">'.$parts[$c].'</a>';
						$rows[$i]->message = str_replace($matches[0][$k], $href, $rows[$i]->message);
					}
				}
			}
		}

		$userdata = isset($options['userdata']) ? (int)$options['userdata'] : 0;

		if ($userdata != 1) { return $rows; }

		$uids = array();
		foreach ($rows as $row) {
			if ($row->fromid > 0) { $uids[] = $row->fromid; }
			if ($row->toid > 0) { $uids[] = $row->toid; }
		}
		if (!$uids) { return $rows; }
		$uids = array_unique($uids);

		$sql = "SELECT ".$db->quoteId('uid').", ".$db->quoteId('uname').", ".$db->quoteId('avatar').", ".$db->quoteId('email')
		."\n FROM ".$db->quoteId('#__users');
		if (count($uids) == 1) {
			$sql .= "\n WHERE ".$db->quoteId('uid').' = '.$uids[0];
		} else {
			$sql .= "\n WHERE ".$db->quoteId('uid')." IN (".implode(', ', $uids).")";
		}
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$users = $stmt->fetchAllAssoc('uid', PDO::FETCH_ASSOC);
		if (!$users) { $users = array(); }

		foreach ($rows as $i => $row) {
			$toid = $row->toid;
			$fromid = $row->fromid;
			$rows[$i]->to_uname = '';
			$rows[$i]->to_avatar = '';
			$rows[$i]->to_email = '';
			$rows[$i]->from_uname = '';
			$rows[$i]->from_avatar = '';
			$rows[$i]->from_email = '';
			if ($toid > 0) {
				if (isset($users[$toid])) {
					$rows[$i]->to_uname = $users[$toid]['uname'];
					$rows[$i]->to_avatar = $users[$toid]['avatar'];
					$rows[$i]->to_email = $users[$toid]['email'];
				}
			}
			if ($fromid > 0) {
				if (isset($users[$fromid])) {
					$rows[$i]->from_uname = $users[$fromid]['uname'];
					$rows[$i]->from_avatar = $users[$fromid]['avatar'];
					$rows[$i]->from_email = $users[$fromid]['email'];
				}
			}
		}

		return $rows;
	}


	/*****************************/
	/* BIND OPTIONS TO SQL QUERY */
	/*****************************/
	private function bindOptions($options) {
		$binds = array();

		if (!is_array($options) || (count($options) == 0)) { return $binds; }

		foreach ($options as $k => $v) {
			switch ($k) {
				case 'id':
				case 'fromid':
				case 'toid':
				case 'read':
				case 'replyto':
					$v = (int)$v;
					if ($v > -1) { $binds[] = array($k, '=', $v, PDO::PARAM_INT); }
				break;
				case 'fromname':
				case 'toname':
				case 'msgtype':
					$v = eUTF::trim($v);
					if ($v != '') { $binds[] = array($k, '=', $v, PDO::PARAM_STR); }
				break;
				case 'message':
				case 'created':
					$v = eUTF::trim($v);
					if ($v != '') {
						$v2 = '%'.$v.'%';
						$binds[] = array($k, 'LIKE', $v2, PDO::PARAM_STR); 
					}
				break;
				default: break;
			}
		}

		return $binds;
	}


	/************************/
	/* GET MESSAGES THREADS */
	/************************/
	public function getThreads($uid, $limitstart=0, $limit=10, $convertlinks=false) {
		$db = eFactory::getDB();

		$this->total_threads = 0;

		$sql = "SELECT * FROM ".$db->quoteId('#__messages')." WHERE ".$db->quoteId('fromid')." = :xuid OR ".$db->quoteId('toid')." = :xuid"
		."\n ORDER BY ".$db->quoteId('created')." DESC";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':xuid', $uid, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
		if (!$rows) { return array(); }

		$threads = array();
		//first pass, create threads
		foreach ($rows as $k => $row) {
			if ($row->replyto == 0) {
				$id = (int)$row->id;
				if (($row->fromid == $uid) && ($row->delbyfrom == 1)) {//thread deleted by original sender
					unset($rows[$k]);
					continue;
				}
				if (($row->toid == $uid) && ($row->delbyto == 1)) {//thread deleted by original recipient
					unset($rows[$k]);
					continue;
				}
				$threads[$id] = $row;
				unset($rows[$k]);
			}
		}

		//second pass, get latest message on each thread
		if ($rows) {
			foreach ($rows as $row) {
				$id = (int)$row->replyto;
				if ($id == 0) { continue; } //just in case
				if (!isset($threads[$id])) { continue; }
				if ($row->created > $threads[$id]->created) { $threads[$id] = $row; }
			}
		}

		if (!$threads) { return array(); }

		$this->total_threads = count($threads);

		//re-order threads based on latest reply date
		if ($this->total_threads > 1) {
			uasort($threads, array($this, 'orderThreadsByDate'));
		}

		if ($limit > 0) {
			$newthreads = array();
			$i = 0;
			$end = $limitstart + $limit;
			foreach($threads as $id => $thread) {
				if ($i < $limitstart) { continue; }
				if ($i >= $end) { break; }
				$newthreads[$id] = $thread;
				$i++;
			}
			$threads = $newthreads;
			unset($newthreads);

			if (!$threads) { return array(); }
		}

		$uids = array();
		foreach ($threads as $thread) {
			if ($thread->fromid > 0) { $uids[] = $thread->fromid; }
			if ($thread->toid > 0) { $uids[] = $thread->toid; }
		}

		$users = array();
		if ($uids) {
			$uids = array_unique($uids);
			$sql = "SELECT u.uid, u.uname, u.avatar,u.email, s.last_activity FROM ".$db->quoteId('#__users')." u"
			."\n LEFT JOIN ".$db->quoteId('#__session')." s ON s.uid = u.uid";
			if (count($uids) == 1) {
				$sql .= "\n WHERE u.uid = ".$uids[0];
			} else {
				$sql .= "\n WHERE u.uid IN (".implode(', ', $uids).")";
			}
			$sql .= "\n GROUP BY u.uid";
			$stmt = $db->prepare($sql);
			$stmt->execute();
			$users = $stmt->fetchAllAssoc('uid', PDO::FETCH_ASSOC);
			if (!$users) { $users = array(); }
		}

		foreach ($threads as $id => $thread) {
			$toid = $thread->toid;
			$fromid = $thread->fromid;
			$threads[$id]->from_uname = '';
			$threads[$id]->from_avatar = '';
			$threads[$id]->from_email = '';
			$threads[$id]->from_last_activity = 0;
			$threads[$id]->to_uname = '';
			$threads[$id]->to_avatar = '';
			$threads[$id]->to_email = '';
			$threads[$id]->to_last_activity = 0;
			if ($fromid > 0) {
				if (isset($users[$fromid])) {
					$threads[$id]->from_uname = $users[$fromid]['uname'];
					$threads[$id]->from_avatar = $users[$fromid]['avatar'];
					$threads[$id]->from_email = $users[$fromid]['email'];
					$threads[$id]->from_last_activity = (int)$users[$fromid]['last_activity'];
				}
			}
			if ($toid > 0) {
				if (isset($users[$toid])) {
					$threads[$id]->to_uname = $users[$toid]['uname'];
					$threads[$id]->to_avatar = $users[$toid]['avatar'];
					$threads[$id]->to_email = $users[$toid]['email'];
					$threads[$id]->to_last_activity = (int)$users[$toid]['last_activity'];
				}
			}

		}

		if ($convertlinks) {
			$elxis = eFactory::getElxis();

			foreach ($threads as $id => $thread) {
				//convert URLs to links
				$threads[$id]->message = preg_replace("~[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]~", "<a href=\"\\0\" target=\"_blank\">\\0</a>", $thread->message);

				//convert Elxis URIs to links
				preg_match_all('/(#elink:[a-z0-9\:\.\_\-\/]+)/i', $thread->message, $matches, PREG_PATTERN_ORDER);
				if ($matches) {
					foreach ($matches[0] as $k => $match) {
						$elxis_uri = preg_replace('/^(\#elink\:)/i', '', $match);
						$url = $elxis->makeURL($elxis_uri);
						$parts = preg_split('@\/@', $url, -1, PREG_SPLIT_NO_EMPTY);
						$c = count($parts) - 1;
						$href = '<a href="'.$url.'" target="_blank">'.$parts[$c].'</a>';
						$threads[$id]->message = str_replace($matches[0][$k], $href, $threads[$id]->message);
					}
				}
			}
		}

		return $threads;
	}


	/**************/
	/* GET THREAD */
	/**************/
	public function getThread($threadid) {
		$elxis = eFactory::getElxis();
		$db = eFactory::getDB();

		$sql = "SELECT * FROM ".$db->quoteId('#__messages')." WHERE ".$db->quoteId('id')." = :xid OR ".$db->quoteId('replyto')." = :xid"
		."\n ORDER BY ".$db->quoteId('created')." ASC";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':xid', $threadid, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
		if (!$rows) { return array(); }

		$uids = array();
		foreach ($rows as $row) {
			if ($row->fromid > 0) { $uids[] = $row->fromid; }
			if ($row->toid > 0) { $uids[] = $row->toid; }
		}

		$users = array();
		if ($uids) {
			$uids = array_unique($uids);
			$sql = "SELECT u.uid, u.uname, u.avatar,u.email, s.last_activity FROM ".$db->quoteId('#__users')." u"
			."\n LEFT JOIN ".$db->quoteId('#__session')." s ON s.uid = u.uid";
			if (count($uids) == 1) {
				$sql .= "\n WHERE u.uid = ".$uids[0];
			} else {
				$sql .= "\n WHERE u.uid IN (".implode(', ', $uids).")";
			}
			$sql .= "\n GROUP BY u.uid";
			$stmt = $db->prepare($sql);
			$stmt->execute();
			$users = $stmt->fetchAllAssoc('uid', PDO::FETCH_ASSOC);
			if (!$users) { $users = array(); }
		}

		foreach ($rows as $k => $row) {
			$toid = $row->toid;
			$fromid = $row->fromid;
			$rows[$k]->from_uname = '';
			$rows[$k]->from_avatar = '';
			$rows[$k]->from_email = '';
			$rows[$k]->from_last_activity = 0;
			$rows[$k]->to_uname = '';
			$rows[$k]->to_avatar = '';
			$rows[$k]->to_email = '';
			$rows[$k]->to_last_activity = 0;
			if ($fromid > 0) {
				if (isset($users[$fromid])) {
					$rows[$k]->from_uname = $users[$fromid]['uname'];
					$rows[$k]->from_avatar = $users[$fromid]['avatar'];
					$rows[$k]->from_email = $users[$fromid]['email'];
					$rows[$k]->from_last_activity = (int)$users[$fromid]['last_activity'];
				}
			}
			if ($toid > 0) {
				if (isset($users[$toid])) {
					$rows[$k]->to_uname = $users[$toid]['uname'];
					$rows[$k]->to_avatar = $users[$toid]['avatar'];
					$rows[$k]->to_email = $users[$toid]['email'];
					$rows[$k]->to_last_activity = (int)$users[$toid]['last_activity'];
				}
			}

			//convert URLs to links
			$rows[$k]->message = preg_replace("~[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]~", "<a href=\"\\0\" target=\"_blank\">\\0</a>", $row->message);
			//convert Elxis URIs to links
			preg_match_all('/(#elink:[a-z0-9\:\.\_\-\/]+)/i', $row->message, $matches, PREG_PATTERN_ORDER);
			if ($matches) {
				foreach ($matches[0] as $k => $match) {
					$elxis_uri = preg_replace('/^(\#elink\:)/i', '', $match);
					$url = $elxis->makeURL($elxis_uri);
					$parts = preg_split('@\/@', $url, -1, PREG_SPLIT_NO_EMPTY);
					$c = count($parts) - 1;
					$href = '<a href="'.$url.'" target="_blank">'.$parts[$c].'</a>';
					$rows[$k]->message = str_replace($matches[0][$k], $href, $rows[$k]->message);
				}
			}
		}

		return $rows;
	}


	/***********************/
	/* NEWER THREADS FIRST */
	/***********************/
	private function orderThreadsByDate($a, $b) {
		if ($a->created == $b->created) { return 0; }
		return ($a->created < $b->created) ? 1 : -1;
	}


	/*******************************************************/
	/* GET TOTAL THREADS AS COUNTED IN METHOD "getThreads" */
	/*******************************************************/
	public function getTotalThreads() {
		return $this->total_threads;
	}


	/*******************************/
	/* COUNT USER'S TOTAL MESSAGES */
	/*******************************/
	public function getTotalMessages($uid) {
		$db = eFactory::getDB();

		$sql = "SELECT COUNT(".$db->quoteId('id').") FROM ".$db->quoteId('#__messages')
		."\n WHERE (".$db->quoteId('fromid')." = :xuid AND ".$db->quoteId('delbyfrom')." = 0) OR (".$db->quoteId('toid')." = :xuid AND ".$db->quoteId('delbyto')." = 0)";
		$stmt = $db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':xuid', $uid, PDO::PARAM_INT);
		$stmt->execute();
		$num = (int)$stmt->fetchResult();

		return $num;
	}


	/**************************/
	/* DELETE MESSAGES THREAD */
	/**************************/
	public function deleteThread($threadid, $is_sender) {
		$db = eFactory::getDB();

		$threadid = (int)$threadid;
		if ($threadid < 1) { return; }

		if ($is_sender == 1) {
			$sql = "UPDATE ".$db->quoteId('#__messages')." SET ".$db->quoteId('delbyfrom')." = 1"
			."\n WHERE (".$db->quoteId('id')." = :xid AND ".$db->quoteId('replyto')." = 0) OR (".$db->quoteId('replyto')." = :xid)";
		} else if ($is_sender == 0) {
			$sql = "UPDATE ".$db->quoteId('#__messages')." SET ".$db->quoteId('read')." = 1, ".$db->quoteId('delbyto')." = 1"
			."\n WHERE (".$db->quoteId('id')." = :xid AND ".$db->quoteId('replyto')." = 0) OR (".$db->quoteId('replyto')." = :xid)";
		} else {
			return;
		}
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':xid', $threadid, PDO::PARAM_INT);
		$stmt->execute();

		$this->cleanUP();
	}


	/****************************/
	/* CLEANUP DELETED MESSAGES */
	/****************************/
	public function cleanUP() {
		$db = eFactory::getDB();

		$sql = "DELETE FROM ".$db->quoteId('#__messages')." WHERE ".$db->quoteId('delbyfrom')." = 1 AND ".$db->quoteId('delbyto')." = 1";
		$stmt = $db->prepare($sql);
		$stmt->execute();

		$sql = "DELETE FROM ".$db->quoteId('#__messages')." WHERE ".$db->quoteId('delbyto')." = 1 AND ".$db->quoteId('fromid')." = 0";
		$stmt = $db->prepare($sql);
		$stmt->execute();

		$sql = "DELETE FROM ".$db->quoteId('#__messages')." WHERE ".$db->quoteId('delbyfrom')." = 1 AND ".$db->quoteId('toid')." = 0";
		$stmt = $db->prepare($sql);
		$stmt->execute();
	}


	/************************/
	/* COUNT USER'S THREADS */
	/************************/
	public function countTotalThreads($uid) {
		$db = eFactory::getDB();

		$sql = "SELECT COUNT(id) FROM ".$db->quoteId('#__messages')
		."\n WHERE (".$db->quoteId('fromid')." = :xuid OR ".$db->quoteId('toid')." = :xuid) AND ".$db->quoteId('replyto')." = 0"; 
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':xuid', $uid, PDO::PARAM_INT);
		$stmt->execute();
		$total_threads = (int)$stmt->fetchResult();
		$total_threads = $stmt->fetchResult();
		return $total_threads;
	}


	/*********************************/
	/* MARK THREAD AS READ OR UNREAD */
	/*********************************/
	public function markThreadRead($threadid, $uid, $read=1) {
		$db = eFactory::getDB();

		$read = (int)$read;
		$sql = "UPDATE ".$db->quoteId('#__messages')." SET ".$db->quoteId('read')." = :xread"
		."\n WHERE (".$db->quoteId('id')." = :xid OR ".$db->quoteId('replyto')." = :xid) AND ".$db->quoteId('toid')." = :xuid";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':xread', $read, PDO::PARAM_INT);
		$stmt->bindParam(':xid', $threadid, PDO::PARAM_INT);
		$stmt->bindParam(':xuid', $uid, PDO::PARAM_INT);
		$stmt->execute();
	}

}

?>