<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class IndexController extends Action {

	public function index() {

		$this->view->login = isset($_GET['login']) ? $_GET['login'] : '';

		session_start();

		$this->view->email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
		$this->view->senha = isset($_SESSION['senha']) ? $_SESSION['senha'] : '';

		unset($_SESSION['email']);
		unset($_SESSION['senha']);

		session_destroy();

		$this->render('index');
	}

	public function inscreverse() {
		$this->view->usuario = array(
			'nome' => '',
			'email' => '',
			'senha' => '',
		);
		$this->view->erroCadastro = false;

		$this->render('inscreverse');
	}

	public function registrar() {
		//Instancia do modelo Usuario
		$usuario = Container::getModel('Usuario');

		//Seta os dados recebidos do form
		$usuario->__set('nome', $_POST['nome']);
		$usuario->__set('email', $_POST['email']);
		$usuario->__set('senha', $_POST['senha']);

		//Testa se dados são validos e se email ainda não existe no banco
		$errosValidacao = $usuario->validarCadastro();

		if (empty($errosValidacao) && count($usuario->getUsuarioPorEmail()) == 0) {

			//Salva os dados no banco
			$usuario->salvar();
			//Renderiza a tela de sucesso
			$this->render('cadastro');

		} else {

			//Volta para a tela com o mesmos dados digitados
			$this->view->usuario = array(
				'nome' => $_POST['nome'],
				'email' => $_POST['email'],
				'senha' => $_POST['senha'],
			);

			//Passa o array de erros
			$this->view->erroCadastro = $errosValidacao;

			$this->render('inscreverse');
		}
	}

}


?>
