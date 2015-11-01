<!DOCTYPE html>
<html style="width: 100%; height: 100%">
<head>
  <title>MuvLuv Beta friends map</title>
  <link rel="stylesheet" href="jvectormap/jquery-jvectormap-2.0.3.css" type="text/css" media="screen"/>
  <script src="jquery-1.11.3.min.js"></script>
  <script src="jvectormap/jquery-jvectormap-2.0.3.min.js"></script>
  <script src="jvectormap/jquery-jvectormap-world-mill.js"></script>
  <style>
		html, body, #world-map{ margin:0; width: 100%; height: 100% }
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
				,	series: {
						regions:
							[{	values: gdpData
							,	scale: ['#C8EEFF', '#0071A4']
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
  