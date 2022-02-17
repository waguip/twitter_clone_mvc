<?php

namespace App\Models;

use MF\Model\Model;

class Usuario extends Model {

	private $id;
	private $nome;
	private $email;
	private $senha;

	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}

	//Salvar dados no banco
	public function salvar() {
		$query = "INSERT INTO usuarios(nome, email, senha) VALUES(:nome, :email, :senha)";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':nome', $this->__get('nome'));
		$stmt->bindValue(':email', $this->__get('email'));
		$stmt->bindValue(':senha', md5($this->__get('senha')));  //md5() -> hash de 32 caracteres
		$stmt->execute();

		return $this;
	}
	//Validar cadastro
	public function validarCadastro() {
		$erros = array();

		//Confere se nome tem mais que 3 letras e apenas letras e espaços em branco
	    if (strlen($this->__get('nome')) < 3) {
			$erros['nome'] = '*Nome precisa ter mais que 3 letras';
		} else if (!preg_match("/^[a-zA-Z-' ]*$/", $this->__get('nome'))) {
			$erros['nome'] = '*Apenas letras e espaços em branco são permitidos';
		}

		//Confere se email se encaixa no filtro
		if (!filter_var($this->__get('email'), FILTER_VALIDATE_EMAIL)) {
			$erros['email'] = '*Email inválido';
		}

		//Confere se senha tem mais de 8 caracteres
		if (strlen($this->__get('senha')) < 8) {
			$erros['senha'] = '*Senha precisa ter no mínimo 8 letras';
		}

		return $erros;
	}

	//Recuperar usuário por email
	public function getUsuarioPorEmail() {
		$query = "SELECT nome, email FROM usuarios WHERE email=:email";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':email', $this->__get('email'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	//Autenticação de usuario
	public function autenticar() {
		$query = "SELECT id, nome, email FROM usuarios WHERE email=:email and senha=:senha";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':email', $this->__get('email'));
		$stmt->bindValue(':senha', $this->__get('senha'));
		$stmt->execute();

		$usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

		if(!empty($usuario['id']) && !empty($usuario['nome'])) {
			$this->__set('id', $usuario['id']);
			$this->__set('nome', $usuario['nome']);
		}

		return $this;
	}

	//Recuperar usuarios de acordo com termo de pesquisa
	public function getAll() {

		//Apresenta usuarios que tenham o termo e que não sejam o usuario da sessao
		$query =
			"SELECT u.id, u.nome, u.email,
			(SELECT COUNT(*) FROM usuarios_seguidores AS us WHERE us.id_usuario=:id AND id_usuario_seguido=u.id) AS seguindo
			FROM usuarios AS u WHERE u.nome LIKE :nome AND u.id != :id
			";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':nome', '%'.$this->__get('nome').'%');
		$stmt->bindValue(':id', $this->__get('id'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);

	}

	//Seguir um usuário
	public function seguirUsuario($id_usuario_seguido) {

		$query = "INSERT INTO usuarios_seguidores(id_usuario, id_usuario_seguido) VALUES (:id_usuario, :id_usuario_seguido)";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $this->__get('id'));
		$stmt->bindValue(':id_usuario_seguido', $id_usuario_seguido);
		$stmt->execute();

	}

	//Deixar de seguir um usuário
	public function deixarSeguirUsuario($id_usuario_seguido) {

		$query = "DELETE FROM usuarios_seguidores WHERE id_usuario=:id_usuario AND id_usuario_seguido=:id_usuario_seguido";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $this->__get('id'));
		$stmt->bindValue(':id_usuario_seguido', $id_usuario_seguido);
		$stmt->execute();

	}

	//Recuperar informações do usuário
	public function getInfo() {
		//Contagem de tweets
		$query = "SELECT COUNT(*) as contagem FROM tweets WHERE id_usuario = :id_usuario";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $_SESSION['id']);
		$stmt->execute();
		$contagemTweets = $stmt->fetch(\PDO::FETCH_ASSOC);

		//Contagem de seguindo
		$query = "SELECT COUNT(*) as contagem FROM usuarios_seguidores WHERE id_usuario = :id_usuario";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $_SESSION['id']);
		$stmt->execute();
		$contagemSeguindo = $stmt->fetch(\PDO::FETCH_ASSOC);

		//Contagem de seguidores
		$query = "SELECT COUNT(*) as contagem FROM usuarios_seguidores WHERE id_usuario_seguido = :id_usuario";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $_SESSION['id']);
		$stmt->execute();
		$contagemSeguidores = $stmt->fetch(\PDO::FETCH_ASSOC);

		return array(
			"tweets" => $contagemTweets['contagem'],
			"seguindo" => $contagemSeguindo['contagem'],
			"seguidores" => $contagemSeguidores['contagem']
		);
	}

}

?>
