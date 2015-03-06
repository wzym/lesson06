<?php

class NewsController {

    public function actionShowAll() {
        $items = News::findAll();
        $view = new View();
        $view->items = $items;
        $view->render();
        $view->display();
    }

    public function actionShowOne() {
        $item = News::findByColumn('id', $this->setId());
        $view = new View('/onenew.html');
        $view->item = $item;
        $view->render();
        $view->display();
    }

    private function setId() {
        return (int) $_GET['id'];
    }
}