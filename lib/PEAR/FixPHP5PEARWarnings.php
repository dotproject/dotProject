<?php
if ($skipmsg) {
  if (version_compare(phpversion(), '8.0.0', '<')) {
    $a = &new $ec($code, $mode, $options, $userinfo);  // PHP 8 doesn't need/like references (gwyneth 20210414)
  } else {
    $a = new $ec($code, $mode, $options, $userinfo);
  }
} else {
  if (version_compare(phpversion(), '8.0.0', '<')) {
    $a = &new $ec($message, $code, $mode, $options, $userinfo);  // PHP 8 doesn't need/like references (gwyneth 20210414)
  } else {
    $a = new $ec($message, $code, $mode, $options, $userinfo);
  }
}
?>
