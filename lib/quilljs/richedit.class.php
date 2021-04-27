<?php

class DpRichEdit {
    /**
     * True if the header had been written
     */
    static $HeaderWritten = false;

    function __construct($id, $content) {
        $this->id = $id;
        $this->content = $content;
    }

    /**
     * Render the rich text editor
     */
    function render() {
        global $AppUI;
        if (!DpRichEdit::$HeaderWritten) {
            DpRichEdit::WriteHeader();
        }
        include "richedit.view.php";
    }

    /**
     * Write the header for the dependencies
     */
    static function WriteHeader() {
        if (!DpRichEdit::$HeaderWritten) {
            if (empty($uistyle)) {
              global $AppUI;
              $uistyle = ($AppUI->getPref('UISTYLE') ?? ($dPconfig['host_style'] ?? 'default'));
            }
            echo '<script src="./lib/quilljs/src/quill.min.js"></script>' . PHP_EOL .
            '<link rel="stylesheet" href="./lib/quilljs/src/quill.snow.css" />' . PHP_EOL .
            '<link rel="stylesheet" href="./style/' . $uistyle . '/css/overrides.css" />' . PHP_EOL;
            DpRichEdit::$HeaderWritten = true;
        }
    }
}
