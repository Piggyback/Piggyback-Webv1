<html>
    <head>
        <title>piggyback search</title> 
        <link rel="stylesheet" media="screen" href="../../assets/jquery-ui-1.8.16.custom/css/custom-theme/jquery-ui-1.8.16.custom.css" type="text/css" />
        <script src="../../assets/jquery-ui-1.8.16.custom/js/jquery-1.6.2.min.js" type="text/javascript"></script>
        <script src="../../assets/jquery-ui-1.8.16.custom/js/jquery-ui-1.8.16.custom.min.js" type="text/javascript"></script>
        <script src="../../assets/js/date.format.js" type="text/javascript"></script>
                <meta charset="utf-8">
	
    </head>
    <body>
	
	
	
	
	
	
	<style>
	#project-icon {
		float: left;
		height: 32px;
		width: 32px;
	}
	#project-description {
		margin: 0;
		padding: 0;
	}
	</style>
	<script>
	$(function() {
		var projects = [
			{
				value: 'jquery',
				label: "<img src='hello.jpg'>jQuery",
				desc: 'the write less, do more, JavaScript library',
				icon: 'jquery_32x32.png'
			},
			{
				value: 'jquery-ui',
				label: 'jQuery UI',
				desc: 'the official user interface library for jQuery',
				icon: 'jqueryui_32x32.png'
			},
			{
				value: "sizzlejs",
				label: "Sizzle JS",
				desc: "a pure-JavaScript CSS selector engine",
				icon: "sizzlejs_32x32.png"
			}
		];

		$( "#project" ).autocomplete({
			minLength: 0,
			source: projects,
			focus: function( event, ui ) {
				$( "#project" ).val(ui.item.label.split('>')[1]);
				return false;
			},
			select: function( event, ui ) {
				$( "#project" ).val(ui.item.label.split('>')[1]);
				$( "#project-id" ).val( ui.item.value );
				$( "#project-description" ).html( ui.item.desc );
				$( "#project-icon" ).attr( "src", "images/" + ui.item.icon );
				return false;
			}
		})
		.data( "autocomplete" )._renderItem = function( ul, item ) {
			return $( "<li></li>" )
				.data( "item.autocomplete", item )
				.append( "<a>" + item.label + "<br>" + item.desc + "</a>" )
				.appendTo( ul );
		};
	});
	</script>


        <div class="demo">
                <div id="project-label">Who do you want to refer <B>"Spicy Spices"</b> to?</div>
                <img id="project-icon" style="border:0px;" />
                <input id="project"/>
                <input type="hidden" id="project-id"/>
                <p id="project-description"></p>
        </div><!-- End demo -->
	</body>
</html>


