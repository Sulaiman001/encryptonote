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
        return $this->documentManager->getRepository("Documents\Note")->findBy(array("author" => $author));
    }

    public function listNoteIds($author) {
        $notes = $this->findAllNotes($author);
        if (is_null($notes)) {
            return [];
        } else {
            $ret = [];
            foreach ($notes as $note) {
                $ret[] = $note->getId();
            }
            return $ret;
        }
    }


    public static function isMobile() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        return preg_match("/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i", $userAgent) || preg_match("/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i", substr($userAgent,0,4));
    }

    public static function cleanForHtmlQuotes($arg) {
        return str_replace("\"", "\\\"", $arg);
    }

}
