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

    }
}