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
     */
    public function validate($secret) {
        return (isset($_GET['s']) && in_array(hash($this->cfg['hash'], $_GET['s']), $this->cfg['secrets']))
            || (isset($_POST['s']) && in_array(hash($this->cfg['hash'], $_POST['s']), $this->cfg['secrets']));
    }

    /**
     * Given a secret extract the author from the config.
     */
    public function getAuthor($secret) {
        $hash = hash($this->cfg['hash'], $secret);
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

}
