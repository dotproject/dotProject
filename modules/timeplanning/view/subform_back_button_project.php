<?php 
$projectId=isset($_GET["project_id"])? $_GET["project_id"]: $_GET["project"];
?>
<script>
    function confirmGoBack(){
        if (window.confirm("<?php echo $AppUI->_("LBL_CONFIRM_BACK", UI_OUTPUT_JS) ?>")){
                if(typeof targetScreenOnProject !== "undefined" && "<?php echo $projectId ?>"!=""){
                    var projectId=<?php echo $projectId ?>;
                    var url=projectId==""?document.referrer:"index.php?m=projects&a=view&project_id=<?php echo $projectId ?>";
                    window.location=url+"&targetScreenOnProject="+targetScreenOnProject;
                }else{
                    window.location=document.referrer;
                }
            }
    }
</script>
<input class="button" type="button" onclick="confirmGoBack()" value="<?php echo ucwords($AppUI->_('LBL_CANCEL')); ?>" />