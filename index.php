<?php

require_once("config.php");
require_once("lib/Notes.php");
require_once("lib/WsTmpl.php");

date_default_timezone_set($cfg['timezone']);

$noteId = Notes::cleanForHtmlQuotes($_GET['n']);

$css = "notes.css";
if (Notes::isMobile()) {
    $css = "notes-mobile.css";
}

$t = new WsTmpl();

$t->setFile("tmpl/notes-editor.tmpl");
$t->setData(array());
$notesEditor = $t->compile();

$t->setFile("tmpl/index.tmpl");
$t->setData(array("css" => $css, "noteId" => $noteId, "notes-editor" => $notesEditor));

$index = $t->compile();

print($index);
