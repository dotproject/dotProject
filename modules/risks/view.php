<?php
require (DP_BASE_DIR . "/modules/risks/addedit.php");
?>
<script>
    var inputs=document.getElementsByTagName("input");
    var selects=document.getElementsByTagName("select");
    var textareas=document.getElementsByTagName("textarea");
    var list= new Array();
    list=list.concat(inputs, selects, textareas);
    for(j=0;j<list.length;j++){
        for(i=0; i< list[j].length;i++){
            list[j][i].disabled=true;
        } 
    }
</script>