<?php
    class Core {
        private $currentController = 'Pages';
        private $currentMethod = 'index';
        private $params = [];

        public function __construct()
        {
            $url = $this->getUrl();
            
            //Ищет в контроллерах первое значение, ucwords делает заглавной первую букву
            if (isset($url[0]) && file_exists('../app/controllers/' . ucwords($url[0]) . '.php')) {
                //Устанавливает новый контроллер
                $this->currentController = ucwords($url[0]);
                unset($url[0]);
            }

            require_once '../app/controllers/' . $this->currentController . '.php';
            $this->currentController = new $this->currentController;

            //Check for second part of the URL
            if (isset($url[1])) {
                if (method_exists($this->currentController, $url[1])) {
                    $this->currentMethod = $url[1];
                    unset($url[1]);
                }
            }

            //Get parameters
            $this->params = $url ? array_values($url) : [];

            //Call a callback with array of params
            call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
        }

        private function getUrl() {
            if(isset($_GET['url'])) {
                $url = rtrim($_GET['url'], '/');
                $url = filter_var($url, FILTER_SANITIZE_URL);
                //Разделение на массив
                $url = explode('/', $url);
                return $url;
            }  
        }
    }