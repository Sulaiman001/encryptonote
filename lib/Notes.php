<?php

class Notes {

    private $documentManager;
    private $cfg;

    public function __construct($documentManager, $cfg) {
        $this->documentManager = $documentManager;
        $this->cfg = $cfg;
    }

    public function setDocumentManager($documentManager) {
        $this->documentManager = $documentManager;
    }

    public function getDocumentManager() {
        return $this->documentManager;
    }

    public function setCfg($cfg) {
        $this->cfg = $cfg;
    }

    public function getCfg() {
        return $this->cfg;
    }

    /**
     * Find a note.
     */
    public function getNote($noteId) {
        return $this->documentManager->find("Documents\Note", $noteId);
    }

    /**
     * Validates secret against configured hash.
     * @return Returns an '(string) author' or false.
     */
    public function validate($secret) {
        $hash = $this->isVarSet($secret) ? hash($this->cfg['hash'], $secret) : false;
        if (in_array($hash, $this->cfg['secrets'])) {
            return $this->getAuthorFromHash($hash);
        } else {
            return false;
        }
    }

    /**
     * Given a secret extract the author from the config.
     */
    public function getAuthor($secret) {
        $hash = hash($this->cfg['hash'], $secret);
        return $this->getAuthorFromHash($hash);
    }

    /**
     * Given a secret extract the author from the config.
     */
    public function getAuthorFromHash($hash) {
        return array_search($hash, $this->cfg['secrets']);
    }

    /**
     * Encrypt some data.
     */
    public function encrypt($data, $password) {
        return openssl_encrypt($data, $this->cfg['cipher'], $password, false, $this->cfg['salt']);
    }

    /**
     * Decrypt some data.
     */
    public function decrypt($data, $password) {
        return openssl_decrypt($data, $this->cfg['cipher'], $password, false, $this->cfg['salt']);
    }

    /**
     * Checks a documents author against the author associated with a secret.
     */
    public function validateAuthor($secret, $noteId) {
        $author = $this->getAuthor($secret);
        $note = $this->getNote($noteId);
        if (is_null($note)) {
            return false;
        } else {
            if ($author === $note->getAuthor()) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * A helper function for asserting two values.
     *
     * @param string $expected This is what you expect. e.g. get("load-note", $_GET['var'])
     * @param string $got This is the variable in question. e.g. $_GET['var']
     */
    public function get($expected, $got) {
        if ($this->isVarSet($expected) && $this->isVarSet($got)) {
            if ($expected === $got) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * A wrapper to check if a variable is set. Must be set, not null, and contain
     * more than whitespace.
     */
    public function isVarSet($var) {
        $var = trim($var);
        if (isset($var) && $var != null && $var !== "") {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns all notes for an author.
     */
    public function findAllNotes($author) {
        return $this->documentManager->getRepository("Note")->findBy(array("author" => $author));
    }

}
