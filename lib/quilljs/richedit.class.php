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
            echo '<script src="lib/quilljs/src/quill.min.js"></script>
            <link rel="stylesheet" href="lib/quilljs/src/quill.snow.css" />';
            DpRichEdit::$HeaderWritten = true;
        }
    }
}