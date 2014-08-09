<?php
use Luracast\Restler\RestException;

/**
 *
 * @access protected
 */
class Assets {

	public $dp;

	function __construct() {
		$this->dp = new DB_PDO_Assets();
	}

	function index() {
		return $this->dp->getAll();
	}

	function get($id) {
		return $this->dp->get($id);
	}
}
