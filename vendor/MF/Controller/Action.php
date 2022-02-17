<?php

namespace MF\Controller;

abstract class Action {

	protected $view;

	public function __construct() {
		$this->view = new \stdClass();
	}

	protected function render($view, $layout = 'layout') {
		$this->view->page = $view;

		//Se existir layout
		if(file_exists("../App/Views/".$layout.".phtml")) {
			//Renderiza o layout
			require_once "../App/Views/".$layout.".phtml";
		} else {
			//Se não, renderiza so a página
			$this->content();
		}
	}

	protected function content() {
		//Recupera todo o caminho da classe
		$classAtual = get_class($this); //Exemplo: App\\Controllers\\IndexController;

		//Retira o 'App\\Controllers\\'
		$classAtual = str_replace('App\\Controllers\\', '', $classAtual); //Exemplo: IndexController;

		//Retira o 'Controller' e coloca oq sobrou em minusculo
		$classAtual = strtolower(str_replace('Controller', '', $classAtual)); //Exemplo: index;

		//Acesso a view adequada
		require_once "../App/Views/".$classAtual."/".$this->view->page.".phtml"; //Exemplo: /App/Views/index/view.phtml;
	}
}

?>
