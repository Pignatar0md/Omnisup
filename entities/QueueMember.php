<?php

class QueueMember {

    private $exten;
    private $name;
    private $callsTaken;
    private $status;

    function __construct() {
        $this->exten = 0;
        $this->name = "";
        $this->callsTaken = 0;
        $this->status = "";
    }
    
    function getExten() {
        return $this->exten;
    }

    function getName() {
        return $this->name;
    }

    function getCallsTaken() {
        return $this->callsTaken;
    }

    function getStatus() {
        return $this->status;
    }

    function setExten($exten) {
        $this->exten = $exten;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setCallsTaken($callsTaken) {
        $this->callsTaken = $callsTaken;
    }

    function setStatus($status) {
        $this->status = $status;
    }

}
