<?php
// Entries in the LANGUAGES array are elements that describe the
// countries and language variants supported by this locale pack.
// Elements are keyed by the ISO 2 character language code in lowercase
// followed by an underscore and the 2 character country code in Uppercase.
// Each array element has 4 parts:
// 1. Directory name of locale directory
// 2. English name of language
// 3. Name of language in that language
// 4. Microsoft locale code

$dir = basename(dirname(__FILE__));

$LANGUAGES['en_AU'] = array ( $dir, 'English (Aus)', 'English (Aus)', 'ena');
$LANGUAGES['en_CA'] = array ( $dir, 'English (Can)', 'English (Can)', 'enc');
$LANGUAGES['en_GB'] = array ( $dir, 'English (GB)', 'English (GB)', 'eng');
$LANGUAGES['en_NZ'] = array ( $dir, 'English (NZ)', 'English (NZ)', 'enz');
$LANGUAGES['en_US'] = array ( $dir, 'English (US)', 'English (US)', 'enu', 'ISO8859-15');
?>
