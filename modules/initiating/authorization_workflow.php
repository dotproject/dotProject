<br />
<div align="center">
    <div align="center"  style="background-color: #FFF; width:95%;">
        <br />
        <span style="color:#000"><?php echo $AppUI->_("LBL_AUTHORIZATION_WORKFLOW") ?></span>
        <br /><br />
        <canvas id="authorization_workflow" width="950px" height="120px" align="center" style="border:0px solid #000000;">

        </canvas>
        <br />
    </div>
</div>
<script>
    /**
     * @param {ContextCanvas} ctx
     * @param {int} x
     * @param {int} y
     * @param {int} circumference
     * @param {int} status (1 - concluded; 2 - current; 3- not reached)
     * @param {String} label
     * @returns {void}
     */
    
    function drawCircle(ctx, x, y, circumference, status, label){
        var color="";
        if(status === 1){
            color="rgb(218,165,32)";
        }else if (status === 2){
            color="rgb(150,150,150)";
        }else if (status === 3){
            color="rgb(225,225,225)";
        }
        //print circle
        ctx.beginPath();
        ctx.fillStyle = color;
        ctx.arc(x, y, circumference, 0, 2*Math.PI);
        //ctx.stroke(); //means the element border
        ctx.fill(); // means the element background color
        ctx.closePath();
        
        //print label
        //move cursor to below part of the circle
        y+=circumference+20;
        x-= 25;
        ctx.beginPath();
        ctx.fillStyle = "rgb(0,0,0)";
        ctx.font = '11px arial';
        ctx.fillText(label, x, y);
        ctx.closePath();
        
    }
    
    
    function drawEndCircle(ctx, x, y, circumference,flag){
        var color="";
        if(flag){
            color="rgb(0,150,0)";
        }else{
            color="rgb(230,230,230)";
        }
        
        //print circle
        ctx.beginPath();
        ctx.fillStyle = color;
        ctx.arc(x, y, circumference, 0, 2*Math.PI);
        //ctx.stroke(); //means the element border
        ctx.fill(); // means the element background color
        ctx.closePath();
    }
    
    /**
     * 
     * @param {ContextCanvas} ctx
     * @param {int} x
     * @param {int} y
     * @param {string} name
     * @param {string} date
     * @returns {void}
     */
    function printArrow (ctx, x, y, name, date){      
        ctx.beginPath();
        ctx.fillStyle = "rgb(150,150,150)";
        ctx.moveTo(x+50,y);
        ctx.lineTo(x+135,y);//40px de linha
        ctx.fill();
        ctx.strokeStyle="rgb(150,150,150)";
        ctx.stroke();
        ctx.closePath();
        drawTriangle(ctx,x+135,y);
        
         //print label
        //move cursor to upper part of the arrow
        y-=40;
        x+=43;
        ctx.beginPath();
        ctx.fillStyle = "rgb(0,0,0)";
        ctx.font = '11px arial';
        ctx.fillText(date , x, y);
        ctx.fillText(name , x+35, y+15);
        ctx.closePath();
        ctx.fillStyle = "rgb(150,150,150)";//reset style
    }
    
    function drawTriangle(ctx,x, y){
        var context=ctx;
        context.fillStyle = "rgb(150,150,150)";
        // the triangle
        context.beginPath();
        context.moveTo(x, y);
        context.lineTo(x, y+5);
        context.lineTo(x+8, y);
        context.lineTo(x, y-5);
        context.lineTo(x, y);
        context.closePath();
        // the outline
        context.fill();
        context.stroke();        
    }
    
    
    //initiate status; 
    <?php
    //All status begins with 3 (not reached), and it is beign checked and filled accordinly
    $isDraft=3;
    $isCompleted=3;
    $isApproved=3;
    $isAuthorized=3;
    
    
    //get information about authorization workflow
    require_once (DP_BASE_DIR . "/modules/admin/admin.class.php");
    require_once (DP_BASE_DIR . "/modules/initiating/authoriziation_workflow.class.php");
    $authorizationWorkflow=new CAuthorizationWorkflow();
    $authorizationWorkflow->load($obj->initiating_id);
    //$obj: is an object from CInitiating classe, instantiate in addedit.php file which this one is include
    $user=new CUser();
    if(!is_null($authorizationWorkflow->draft_when)){
        $user->load($authorizationWorkflow->draft_by);
        $draftBy=$user->user_username;
        $oDate = new DateTime($authorizationWorkflow->draft_when);
        $draftWhen=$oDate->format("d/m/Y H:i:s");;
    }
    
    if(!is_null($authorizationWorkflow->completed_when)){
        $user->load($authorizationWorkflow->completed_by);
        $completedBy=$user->user_username;
        $oDate = new DateTime($authorizationWorkflow->completed_when);
        $completedWhen=$oDate->format("d/m/Y H:i:s");
    }
    
    if(!is_null($authorizationWorkflow->approved_when)){
        $user->load($authorizationWorkflow->approved_by);
        $approvedBy=$user->user_username;
        $oDate = new DateTime($authorizationWorkflow->approved_when);
        $approvedWhen=$oDate->format("d/m/Y H:i:s");
    }
    
    if(!is_null($authorizationWorkflow->authorized_when)){
        $user->load($authorizationWorkflow->authorized_by);
        $authorizedBy=$user->user_username;
        $oDate = new DateTime($authorizationWorkflow->authorized_when);
        $authorizedWhen=$oDate->format("d/m/Y H:i:s");
    }
    
    if($obj->initiating_completed==1){
        $isDraft=1;
        $isCompleted=1;
        $isApproved=2;
    }else{
        $isDraft=2;
    }
    
    if($obj->initiating_approved==1){
        $isApproved=1;
        $isAuthorized=2;
    }
    
    if($obj->initiating_authorized==1){
        $isAuthorized=1;
    }
    ?>
            
    var c = document.getElementById("authorization_workflow");
    var ctx = c.getContext("2d");
    ctx.lineWidth = 1;
    var x=70;
    var y=50;
    var circumference=40;
    var distanceBetweenCircles=200;
    
    drawCircle(ctx, x, y, circumference, <?php echo $isDraft ?>,"<?php echo $AppUI->_("Draft"); ?>");
    printArrow(ctx, x, y,"<?php echo $draftBy ?>","<?php echo $draftWhen ?>");
        
    x+=distanceBetweenCircles;
    drawCircle(ctx, x, y, circumference, <?php echo $isCompleted ?>,"<?php echo $AppUI->_("Completed"); ?>");
    printArrow(ctx, x, y,"<?php echo $completedBy ?>","<?php echo $completedWhen ?>");
    
    x+=distanceBetweenCircles;
    drawCircle(ctx, x, y, circumference, <?php echo $isApproved ?>,"<?php echo $AppUI->_("Approved"); ?>");
    printArrow(ctx, x, y,"<?php echo $approvedBy ?>","<?php echo $approvedWhen ?>");

    x+=distanceBetweenCircles;
    drawCircle(ctx, x, y, circumference, <?php echo $isAuthorized ?>,"<?php echo $AppUI->_("Authorized");  ?>");
    printArrow(ctx, x, y,"<?php echo $authorizedBy ?>","<?php echo $authorizedWhen ?>");
    
    x+=distanceBetweenCircles;
    drawEndCircle(ctx, x, y, 30, <?php echo $authorizedWhen =="" ? "false" : "true" ?>);
    
</script>
