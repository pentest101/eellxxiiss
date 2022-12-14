<?php 
/**
* @version		$Id: mysql.importer.php 2386 2021-01-30 08:16:22Z IOS $
* @package		Elxis
* @subpackage	Database
* @copyright	Copyright (c) 2006-2021 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/


class elxisMysqlImporter extends elxisDbImporter {

	private $link = null;
	private $replacePrefixes = false;


	/***********************/
	/* VALIDATE PARAMETERS */
	/***********************/
	protected function validate() {
		$req_params = array('db_host', 'db_user', 'db_pass', 'db_name', 'db_port');
		foreach ($req_params as $req_param) {
			if (!isset($this->params[$req_param])) {
				$this->error = true;
				$this->errormsg = 'Required parameter '.$req_param.' is not set!';
				return false;
			}
			if ($req_param == 'db_port') { $this->params['db_port'] = (int)$this->params['db_port']; continue; }
			if (trim($this->params[$req_param]) == '') {
				$this->error = true;
				$this->errormsg = 'Required parameter '.$req_param.' is empty!';
				return false;
			}		
		}
		return true;
	}


	/***********************/
	/* CONNECT TO DATABASE */
	/***********************/
	protected function connect() {
		if (is_resource($this->link)) { return true; }
		if (!function_exists('mysqli_connect')) {
			$this->error = true;
			$this->errormsg = 'This feature requires native mysqli driver with functions like mysqli_connect.';
			return false;
		}

		try {
			if ($this->params['db_port'] > 0) {
				$this->link = mysqli_connect($this->params['db_host'], $this->params['db_user'], $this->params['db_pass'], '', $this->params['db_port']);
			} else {
				$this->link = mysqli_connect($this->params['db_host'], $this->params['db_user'], $this->params['db_pass'], '');
			}
			if (!$this->link) {
				throw new Exception(mysqli_error());
			}
		} catch (Exception $e) {
			$this->error = true;
			$this->errormsg = $e->getMessage();
			return false;
		}

		if ($this->selectdb()) {
			$this->query('SET NAMES utf8');
			return true;
		} else {
			return false;
		}
	}


	/****************************/
	/* DISCONNECT FROM DATABASE */
	/****************************/
	public function disconnect() {
		if (is_resource($this->link)) {
			mysqli_close($this->link);
		}
	}


	/*******************/
	/* SELECT DATABASE */
	/*******************/
	private function selectdb() {
		if ($this->error) { return false; }
		try {
			if (!mysqli_select_db($this->link, $this->params['db_name'])) {
				throw new Exception(mysqli_error($this->link));
			}
		} catch (Exception $e) {
			$this->error = true;
			$this->errormsg = $e->getMessage();
			$this->disconnect();
			return false;
		}
		return true;
	}


	/**********************/
	/* IMPORT AN SQL FILE */
	/**********************/
	public function import() {
		if ($this->error) { return false; }
		if (!isset($this->params['file'])) {
			$this->errormsg = 'Parameter file was not set!';
			return false;
		}
		if (trim($this->params['file']) == '') {
			$this->errormsg = 'Parameter file is empty!';
			return false;
		}
		if (!file_exists($this->params['file'])) {
			$this->errormsg = 'Import SQL file does not exist!';
			return false;
		}
		if (strtolower(substr(strrchr($this->params['file'], '.'), 1)) != 'sql') {
			$this->errormsg = 'Import file is not an SQL file!';
			return false;
		}

		if (isset($this->params['db_prefix']) && isset($this->params['db_prefix_old']) && ($this->params['db_prefix']) != $this->params['db_prefix_old']) { 
			$this->replacePrefixes = true;
		}

		$templine = '';
		$lines = file($this->params['file']);
		if ($lines) {
			foreach ($lines as $line) {
				$trimmed_line = trim($line);
				if (($trimmed_line == '') || (substr($trimmed_line, 0, 2) == '--')) { continue; }
				$templine .= $line;
				if (substr($trimmed_line, -1, 1) == ';') {
					if ($this->replacePrefixes) {
						$templine = $this->replaceTblPrefix($templine, $this->params['db_prefix_old'], $this->params['db_prefix']);
					}
					try {
						if (!mysqli_query($this->link, $templine)) {
							throw new Exception(mysqli_error($this->link));
						}
					} catch (Exception $e) {
						$this->error = true;
						$this->errormsg = $e->getMessage();
						$this->disconnect();
						return false;
					}
					$this->queries++;
					$templine = '';
				}
			}
		}
		return true;
	}


	/**************************/
	/* REPLACE TABLE PREFIXES */
	/**************************/
	private function replaceTblPrefix($sql, $oldprf, $newprf) {
		if ($oldprf == $newprf) { return $sql; }
		$sql = str_replace('`'.$oldprf, '`'.$newprf, $sql);
		$sql = str_replace('DROP TABLE '.$oldprf, 'DROP TABLE '.$newprf, $sql);
		$sql = str_replace('DROP TABLE IF EXISTS '.$oldprf, 'DROP TABLE IF EXISTS '.$newprf, $sql);
		$sql = str_replace('CREATE TABLE '.$oldprf, 'CREATE TABLE '.$newprf, $sql);
		$sql = str_replace('INSERT INTO '.$oldprf, 'INSERT INTO '.$newprf, $sql);
		return $sql;
	}


	/************************/
	/* EXECUTE AN SQL QUERY */
	/************************/
	public function query($sql) {
		if ($this->error) { return false; }
		if (!$this->link) {
			$this->errormsg = 'Database connection is closed!';
			return false;
		}
		if (trim($sql) == '') {
			$this->errormsg = 'The SQL query is empty!';
			return false;
		}

		try {
			if (!mysqli_query($this->link, $sql)) {
				throw new Exception(mysqli_error($this->link));
			}
		} catch (Exception $e) {
			$this->errormsg = $e->getMessage();
			return false;
		}
		return true;
	}

}

?>