<?php

class NewsController {

    public function actionShowAll() {
        $items = News::findAll();
        if (empty($items)) {
            $exc = new E404Exception('Новостей в базе не нашлось. Это, очевидно, неверно. Попробуйте попытаться позже');
            throw $exc;
        }
        $view = new View();
        $view->items = $items;
        $view->render();
        $view->display();
    }

    public function actionShowOne() {
        $item = News::findByColumn('id', $this->setId());
        if (empty($item)) {
            $exc = new E404Exception('Запрошенной новости не нашлось. Посмотрите другую.');
            throw $exc;
        }
        $view = new View('/onenew.html');
        $view->item = $item;
        $view->render();
        $view->display();
    }

    private function setId() {
        return (int) $_GET['id'];
    }

    public function actionShowError($errMes) {
        $view = new View('/exc1.html');
        $view->errMes = $errMes;
        $view->render();
        $view->display();
    }
}