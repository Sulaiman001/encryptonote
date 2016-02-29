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

if ($notes->validate($_GET['s'])) {

    // A note is requested.
    if (isset($_GET['n'])) {

        $note = $notes->getNote($_GET['n']);
        if (is_null($note)) {
            $note = new Note();
            $note->setId($_GET['n']);
            $note->setText($notes->encrypt("Lorem ipsum.", $_GET['s']));
            $note->setAuthor($notes->getAuthor($_GET['s']));
            $note->setCreated(new DateTime());
            $note->setModified(new DateTime());
            $dm->persist($note);
            $dm->flush();
        }
        if ($notes->validateAuthor($_GET['s'], $_GET['n'])) {
            // This overrides the encrypted text with the decrypted text so that
            // we can serialize to JSON. Possibly Note->jsonSerialize() could handle the decryption.
            $note->setText($notes->decrypt($note->getText(), $_GET['s']));
            die(json_encode($note));
        } else {
            die(json_encode(array("status" => "error", "message" => "Access denied")));
        }

    // Saving a note.
    } else if (isset($_POST['n']) && isset($_POST['text']) 
            && $notes->validateAuthor($_POST['s'], $_POST['n'])) {

        try {
            $note = $notes->getNote($_POST['n']);
            if (is_null($note)) {
                $note = new Note();
                $note->setId($_POST['n']);
                $note->setText($notes->encrypt($_POST['text'], $_POST['s']));
                $note->setAuthor($notes->getAuthor($_POST['s']));
                $note->setCreated(new DateTime());
                $note->setModified(new DateTime());
            } else {
                $note->setText($notes->encrypt($_POST['text'], $_POST['s']));
                $note->setModified(new DateTime());
            }
            $dm->persist($note);
            $dm->flush();
            die(json_encode(array("status" => "ok", "message" => "Note saved")));
        } catch (Exception $ex) {
            die(json_encode(array("status" => "error", "message" => "Note not saved: " . $ex->getMessage())));
        }

    }

} else {

    die(json_encode(array("status" => "error", "message" => "Not authenticated")));

}
