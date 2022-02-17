<?php

namespace App\Models;

use MF\Model\Model;

class Tweet extends Model {

	private $id;
	private $idUsuario;
	private $tweet;
	private $data;

	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}

	//Salvar no banco de dados
	public function salvar() {

		$query = "INSERT INTO tweets(id_usuario, tweet) VALUES (:idUsuario, :tweet)";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':idUsuario', $this->__get('idUsuario'));
		$stmt->bindValue(':tweet', $this->__get('tweet'));
		$stmt->execute();

	}

	//Recuperar tweets
	public function getAll() {
		$query =
		"	SELECT t.id, t.id_usuario, u.nome, t.tweet, DATE_FORMAT(t.data, '%d/%m/%Y %H:%i') as data FROM tweets AS t
			LEFT JOIN usuarios AS u On (t.id_usuario = u.id)
			WHERE id_usuario = :id_usuario
			OR t.id_usuario IN (SELECT id_usuario_seguido FROM usuarios_seguidores WHERE id_usuario = :id_usuario)
			ORDER BY t.data desc
		";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $this->__get('idUsuario'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	//Remover do banco
	public function remover() {

		$query = "DELETE FROM tweets WHERE id = :id";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id', $this->__get('id'));
		$stmt->execute();

	}

}

?>
