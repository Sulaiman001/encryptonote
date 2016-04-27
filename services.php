<?php

require_once("config.php");
require_once("lib/Notes.php");

date_default_timezone_set($cfg['timezone']);

$loader = require_once("vendor/autoload.php");

use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;

use Documents;
use Documents\Note;

$loader->add("Documents", __DIR__);

$mongo = new Mongo($cfg['mongoHost']);
$connection = new Connection($mongo);

$config = new Configuration();
$config->setProxyDir(__DIR__ . "/Proxies");
$config->setProxyNamespace("Proxies");
$config->setHydratorDir(__DIR__ . "/Hydrators");
$config->setHydratorNamespace("Hydrators");
$config->setDefaultDB($cfg['mongoDatabase']);
$config->setMetadataDriverImpl(AnnotationDriver::create(__DIR__ . "/Documents"));

AnnotationDriver::registerAnnotationClasses();

$dm = DocumentManager::create($connection, $config);

$notes = new Notes($dm, $cfg);

define('action', $notes->isVarSet($_GET['a']) ? $_GET['a'] : ($notes->isVarSet($_POST['a']) ? $_POST['a'] : false));
define('secret', $notes->isVarSet($_GET['s']) ? $_GET['s'] : ($notes->isVarSet($_POST['s']) ? $_POST['s'] : false));
define('noteId', $notes->isVarSet($_GET['n']) ? $_GET['n'] : ($notes->isVarSet($_POST['n']) ? $_POST['n'] : false));
define('text', $notes->isVarSet($_GET['text']) ? $_GET['text'] : ($notes->isVarSet($_POST['text']) ? $_POST['text'] : false));
define('author', $notes->validate(secret));

if (author) {

    // A note is requested.
    if (action === "load-note" && noteId) {

        $note = $notes->getNote(noteId);
        if (is_null($note)) {
            $note = new Note();
            $note->setId(noteId);
            $note->setText($notes->encrypt("Lorem ipsum.", secret));
            $note->setAuthor(author);
            $note->setCreated(new DateTime());
            $note->setModified(new DateTime());
            $dm->persist($note);
            $dm->flush();
        }
        if ($notes->validateAuthor(secret, noteId)) {
            // This overrides the encrypted text with the decrypted text so that
            // we can serialize to JSON. Possibly Note->jsonSerialize() could handle the decryption.
            $note->setText($notes->decrypt($note->getText(), secret));
            die(json_encode($note));
        } else {
            die(json_encode(array("status" => "error", "message" => "Access denied")));
        }

    // Load a list of all notes for the current author.
    } elseif (action === "list-notes") {

        $notes = $notes->listNoteIds(author);
        print(json_encode($notes));

    // Saving a note.
    } else if (action === "save-note" && noteId 
            && $notes->isVarSet(text) && $notes->validateAuthor(secret, noteId)) {

        try {
            $note = $notes->getNote(noteId);
            $noteBefore = "";
            $saveBackup = false;
            if (is_null($note)) {
                $saveBackup = true;
                // This note is new, just persist the current text.
                $noteBefore = text;
                $note = new Note();
                $note->setId(noteId);
                $note->setText($notes->encrypt(text, secret));
                $note->setAuthor(author);
                $note->setCreated(new DateTime());
                $note->setModified(new DateTime());
            } else {
                // This note is being updated, get the previous text before setting the new text.
                $noteBefore = $notes->decrypt($note->getText(), secret);
                if ($noteBefore !== text) {
                    $saveBackup = true;
                }
                $note->setText($notes->encrypt(text, secret));
                $note->setModified(new DateTime());
            }
            $dm->persist($note);
            $dm->flush();

            // TODO: Create and store a delta/diff
            if ($saveBackup) {
                $savePath = ".deltas/" . date("Y") . "/" . date("m") . "/" . date("d") . "/" . date("H") . "/" . date("i") . "/" . date("s");
                $saveFile = str_replace(" ", "", microtime()) . "." . noteId;
                if (!file_exists($savePath)) {
                    mkdir($savePath, 0777, true);
                }
                file_put_contents("{$savePath}/{$saveFile}", $noteBefore);
            }

            die(json_encode(array("status" => "ok", "message" => "Note saved")));
        } catch (Exception $ex) {
            die(json_encode(array("status" => "error", "message" => "Note not saved: " . $ex->getMessage())));
        }

    }

} else {

    die(json_encode(array("status" => "error", "message" => "Not authenticated")));

}
