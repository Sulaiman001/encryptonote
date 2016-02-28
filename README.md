Encrypted Notes
===============

This is a simple encrypted note taking app.
Notes are stored encrypted with openssl AES-256-CBC in a MongoDB database.
Note editing powered by ckeditor for wysiwyg note taking.

INSTALL
=======

Copy `example.config.php` to `config.php` and edit.

To create your secret (application API key) hash with sha256. This can be done easily
with duckduckgo. For example, https://duckduckgo.com/?q=sha256+test&ia=answer

This hash should be stored in the `$cfg['secrets'] = array("your-username" => "your-secret-hash")` array. `your-username` will
be used to store your author name. There is an `author` field on each `Note`. See `Documents/Note.php`.

Run `composer.phar install`, where composer executable may differ from the example.

Open: https://example.com/notes/index.php?s=your-secret

This will redirect to the `home` note by default. Add the query string parameter
`?n=my-note` to edit a note. This is the MongoDB id for a note. Don't forget you
always have to submit your secret. e.g. `?n=my-note&s=your-secret`
