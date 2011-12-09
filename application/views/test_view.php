<!-- the URL for this TEST SANDBOX is ../test -->

<html>
    <head>
        <title>piggyback search</title> 
        <link rel="stylesheet" media="screen" href="../../assets/jquery-ui-1.8.16.custom/css/custom-theme/jquery-ui-1.8.16.custom.css" type="text/css" />
        <script src="../../assets/jquery-ui-1.8.16.custom/js/jquery-1.6.2.min.js" type="text/javascript"></script>
        <script src="../../assets/jquery-ui-1.8.16.custom/js/jquery-ui-1.8.16.custom.min.js" type="text/javascript"></script>
        <script src="../../assets/js/date.format.js" type="text/javascript"></script>
        <script src="../../assets/jquery-ui-1.8.16.custom/development-bundle/ui/jquery.ui.accordionCustom.js"></script>
                <meta charset="utf-8">
	
    </head>
    <body>
	
	<script>
	$(function() {
            $('#accordionCustom').accordionCustom({
              header: 'div.accordion-header',
              content: 'div.accordion-content',
              footer: 'div.accordion-footer'
            })
	});
        
	</script>

        <div id="accordionCustom">
            <div class="accordion-header"><a>first header</a></div>
            <div class="accordion-content">first content</div>
            <div class="accordion-footer">the comments list</div>
            
            <div class="accordion-header"><a>second header</a></div>
            <div class="accordion-content">second content</div>
            <div class="accordion-footer">the comments list</div>
        </div>
        
    </body>
</html>


