<?php

require_once("config.php");

date_default_timezone_set($cfg['timezone']);

$mongo = new Mongo($cfg['mongoHost']);

$secret = isset($_GET['s']) ? $_GET['s'] : "";
if (!isset($_GET['n'])) {
    header("Location:?n=home&s=" . $secret);
    exit();
}

function cleanForHtmlQuotes($arg) {
    return str_replace("\"", "\\\"", $arg);
}
$noteId = cleanForHtmlQuotes($_GET['n']);

$html = <<<eof
<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>{$noteId}</title>
        <link rel="icon" href="favicon.ico" type="image/x-icon">
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="css/foundation.css" />
        <link rel="stylesheet" href="css/app.css" />
        <link rel="stylesheet" href="css/notes.css" />
    </head>
    <body>
        <div class="message"></div>
        <div class="row">
            <div class="large-12 medium-12 small-12 columns">
                <form>
                    <div class="row">
                        <div class="large-12 medium-12 small-12 columns align">
                            <button type="button" class="save success button scroller-save">Save</button>
                            <span class="label">Last saved: <span class="last-saved"></span></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="large-12 medium-12 small-12 columns">
                            <textarea name="editor" id="editor" rows="40"></textarea>
                            <br />
                        </div>
                    </div>
                    <div class="row">
                        <div class="large-12 medium-12 small-12 columns align">
                            <button type="button" class="save success button">Save</button>
                            <span class="label">Last saved: <span class="last-saved"></span></span>
                        </div>
                    </div>
                </form>                
            </div>
        </div>

        <input id="noteId" name="noteId" type="hidden" value="{$noteId}" />
        <input id="secret" name="secret" type="hidden" value="{$secret}" />

        <script src="js/vendor/jquery.min.js"></script>
        <script src="js/vendor/what-input.min.js"></script>
        <script src="js/foundation.min.js"></script>
        <script src="js/app.js"></script>
        <script src="js/ckeditor/ckeditor.js"></script>
        <script src="js/vendor/fastclick.js"></script>
        <script src="js/notes.js"></script>
    </body>
</html>
eof;

print($html);
