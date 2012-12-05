		<div style="clear:both;"></div>
    </div><!-- #main -->
    
    </div><!-- #middleShadow --> 
    <div id="bottomShadow"></div>  

    <?php
    
    // action hook for placing content above the footer
    thematic_abovefooter();
    
    ?>    

	<div id="footer">
    
        <?php
        
        // action hook creating the footer 
        thematic_footer();
        
        ?>
        
	</div><!-- #footer -->
	
    <?php
    
    // actio hook for placing content below the footer
    thematic_belowfooter();
    
    ?>
    
</div><!-- #wrapper .hfeed -->

<?php 

// calling WordPress' footer action hook
wp_footer();

// action hook for placing content before closing the BODY tag
thematic_after(); 

?>

<!--Utvikling og design av Making Waves <http://www.makingwaves.no/>-->
</body>
</html>