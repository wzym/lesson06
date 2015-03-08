<?php

class EventLog {
    private $filename;
    private $lineNum;
    private $time;
    private $message;

    public function __construct($filename, $lineNum, $time, $message) {
        $this->filename = $filename;
        $this->lineNum = $lineNum;
        $this->time = $time;
        $this->message = $message;
        $this->saveEvent();
    }

    private function saveEvent() {
        $handle = fopen(__DIR__ . '/../views/log.html', 'a');
        $stringToAdd = '<div>' .
                        'Время события: ' . $this->time . '.<br />' .
                        'Сообщение: ' . $this->message . '.<br />' .
                        'Файл с ошибкой скрипта: ' . $this->filename . '.<br />' .
                        'Строка ошибки:' . $this->lineNum . '.<br /><br />' .
                        '</div>
                        ';
        fwrite($handle, $stringToAdd);
        fclose($handle);
    }
}