<?php

// Your MongoDB host:port
$cfg['mongoHost'] = "localhost:27017";
// Each collection for this database comes from entities in Documents/
$cfg['mongoDatabase'] = "enter_database";

// http://us3.php.net/manual/en/timezones.php
$cfg['timezone'] = "America/Los_Angeles";

// The 'secrets' array should be an array of sha256 hashes with the keys being your usernames.
// Authentication happens when you hit ?s=secret where 'secret' is some
// passphrase that will be hashed by sha256.
$cfg['secrets'] = array("foousername" => "dea328d398f89527aafc56181d299b35260ef3ba20ab9651afa60e1bad24c089");
// This is a salt for encrypting note text.
$cfg['salt'] = "h6Dfhrck2NxXD8wI";

// Configure the date format for each todo item. Uses PHP's date() function,
// http://php.net/manual/en/function.date.php
$cfg['date-format'] = "F j, Y, g:i a";
