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
	
	<meta charset="utf-8">

	<style>
	h1 { padding: .2em; margin: 0; }
	#products { float:left; width: 500px; margin-right: 2em; }
	#list { width: 200px; float: left; }
	/* style the list to maximize the droppable hitarea */
	#list ol { margin: 0; padding: 1em 0 1em 3em; }
	</style>
	<script>
	$(function() {
		$( "#catalog" ).accordion();
		$( "#catalog h3" ).draggable({
			appendTo: "body",
			helper: "original"
		});
		$( "#list ol" ).droppable({
			activeClass: "ui-state-default",
			hoverClass: "ui-state-hover",   
			accept: ":not(.ui-sortable-helper)",
			drop: function( event, ui ) {
				$( this ).find( ".placeholder" ).remove();
				$( "<li></li>" ).text( ui.draggable.text() ).appendTo( this );
			}
		}).sortable({
			items: "li:not(.placeholder)",
			sort: function() {
				// gets added unintentionally by droppable interacting with sortable
				// using connectWithSortable fixes this, but doesn't allow you to customize active/hoverClass options
				$( this ).removeClass( "ui-state-default" );
			}
		});
	});
	</script>

        <div class="demo">
        <div id="products">
            <h1 class="ui-widget-header">Products</h1>	
            <div id="catalog">
                <h3><a href="#">T-Shirts</a></h3>
                <div>
                    <ul>
                        <li>Lolcat Shirt</li>
                        <li>Cheezeburger Shirt</li>
                        <li>Buckit Shirt</li>
                    </ul>
                </div>
                <h3><a href="#">Bags</a></h3>
                <div>
                    <ul>
                        <li>Zebra Striped</li>
                        <li>Black Leather</li>
                        <li>Alligator Leather</li>
                    </ul>
                </div>
                <h3><a href="#">Gadgets</a></h3>
                <div>
                    <ul>
                        <li>iPhone</li>
                        <li>iPod</li>
                        <li>iPad</li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="list">
            <h1 class="ui-widget-header">Best Burger Joints</h1>
            <div class="ui-widget-content">
                <ol>
                    <li class="list">Best Burgers</li>
                    <li class="list">LA List</li>
                </ol>
            </div>
        </div>
        </div><!-- End demo -->
        
    </body>
</html>


