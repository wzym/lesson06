<?php

abstract class AbstractArticle
        implements ArrayAccess {

    static protected $table;

    public static function findAll() {
        $sql = 'SELECT * FROM ' . static::$table . ' ORDER BY date DESC';
        return static::commonRequest($sql, true);
    }

    public static function findByColumn($column, $value) {
        $sql = 'SELECT * FROM ' . static::$table . ' WHERE ' . $column . '=:' . $column;
        return static::commonRequest($sql, true, [':' . $column => $value])[0];
    }

    public function delete($column, $value) {
        $sql = 'DELETE FROM ' . static::$table . ' WHERE ' . $column . '=:' . $column;
        return static::commonRequest($sql, false, [':' . $column => $value]);
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

        $db = new DB();         // создаём вручную, чтобы получить из этой функции id через доступ к DB
        $db->query($sql, $params, false);
        $this->id = $db->lastInsertId();
    }

    public function update() {
        $params[':id'] = $this->id;            // сохраняем в параметры сразу известные данные, по которым ищем
        $str = [];
        foreach ($this as $field => $value) {       // перебираем как массив объект, формируем строку и массив параметров
            if (!empty($value)) {
                $params[':' . $field] = $value;
                $str[] = $field . '=:' . $field;
            }
        }
        $str = implode(', ', $str);
        $sql = '
                UPDATE ' . static::$table . ' SET ' .
                $str .
                ' WHERE id=:id';
        echo $sql . '<br />';
        static::commonRequest($sql, false, $params);
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
        return static::commonRequest($sql, true, $params);
    }

    private static function commonRequest($sql, $isReturnable, $params = []) {
        $db = new DB();
        if (true === $isReturnable) {
            $db->setClassName(get_called_class());
        }
        $res = $db->query($sql, $params, $isReturnable);
        if (!empty($res)) {
            return $res;
        }
        return false;
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