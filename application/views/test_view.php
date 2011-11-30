<html>
    <head>
        <title>piggyback search</title> 
        <link rel="stylesheet" media="screen" href="../../assets/jquery-ui-1.8.16.custom/css/custom-theme/jquery-ui-1.8.16.custom.css" type="text/css" />
        <script src="../../assets/jquery-ui-1.8.16.custom/js/jquery-1.6.2.min.js" type="text/javascript"></script>
        <script src="../../assets/jquery-ui-1.8.16.custom/js/jquery-ui-1.8.16.custom.min.js" type="text/javascript"></script>
        		<script type="text/javascript">
			$(function(){
				// Dialog			
				$('#dialog').dialog({
					autoOpen: false,
					width: 1000,
					buttons: {
						"Refer!": function() { 
							$(this).dialog("close"); 
						}
					}
				});
				
				// Dialog Link
				$('#dialog_link').click(function(){
					$('#dialog').dialog('open');
					return false;
				});
				
				//hover states on the static widgets
				$('#dialog_link, ul#icons li').hover(
					function() { $(this).addClass('ui-state-hover'); }, 
					function() { $(this).removeClass('ui-state-hover'); }
				);
				
			});
		</script>
		<style type="text/css">
			/*demo page css*/
			body{ font: 62.5% "Helvetica", sans-serif; margin: 50px;}
			#dialog_link {padding: .4em 1em .4em 20px;text-decoration: none;position: relative;}
			#dialog_link span.ui-icon {margin: 0 5px 0 0;position: absolute;left: .2em;top: 50%;margin-top: -8px;}
		</style>	
	</head>
	<body>  
            <?php
            $hello = "hello";
            ?>
                <!-- button for dialog pop up-->
                <p><a href="#" id="dialog_link" class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-plus"></span>Refer to Friends</a></p>
		
		<!-- ui-dialog pop up-->
		<div id="dialog" title="Refer Friends to VendorName">
                    <p>this is from php: <?php echo $hello ?> </p>
                </div>
	</body>
</html>


