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
