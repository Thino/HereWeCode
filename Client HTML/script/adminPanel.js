/***********************************************************************
 * 
 * Scripts for the admin panel page.
 * 
 **********************************************************************/

window.onload = function()
{
	getPlaces(null); /// Get places on load
}

/// Function to clear places
function clean()
{
	$("#places")[0].innerHTML = ''; /// Cleans all places
}

/// Function to get non approved places
function getPlaces()
{
	var url = getCookie('ip') + 'place/search'; /// Set uri
	var client = setClient('POST', url, true); /// Set client
	if (client != null)
	{
		var data = {};
		data['query'] = ''; /// No query
		data['startAt'] = '0';
		data['maxResults'] = '20';
		data['approved'] = 'false'; /// Places not approved
		data['orderBy'] = 'distance'; /// Ordered by distances
		var request = JSON.stringify(data);
		client.send(request);
		//console.log('Request : ' + request);
		if (client.readyState == 4) /// When ready
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

/// Display places on the page
function displayPlaces(data)
{
	var places = JSON.parse(data); /// Get and parse places
	var section = $('#places')[0]; /// Get the section
	for (var i = 0 ; i<places.length ; ++i) /// For each place
	{
		var placeDiv = document.createElement('div'); /// Create new div
		placeDiv.className = 'placeDiv'; /// Set classname for global style
		placeDiv.setAttribute('placeId', places[i].idPlace); /// Set place id into an attribute
		/// Then write informations in the div
		placeDiv.innerHTML = '<b>' + places[i].name + '</b><br/>' + places[i].summary + '<br/><i>' + places[i].address + '</i>';
		placeDiv.innerHTML += '<br/><b><i>Mark :</i></b> ' + places[i].placeMark + '&nbsp;&nbsp;<b><i>Distance :</i></b> ' + parseInt(places[i].distance/1000) + ' km';
		placeDiv.onclick = function() /// And set the click on the div to go to place page
		{
			createCookie('placeId', this.getAttribute('placeId'));
			goTo('place');
		}
		section.appendChild(placeDiv); /// Append to the section
	}
}
