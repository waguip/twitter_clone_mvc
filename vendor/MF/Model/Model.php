<?php

namespace MF\Model;

abstract class Model {

	protected $db;

	//Construtor dos modelos (Comum a todos)
	public function __construct(\PDO $db) {
		$this->db = $db;
	}
}


?>
