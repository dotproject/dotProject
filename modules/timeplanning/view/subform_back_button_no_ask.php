<?php 
$projectId=isset($_GET["project_id"])? $_GET["project_id"]: "";
?>
<script>
    function goBackNoAsk(){
        if(typeof targetScreenOnProject !== "undefined"){
            window.location=document.referrer+"&targetScreenOnProject="+targetScreenOnProject;
        }else{
            window.location=document.referrer;
        }
    }
</script>
<input class="button" type="button" onclick="goBackNoAsk()" value="<?php echo ucwords($AppUI->_('LBL_CANCEL')); ?>" />