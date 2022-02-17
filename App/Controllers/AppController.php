<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action {

	public function validaSessao() {
		session_start();
		if (!isset($_SESSION['id']) || empty($_SESSION['id']) || !isset($_SESSION['nome']) || empty($_SESSION['nome'])) {
			header('Location: /?login=erro');
		}
	}

	public function timeline() {

		$this->validaSessao();

		//Recuperar e renderizar tweets
		$tweet = Container::getModel('Tweet');
		$tweet->__set('idUsuario', $_SESSION['id']);
		$this->view->tweets = $tweet->getAll();

		//Recuperar info do usuario
		$usuario = Container::getModel('Usuario');
		$this->view->usuarioInfo = $usuario->getInfo();

		$this->render('timeline');

	}

	public function tweet() {

		$this->validaSessao();

		$tweet = Container::getModel('Tweet');
		$tweet->__set('tweet', $_POST['tweet']);
		$tweet->__set('idUsuario', $_SESSION['id']);
		$tweet->salvar();

		header('Location: /timeline');

	}

	public function quemSeguir() {

		$this->validaSessao();

		$usuarios = array();

		//Termo de pesquisa
		$termoPesquisa = isset(($_GET['termoPesquisa'])) ? $_GET['termoPesquisa'] : '';
		$this->view->termoPesquisa = $termoPesquisa;

		//Recuperar usuarios a partir do termo da pesquisa
		$usuario = Container::getModel('Usuario');
		$usuario->__set('nome', $termoPesquisa);
		$usuario->__set('id', $_SESSION['id']);
		$usuarios = $usuario->getAll();
		$this->view->usuarios = $usuarios;

		//Recuperar info do usuario
		$usuario = Container::getModel('Usuario');
		$this->view->usuarioInfo = $usuario->getInfo();

		$this->render('quemSeguir');

	}

	//Seguir ou deixar de seguir usuarios
	public function acao() {

		$this->validaSessao();

		$acao = isset($_GET['acao']) ? $_GET['acao'] : '';
		$id_usuario_seguido = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';
		$termoPesquisa = isset(($_GET['termoPesquisa'])) ? $_GET['termoPesquisa'] : '';

		$usuario = Container::getModel('Usuario');
		$usuario->__set('id', $_SESSION['id']);

		if ($acao == 'seguir') {
			$usuario->seguirUsuario($id_usuario_seguido);
		} else if ($acao == 'deixar_de_seguir') {
			$usuario->deixarSeguirUsuario($id_usuario_seguido);
		}

		header('Location: /quem_seguir?termoPesquisa='.$termoPesquisa);
	}

	public function removerTweet() {

		$this->validaSessao();

		$tweet = Container::getModel('Tweet');
		$tweet->__set('id', $_GET['id']);
		$tweet->remover();

		header('Location: /timeline');

	}

}

?>
