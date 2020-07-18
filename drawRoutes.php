
map.on('load', drawRoutes);
function drawRoutes() {
	map.addSource('route', {
		'type': 'geojson',
		'data': {
		'type': 'Feature',
		'properties': {},
		'geometry': {
			'type': 'LineString',
			'coordinates': [
				[110, 14],
				[111, 13],
				[112, 14],
				[113, 14],
				[114, 14]
			]
			}
		}
	});
    map.addLayer({
        'id': 'route',
        'type': 'line',
        'source': 'route',
        'layout': {
            'line-join': 'round',
            'line-cap': 'round'
        },
        'paint': {
            'line-color': '#888',
            'line-width': 8
        }
    });
}