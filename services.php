<?php

require_once("config.php");

date_default_timezone_set($cfg['timezone']);

$loader = require_once("vendor/autoload.php");

use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;

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

use Documents;
use Documents\Note;

function validate($secret, $cfg) {
    return (isset($_GET['s']) && in_array(hash("sha256", $_GET['s']), $cfg['secrets']))
        || (isset($_POST['s']) && in_array(hash("sha256", $_POST['s']), $cfg['secrets']));
}

function getAuthor($secret, $cfg) {
    $hash = hash("sha256", $secret);
    return array_search($hash, $cfg['secrets']);
}

function encrypt($data, $password, $cfg) {
    return openssl_encrypt($data, "AES-256-CBC", $password, false, $cfg['salt']);
}

function decrypt($data, $password, $cfg) {
    return openssl_decrypt($data, "AES-256-CBC", $password, false, $cfg['salt']);
}

if (validate($_GET['s'], $cfg)) {

    if (isset($_GET['n'])) {

        $note = $dm->find("Documents\Note", $_GET['n']);
        if (is_null($note)) {
            $note = new Note();
            $note->setId($_GET['n']);
            $note->setText(encrypt("Lorem ipsum.", $_GET['s'], $cfg));
            $note->setAuthor(getAuthor($_GET['s'], $cfg));
            $note->setCreated(new DateTime());
            $note->setModified(new DateTime());
            $dm->persist($note);
            $dm->flush();
        }
        $note->setText(decrypt($note->getText(), $_GET['s'], $cfg));
        die(json_encode($note));

    } else if (isset($_POST['n']) && isset($_POST['text'])) {

        try {
            $note = $dm->find("Documents\Note", $_POST['n']);
            if (is_null($note)) {
                $note = new Note();
                $note->setId($_POST['n']);
                $note->setText(encrypt($_POST['text'], $_POST['s'], $cfg));
                $note->setAuthor(getAuthor($_POST['s'], $cfg));
                $note->setCreated(new DateTime());
                $note->setModified(new DateTime());
            } else {
                $note->setText(encrypt($_POST['text'], $_POST['s'], $cfg));
                $note->setModified(new DateTime());
            }
            $dm->persist($note);
            $dm->flush();
        } catch (Exception $ex) {
            die(json_encode(array("status" => "error", "message" => "Note not saved: " . $ex->getMessage())));
        }
        die(json_encode(array("status" => "ok", "message" => "Note saved")));

    }

} else {

    die(json_encode(array("status" => "error", "message" => "Not authenticated")));

}
