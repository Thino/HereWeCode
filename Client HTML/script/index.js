/***********************************************************************
 * 
 * Scripts for the index page.
 * 
 **********************************************************************/

/// Actions to do on load.
window.onload = function()
{
	var query = getCookie('search'); /// Get search cookie
	if (query != null) /// If existing
	{
		getPlaces('mark', query); /// Execute search
		deleteCookie('search'); /// Delete cookie
	}
	else
	{
		getPlaces(null); /// Else, normal places
	}
}

/// Clear places
function clean()
{
	$("#places")[0].innerHTML = '';
}

/// Get approved places
function getPlaces(order, query)
{
	var url = getCookie('ip') + 'place/search'; /// Set uri
	var client = setClient('POST', url, false); /// Set client
	if (client != null) /// If client ok
	{
		var data = {};
		data['query'] = '';
		if (query != null) /// If query isn't empty
		{
			data['query'] = query; /// Set query
		}
		data['startAt'] = '0';
		data['maxResults'] = '38';
		data['approved'] = 'true'; /// Only approved places (identification needed else)
		data['orderBy'] = order;
		var request = JSON.stringify(data); /// Stringify request
		client.send(request); /// Send request
		if (client.readyState == 4) /// When response is here
		{
			if (client.status == 200) /// If successful
			{
				//console.log('Resultats ' + client.responseText);
				clean(); /// Clear
				displayPlaces(client.responseText); /// Display
			}
			else
			{
				console.log('Error : ' + client.status);
			}
		}
	}
}

/// Display places
function displayPlaces(data)
{
	var places = JSON.parse(data); /// Get and parse places
	var section = $('#places')[0];
	for (var i = 0 ; i<places.length ; ++i) /// For each place
	{
		var placeDiv = document.createElement('div'); /// Create new div
		placeDiv.className = 'placeDiv'; /// Give class name for global style
		placeDiv.setAttribute('placeId', places[i].idPlace); /// Set id as attribute
		/// Then insert informations
		placeDiv.innerHTML = '<b>' + places[i].name + '</b><br/>' + places[i].summary + '<br/><i>' + places[i].address + '</i>';
		placeDiv.innerHTML += '<br/><b><i>Mark :</i></b> ' + places[i].placeMark + '&nbsp;&nbsp;<b><i>Distance :</i></b> ' + parseInt(places[i].distance/1000) + ' km';
		placeDiv.onclick = function() /// Set click to go to place page
		{
			createCookie('placeId', this.getAttribute('placeId'));
			goTo('place');
		}
		section.appendChild(placeDiv); /// Append
	}
}
