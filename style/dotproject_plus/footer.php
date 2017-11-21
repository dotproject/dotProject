<?php echo $AppUI->getMsg(); ?>
<?php if(!$dialog) { ?>
</div>
</td>
<td bgcolor="#FFFFFF" width="8px"></td>
</tr>


<tr>
    <td colspan="3">
      &nbsp; 
    </td>
</tr>


</table>
<br /> <br /> <br />
<center>
	<span id="container_footer">
            <!--<img src="style/<?php echo $uistyle; ?>/img/dotproject_plus_logo_no_bg.png" style="width: 170px;height: 50px" />  -->
            
            <img src="style/<?php echo $uistyle;?>/img/icon_front_part.png" style="width:35px;height: 35px" /><span style="font-weight: bold;font-size: 18px">dotProject</span>&nbsp;<span style="color:gold;font-size: 26px">+</span>
           
            <br />
            <?php echo $AppUI->_("LBL_DOTPROJECT_PLUS_DESCRIPTION"); ?> 
            <br />
            <a href="http://www.gqs.ufsc.br/evolution-of-dotproject/" target="_blank" style="color:#000000">
                    www.gqs.ufsc.br/evolution-of-dotproject
            </a>
        </span>
</center>
<br />
</div>
<?php } ?>
</body>
</html>