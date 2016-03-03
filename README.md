Encrypted Notes
===============

Encryptonote is a simple encrypted note taking app.

DEPENDENCIES
============

* PHP (tested on 5.5.22)
    * openssl module (http://php.net/openssl)
* Composer (https://getcomposer.org/)
* Web server (tested on Apache/2.4.12)
* MongoDB (tested on 2.6.5)

TECHNOLOGIES USED
=================

* PHP
* MongoDB
* jQuery
* Foundation 6
* OpenSSL
* CKEditor

QUICK START
===========

Copy `example.config.php` to `config.php` and edit,

    $cfg['mongoHost'] = "localhost:27017";
    $cfg['mongoDatabase'] = "notes_db";
    $cfg['timezone'] = "America/Los_Angeles";
    $cfg['secrets'] = array("foobar" => "dea328d398f89527aafc56181d299b35260ef3ba20ab9651afa60e1bad24c089");
    $cfg['salt'] = "yZO6LH8QM8ZXdtQt";
    $cfg['cipher'] = "AES-256-CBC";
    $cfg['hash'] = "sha256";
    $cfg['date-format'] = "F j, Y, g:i a";

Install PHP dependencies,

    composer install

Open: https://example.com/notes/#/note/my-first-note/dea328d398f89527aafc56181d299b35260ef3ba20ab9651afa60e1bad24c089

Notice, `my-first-note` is an arbitrary note id, and we've included the hash from `$cfg['secrets']` in the URL.
See `INSTALL` below for more information.

INSTALL
=======

Copy `example.config.php` to `config.php` and edit.

To create your secret (application API key) hash with sha256. This can be done easily
with duckduckgo. For example, https://duckduckgo.com/?q=sha256+test&ia=answer
Choose this secret wisely until there is a utility to change secrets. Your secret is
used to encrypt your data and is not stored anywhere, so to change your password you
must decrypt your data with your old secret and re-encrypt with the new secret. A
utility will be released soon.

This hash should be stored in the `$cfg['secrets'] = array("your-username" => "your-secret-hash")` array. `your-username` will
be used to store your author name. There is an `author` field on each `Note`. See `Documents/Note.php`.

Run `composer.phar install`, where composer executable may differ from the example.

Replace `{note-id}` with your documents unique id, and `{your-secret}` with your secret or API key (used interchangeably).

Open: https://example.com/notes/#/note/{note-id}/{your-secret}
