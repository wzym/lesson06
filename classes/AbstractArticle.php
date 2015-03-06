<?php

abstract class AbstractArticle
        implements ArrayAccess {

    static protected $table;

    public static function findAll() {
        $class = get_called_class();
        $sql = 'SELECT * FROM ' . static::$table . ' ORDER BY date DESC';
        $db = new DB();
        $db->setClassName($class);
        return $db->query($sql);
    }

    public static function findByColumn($column, $value) {
        $class = get_called_class();
        $sql = 'SELECT * FROM ' . static::$table . ' WHERE ' . $column . '=:' . $column;
        $db = new DB();
        $db->setClassName($class);
        return $db->query($sql, [':' . $column => $value])[0];
    }

    public static function delete($column, $value) {
        $sql = 'DELETE FROM ' . static::$table . ' WHERE ' . $column . '=:' . $column;
        $db = new DB();
        $db->query($sql, [':' . $column => $value]);
    }

    /*
     * В методах добавления и редактирования модели хотел сохранить у объектов их поля,
     * поэтому использовал функцию get_object_vars(). Чтобы вынести её за скобки,
     * прикрутил к модели интерфейс ArrayAccess.
     */
    private function addToDB() {
        $params = [];
        foreach ($this as $field => $value) {        // пробегаем по объекту как по массиву
            if (!empty($value)) {                    // если в поле есть запись,
               $params[':' . $field] = $value;       // включаем эти данные в массив для подстановки
            }
        }
        $strWithColon =  implode(', ', array_keys($params));    // из готового массива делаем строку для подстановки
        $strWithoutColon = str_replace(':', '', $strWithColon); // то же, без двоеточий

        $sql = '
          INSERT INTO ' . static::$table . '
          (' . $strWithoutColon . ')
          VALUES
          (' . $strWithColon . ')
          ';

        $db = new DB();

        /*
         * При успешном добавлении ищем id по новости, которую ищем
         * по первому из полей, что были добавлены, так как при поиске по
         * максимальному id может выдать не ту новость.
         * Логика: возвращаем итератор рабочего массива (params[]) на первый элемент,
         * получаем поле (ключ без двоеточия), подставляем это поле и его значение.
         */
        if ($db->execute($sql, $params)) {
            reset($params);
            $column = str_replace(':', '', key($params));
            $new = static::findByColumn($column, $this->$column);
            $this->id = $new->id;
        }
    }

    private function update($column, $value) {
        $params[':' . $column] = $value;            // сохраняем в параметры сразу известные данные, по которым ищем
        $str = '';
        foreach ($this as $field => $value) {       // перебираем как массив объект, формируем строку и массив параметров
            if (!empty($value)) {
                $params[':' . $field] = $value;
                $str .= $field . '=:' . $field . ', ';
            }
        }
        $str = substr($str, 0, -2);         // удаляем последнюю запятую
        $sql = '
                UPDATE ' . static::$table . ' SET ' .
                $str .
                ' WHERE ' .
                $column . '=:' . $column;
        $db = new DB();
        $db->execute($sql, $params);
    }

    /*
     * Пробегает по БД по заполненным в форме параметрам (ищет соответствие ПО ВСЕМ ЗАПОЛНЕННЫМ).
     * Если совпадение не найдено - сохраняет как новую запись.
     * Если найдено - то пока не ясна логика. Нам не известно, какой именно класс модели актуален.
     * Следовательно, не известно, по каким полям искать новость для редактирования.
     * Наш поиск сейчас ищет по всем данным параметрам. Как определимся со всеми моделями-наследниками,
     * станет ясна структура замены.
     */
    public function save() {
        if (!empty($this->commonSearch())) {
            $this->update('id', 37);        // для теста меняем 37-ю запись
        } else {
            $this->addToDB();
        }
    }

    /*
     * Работает с текущим объектом модели, из которого вызван: собирает все заполненные поля
     * и ищет в БД соответствующую запись. Возвращает массив объектов-моделей, соответствующих
     * запрошенным данным.
     */
    private function commonSearch() {
        $class = get_called_class();
        $params = [];
        $str = '';
        foreach ($this as $field => $value) {
            if (!empty($value)) {
                $params[':' . $field] = $value;
                $str .= $field . '=:' . $field . ' AND ';
            }
        }
        $str = substr($str, 0, -4);         // удаляем последний AND
        $sql = '
                SELECT * FROM ' . static::$table . '
                WHERE ' . $str
        ;
        $db = new DB();
        $db->setClassName($class);
        return $db->query($sql, $params);
    }

    public function offsetExists($offset) {
        $vars = get_object_vars($this);
        return array_key_exists($offset, $vars);
    }

    public function offsetGet($offset) {
        return $this->$offset;
    }

    public function offsetSet($offset, $value) {
        $this->$offset = $value;
    }

    public function offsetUnset($offset) {
        $this->$offset = null;
    }
}