<?php

namespace MF\Init;

abstract class Bootstrap {
	private $routes;

	abstract protected function initRoutes();

	public function __construct() {
		$this->initRoutes();
		$this->run($this->getUrl());
	}

	public function getRoutes() {
		return $this->routes;
	}

	public function setRoutes(array $routes) {
		$this->routes = $routes;
	}

	protected function run($url) {

		//Percorre o array de rotas
		foreach ($this->getRoutes() as $key => $route) {

			//Confere se o url acessado existe no array de rotas
			if($url == $route['route']) {

				//Pega o controller da rota.
				$class = "App\\Controllers\\".ucfirst($route['controller']);

				//Instancia o controller
				$controller = new $class;

				//Recupera a ação da rota
				$action = $route['action'];

				//Faz o controller executar a ação
				$controller->$action();

			}
		}

	}

	protected function getUrl() {
		return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	}
}

?>
