<?php

class AdminController {
    private $newToAdd;          // поле для сохранения новой новости, чтобы эту готовую новость отправить в БД
    private $className = 'News';

    public function actionAdd() {           // создаёт новый объект новости и передаёт его в функцию для сохранения в БД
        $this->buildNew();
        $this->newToAdd->save();
        $this->actionViewForm();
    }

    public function actionViewForm() {      // отображает страничку, в качестве контента определяет форму
        $view = new View('/editform.html');
        $view->render();
        $view->display();
    }

    public function actionViewLog() {
        $view = new View('/log.html');
        $view->render();
        $view->display();
    }

    /*
     * Обработчик данных из post, заполняет поля новой новости, поля генерируются динамически
     * по полям соответствующего класса, названия заполняются из массива post.
     * Поля класса, столбцы таблицы в БД и имена формы должны соответствовать.
     * Здесь также пользуемся интерфейсом массива у модели.
     */
    private function buildNew() {
        $this->newToAdd = new $this->className();
        foreach ($this->newToAdd as $field => $value) {
            if (!empty($_POST[$field])) {
                $this->newToAdd->$field = $_POST[$field];
            }
        }
    }
}