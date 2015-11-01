<!DOCTYPE html>
<html style="width: 100%; height: 100%">
<head>
  <title>MuvLuv Beta friends map</title>
  <link rel="stylesheet" href="jvectormap/jquery-jvectormap-2.0.3.css" type="text/css" media="screen"/>
  <script src="jquery-1.11.3.min.js"></script>
  <script src="jvectormap/jquery-jvectormap-2.0.3.min.js"></script>
  <script src="jvectormap/jquery-jvectormap-world-mill.js"></script>
  <link href='https://fonts.googleapis.com/css?family=Share+Tech+Mono' rel='stylesheet' type='text/css'>
  <style>
		html, body, #world-map{
				margin:0;
				width: 100%;
				height: 100%;
				font-family: 'Share Tech Mono', monospace;
				color: yellow;
				font-weight: bold;
				cursor: url("crosshair.png") 16 16, crosshair;
			}
		.jvectormap-tip{
			font-family: 'Share Tech Mono', monospace;
			color: yellow;
			font-weight: bold;
			border: 2px solid yellow;
		}
  </style>
</head>
<body>
  <div id="world-map"></div>
  <script>
	var gdpData = <?php
			$db = new PDO('sqlite:hitlist.sqlite');
			$result = $db->query( "SELECT region, Count(*) FROM hitlist GROUP BY region" );
			
			$output = [];
			if( $result )
				foreach( $result as $row )
					$output[ $row['region'] ] = intval( $row['Count(*)'] );

			print json_encode( $output );
		?>;

		$(function(){
			$('#world-map').vectorMap(
				{	map: 'world_mill'
				,	backgroundColor: "#000026"
				,	regionStyle:
					{	initial:
						{ 	fill: "#131969"
						,	stroke: "#42478d"
						,	"stroke-width": "1px"
						}
					,	hover: { cursor: "url('crosshair.png') 16 16" }
					}
				,	regionLabelStyle:
					{	hover:
						{	"font-family": "Share Tech Mono" }
					}
				,	series: {
						regions:
							[{	values: gdpData
							,	scale: ['#480270', '#900266']
							,	normalizeFunction: 'polynomial'
							}]
					}
				,	onRegionTipShow: function(e, el, code){
							str = ' (' + gdpData[code] + ' persons)';
							el.html( el.html() + (gdpData[code] ? str : '') );
						}
				,	onRegionClick: function( e, str ){
							request = $.post( "hitlist.php", {region: str} )
								.done( function( response ){
										if( !gdpData[str] )
											gdpData[str] = 0;
										gdpData[str] = gdpData[str] + 1;
										worldMap.series.regions[0].setValues( gdpData );
										worldMap.series.regions[0].setNormalizeFunction( 'polynomial' );
									} )
								.fail( function( response ){
										alert( response.responseText );
									} )
								;
						}
				} );
			
			worldMap = $('#world-map').vectorMap( 'get', 'mapObject' );
		});
	</script>
</body>
</html>
  