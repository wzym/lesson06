<?php

class NewsController {

    public function actionShowAll() {
        $items = News::findAll();
        if (empty($items)) {
            $exc = new E404Exception();
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
            $exc = new E404Exception();
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

    public function actionShowError() {
        $view = new View('/exc1.html');
        $view->render();
        $view->display();
    }
}