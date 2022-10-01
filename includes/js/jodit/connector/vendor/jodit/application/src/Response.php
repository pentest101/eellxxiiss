<?php 
namespace Jodit;

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


/**
 * Class Response
 * @package jodit
 */
class Response {
	public $success = true;
	public $time;

	public $data = [
		'messages' => [],
		'code' => 220,
	];

	function __construct() {
		$this->time = date('Y-m-d H:i:s');
		$this->data = (object)$this->data;
	}
}