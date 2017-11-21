<?php 
$companyId=isset($_GET["company_id"])? $_GET["company_id"]: "";
?>
<script>
    function confirmGoBack(){
        if (window.confirm("<?php echo $AppUI->_("LBL_CONFIRM_BACK", UI_OUTPUT_JS) ?>")){
            if(goCompanyHome !== "undefined" && "<?php echo $companyId ?>"!=""){
                var companyId=<?php echo $companyId ?>;
                var url=companyId==""?document.referrer:"index.php?m=companies&a=view&company_id=<?php echo $companyId ?>";
                window.location=url;
            }else{
                window.location=document.referrer;
            }
        }
    }
</script>
<input class="button" type="button" onclick="confirmGoBack()" value="<?php echo ucwords($AppUI->_('LBL_CANCEL')); ?>" />