<?php

namespace MF\Model;

use App\Connection;

class Container {

	//Retorna um modelo já com conexão ao banco de dados
	public static function getModel($model) {
		$class = "\\App\\Models\\".ucfirst($model);
		$conn = Connection::getDb();

		return new $class($conn);
	}
}


?>
