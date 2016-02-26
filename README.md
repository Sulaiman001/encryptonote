Encrypted Notes
===============

This project aims to create the most simple but encrypted note taking software possible.
Notes are stored encrypted with cbc-256-aes in a MongoDB database. The project is written
in PHP, jQuery, with persistence handled by Doctrine. The layout is handled by Foundation 6.

INSTALL
=======

Copy `example.config.php` to `config.php` and edit.

To create your secret (application API key) hash with sha256. This can be done easily
with duckduckgo. For example, https://duckduckgo.com/?q=sha256+test&ia=answer

This hash should be stored in the `$cfg['secrets'] = array("your-secret-hash")` array.

Open: https://example.com/notes/index.php?s=your-secret

This will redirect to the `home` note by default. Add the query string parameter
`?n=my-note` to edit a note. This is the MongoDB id for a note. Don't forget you
always have to submit your secret. e.g. `?n=my-note&s=your-secret`

The longer your secret, the better it can be obfuscated in a browser window. The current
security strategy is to be as simple as possible, and to rely on security by obfuscation. In
the future other strategies may be implemented. There's so little time.