<?php

namespace Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use DateTime;
use JsonSerializable;

/** @ODM\Document */
class Note implements JsonSerializable {

    /** @ODM\Id(strategy="NONE", type="string") */
    private $id;

    /** @ODM\Field(type="string") */
    private $text;

    /** @ODM\Field(type="string") */
    private $author;

    /** @ODM\Field(type="date") */
    private $created;

    /** @ODM\Field(type="date") */
    private $modified;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getText() {
        return $this->text;
    }

    public function setText($text) {
        $this->text = $text;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function setAuthor($author) {
        $this->author = $author;
    }

    public function getCreated() {
        return $this->created;
    }

    public function setCreated(DateTime $created) {
        $this->created = $created;
    }

    public function getModified() {
        return $this->modified;
    }

    public function setModified(DateTime $modified) {
        $this->modified = $modified;
    }

    public function jsonSerialize() {
        return array(
            "id" => $this->id,
            "text" => $this->text,
            "author" => $this->author,
            "created" => $this->created,
            "modified" => $this->modified
        );
    }

}
