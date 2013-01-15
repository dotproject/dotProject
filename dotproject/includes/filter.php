<?php

/**
 * Filtering based on the Drupal 6 filter_xss function, which in
 * turn is based on kses.
 *
 * Apart from minor changes to function names (well, to the drual_validate_utf8
 * function at least) this is a direct copy of the drupal code.
 * Drupal is released under GPL and is copyright by the original authors.
 *
 * @see Drupal http://drupal.org
 * @see kses http://sourceforge.net/projects/kses
 */

function check_plain($string) {
  static $php525;

  if (!isset($php525)) {
    $php525 = version_compare(PHP_VERSION, '5.2.5', '>=');
  }
  if ($php525) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
  }
  return (preg_match('/^./us', $string) == 1) ? htmlspecialchars($string, ENT_QUOTES, 'UTF-8') : '';
}

function validate_utf8($string) {
  if (strlen($string) == 0) {
    return true;
  }
  return (preg_match('/^./us', $string) == 1);
}

function filter_xss($string) {
  // Only operate on valid UTF-8 strings. This is necessary to prevent cross
  // site scripting issues on Internet Explorer 6.
  if (!validate_utf8($string)) {
    return '';
  }
  $allowed_tags = dPgetConfig('filter_allowed_tags', array('a', 'em', 'strong', 'cite', 'code', 'ul', 'ol', 'li', 'dl', 'dt', 'dd', 'table', 'tr', 'td', 'tbody', 'thead', 'br', 'b', 'i'));
  // Store the input format
  _filter_xss_split($allowed_tags, TRUE);
  // Remove NUL characters (ignored by some browsers)
  $string = str_replace(chr(0), '', $string);
  // Remove Netscape 4 JS entities
  $string = preg_replace('%&\s*\{[^}]*(\}\s*;?|$)%', '', $string);

  // Defuse all HTML entities
  $string = str_replace('&', '&amp;', $string);
  // Change back only well-formed entities in our whitelist
  // Decimal numeric entities
  $string = preg_replace('/&amp;#([0-9]+;)/', '&#\1', $string);
  // Hexadecimal numeric entities
  $string = preg_replace('/&amp;#[Xx]0*((?:[0-9A-Fa-f]{2})+;)/', '&#x\1', $string);
  // Named entities
  $string = preg_replace('/&amp;([A-Za-z][A-Za-z0-9]*;)/', '&\1', $string);

  return preg_replace_callback('%
    (
    <(?=[^a-zA-Z!/])  # a lone <
    |                 # or
    <!--.*?-->        # a comment
    |                 # or
    <[^>]*(>|$)       # a string that starts with a <, up until the > or the end of the string
    |                 # or
    >                 # just a >
    )%x', '_filter_xss_split', $string);
}

/**
 * Processes an HTML tag.
 *
 * @param @m
 *   An array with various meaning depending on the value of $store.
 *   If $store is TRUE then the array contains the allowed tags.
 *   If $store is FALSE then the array has one element, the HTML tag to process.
 * @param $store
 *   Whether to store $m.
 * @return
 *   If the element isn't allowed, an empty string. Otherwise, the cleaned up
 *   version of the HTML element.
 */
function _filter_xss_split($m, $store = FALSE) {
  static $allowed_html;

  if ($store) {
    $allowed_html = array_flip($m);
    return;
  }

  $string = $m[1];

  if (substr($string, 0, 1) != '<') {
    // We matched a lone ">" character
    return '&gt;';
  }
  else if (strlen($string) == 1) {
    // We matched a lone "<" character
    return '&lt;';
  }

  if (!preg_match('%^(?:<\s*(/\s*)?([a-zA-Z0-9]+)([^>]*)>?|(<!--.*?-->))$%', $string, $matches)) {
    // Seriously malformed
    return '';
  }

  $slash = trim($matches[1]);
  $elem = &$matches[2];
  $attrlist = &$matches[3];
  $comment = &$matches[4];

  if ($comment) {
    $elem = '!--';
  }

  if (!isset($allowed_html[strtolower($elem)])) {
    // Disallowed HTML element
    return '';
  }

  if ($comment) {
    return $comment;
  }

  if ($slash != '') {
    return "</$elem>";
  }

  // Is there a closing XHTML slash at the end of the attributes?
  // In PHP 5.1.0+ we could count the changes, currently we need a separate match
  $xhtml_slash = preg_match('%\s?/\s*$%', $attrlist) ? ' /' : '';
  $attrlist = preg_replace('%(\s?)/\s*$%', '\1', $attrlist);

  // Clean up attributes
  $attr2 = implode(' ', _filter_xss_attributes($attrlist));
  $attr2 = preg_replace('/[<>]/', '', $attr2);
  $attr2 = strlen($attr2) ? ' '. $attr2 : '';

  return "<$elem$attr2$xhtml_slash>";
}

/**
 * Processes a string of HTML attributes.
 *
 * @return
 *   Cleaned up version of the HTML attributes.
 */
function _filter_xss_attributes($attr) {
  $attrarr = array();
  $mode = 0;
  $attrname = '';

  while (strlen($attr) != 0) {
    // Was the last operation successful?
    $working = 0;

    switch ($mode) {
      case 0:
        // Attribute name, href for instance
        if (preg_match('/^([-a-zA-Z]+)/', $attr, $match)) {
          $attrname = strtolower($match[1]);
          $skip = ($attrname == 'style' || substr($attrname, 0, 2) == 'on');
          $working = $mode = 1;
          $attr = preg_replace('/^[-a-zA-Z]+/', '', $attr);
        }

        break;

      case 1:
        // Equals sign or valueless ("selected")
        if (preg_match('/^\s*=\s*/', $attr)) {
          $working = 1; $mode = 2;
          $attr = preg_replace('/^\s*=\s*/', '', $attr);
          break;
        }

        if (preg_match('/^\s+/', $attr)) {
          $working = 1; $mode = 0;
          if (!$skip) {
            $attrarr[] = $attrname;
          }
          $attr = preg_replace('/^\s+/', '', $attr);
        }

        break;

      case 2:
        // Attribute value, a URL after href= for instance
        if (preg_match('/^"([^"]*)"(\s+|$)/', $attr, $match)) {
          $thisval = filter_xss_bad_protocol($match[1]);

          if (!$skip) {
            $attrarr[] = "$attrname=\"$thisval\"";
          }
          $working = 1;
          $mode = 0;
          $attr = preg_replace('/^"[^"]*"(\s+|$)/', '', $attr);
          break;
        }

        if (preg_match("/^'([^']*)'(\s+|$)/", $attr, $match)) {
          $thisval = filter_xss_bad_protocol($match[1]);

          if (!$skip) {
            $attrarr[] = "$attrname='$thisval'";;
          }
          $working = 1; $mode = 0;
          $attr = preg_replace("/^'[^']*'(\s+|$)/", '', $attr);
          break;
        }

        if (preg_match("%^([^\s\"']+)(\s+|$)%", $attr, $match)) {
          $thisval = filter_xss_bad_protocol($match[1]);

          if (!$skip) {
            $attrarr[] = "$attrname=\"$thisval\"";
          }
          $working = 1; $mode = 0;
          $attr = preg_replace("%^[^\s\"']+(\s+|$)%", '', $attr);
        }

        break;
    }

    if ($working == 0) {
      // not well formed, remove and try again
      $attr = preg_replace('/
        ^
        (
        "[^"]*("|$)     # - a string that starts with a double quote, up until the next double quote or the end of the string
        |               # or
        \'[^\']*(\'|$)| # - a string that starts with a quote, up until the next quote or the end of the string
        |               # or
        \S              # - a non-whitespace character
        )*              # any number of the above three
        \s*             # any number of whitespaces
        /x', '', $attr);
      $mode = 0;
    }
  }

  // the attribute list ends with a valueless attribute like "selected"
  if ($mode == 1) {
    $attrarr[] = $attrname;
  }
  return $attrarr;
}

/**
 * Processes an HTML attribute value and ensures it does not contain an URL
 * with a disallowed protocol (e.g. javascript:)
 *
 * @param $string
 *   The string with the attribute value.
 * @param $decode
 *   Whether to decode entities in the $string. Set to FALSE if the $string
 *   is in plain text, TRUE otherwise. Defaults to TRUE.
 * @return
 *   Cleaned up and HTML-escaped version of $string.
 */
function filter_xss_bad_protocol($string, $decode = TRUE) {
  static $allowed_protocols;
  if (!isset($allowed_protocols)) {
    $allowed_protocols = array_flip(dPgetConfig('filter_allowed_protocols', array('http', 'https', 'ftp', 'news', 'nntp', 'tel', 'telnet', 'mailto', 'irc', 'ssh', 'sftp', 'webcal', 'rtsp')));
  }

  // Get the plain text representation of the attribute value (i.e. its meaning).
  if ($decode) {
    $string = decode_entities($string);
  }

  // Iteratively remove any invalid protocol found.

  do {
    $before = $string;
    $colonpos = strpos($string, ':');
    if ($colonpos > 0) {
      // We found a colon, possibly a protocol. Verify.
      $protocol = substr($string, 0, $colonpos);
      // If a colon is preceded by a slash, question mark or hash, it cannot
      // possibly be part of the URL scheme. This must be a relative URL,
      // which inherits the (safe) protocol of the base document.
      if (preg_match('![/?#]!', $protocol)) {
        break;
      }
      // Per RFC2616, section 3.2.3 (URI Comparison) scheme comparison must be case-insensitive
      // Check if this is a disallowed protocol.
      if (!isset($allowed_protocols[strtolower($protocol)])) {
        $string = substr($string, $colonpos + 1);
      }
    }
  } while ($before != $string);
  return check_plain($string);
}


function decode_entities($text, $exclude = array()) {
  static $html_entities;
  if (!isset($html_entities)) {
    include_once DP_BASE_DIR . '/includes/unicode.entities.inc.php';
  }

  // Flip the exclude list so that we can do quick lookups later.
  $exclude = array_flip($exclude);

  // Use a regexp to select all entities in one pass, to avoid decoding 
  // double-escaped entities twice. The PREG_REPLACE_EVAL modifier 'e' is
  // being used to allow for a callback (see 
  // http://php.net/manual/en/reference.pcre.pattern.modifiers).
  return preg_replace('/&(#x?)?([A-Za-z0-9]+);/e', '_decode_entities("$1", "$2", "$0", $html_entities, $exclude)', $text);
}

/**
 * Helper function for decode_entities
 */
function _decode_entities($prefix, $codepoint, $original, &$html_entities, &$exclude) {
  // Named entity
  if (!$prefix) {
    // A named entity not in the exclude list.
    if (isset($html_entities[$original]) && !isset($exclude[$html_entities[$original]])) {
      return $html_entities[$original];
    }
    else {
      return $original;
    }
  }
  // Hexadecimal numerical entity
  if ($prefix == '#x') {
    $codepoint = base_convert($codepoint, 16, 10);
  }
  // Decimal numerical entity (strip leading zeros to avoid PHP octal notation)
  else {
    $codepoint = preg_replace('/^0+/', '', $codepoint);
  }
  // Encode codepoint as UTF-8 bytes
  if ($codepoint < 0x80) {
    $str = chr($codepoint);
  }
  else if ($codepoint < 0x800) {
    $str = chr(0xC0 | ($codepoint >> 6))
         . chr(0x80 | ($codepoint & 0x3F));
  }
  else if ($codepoint < 0x10000) {
    $str = chr(0xE0 | ( $codepoint >> 12))
         . chr(0x80 | (($codepoint >> 6) & 0x3F))
         . chr(0x80 | ( $codepoint       & 0x3F));
  }
  else if ($codepoint < 0x200000) {
    $str = chr(0xF0 | ( $codepoint >> 18))
         . chr(0x80 | (($codepoint >> 12) & 0x3F))
         . chr(0x80 | (($codepoint >> 6)  & 0x3F))
         . chr(0x80 | ( $codepoint        & 0x3F));
  }
  // Check for excluded characters
  if (isset($exclude[$str])) {
    return $original;
  }
  else {
    return $str;
  }
}

