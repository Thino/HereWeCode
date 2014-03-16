/***********************************************************************
 * 
 * Scripts for the place display page.
 * 
 **********************************************************************/

/// Things to execute on page load
window.onload = function()
{
	getPlace(); /// Get place informations
}

/// Get place informations
function getPlace()
{
	var placeId = getCookie('placeId'); /// Get the id of the place to get
	var url = getCookie('ip') + 'place/' + placeId; /// Set uri
	var client = setClient('GET', url, false); /// Set client
	if (client != null) /// If client ok
	{
		client.send(null); /// Send request
		if (client.readyState == 4) /// When response is here
		{
			if (client.status == 200) /// If successful
			{
				displayPlace(client.responseText); /// Display place
				getComments(placeId); /// Get comments
			}
		}
	}
}

/// Display the informations about the place
function displayPlace(data)
{
	var place = JSON.parse(data); /// Get and parse place
	var placeDiv = $("#place")[0];
	var url = getCookie('ip') + 'place/' + place.idPlace + '/facility'; /// Set uri for facilities
	var client = setClient('GET', url, false); /// Set client
	if (client != null) /// If ok
	{
		client.send(null); /// Send
		if ((client.readyState == 4) && (client.status == 200)) /// If everything okay
		{
			var facility = JSON.parse(client.responseText); /// Get facilities
			//console.log('Facility : ' + client.responseText);
			var infosDiv = document.createElement('div'); /// Create new infodiv
			infosDiv.className = 'infosDiv'; /// Set name for style
			///Then display place infos.
			infosDiv.innerHTML = '<b>' + place.name + '</b><br/>' + place.summary + '<br/><i>' + place.address + '</i>';
			placeDiv.appendChild(infosDiv); /// Append
			for (var i = 0 ; i < facility.length ; ++i) /// For each facility
			{
				var criteriaDiv = document.createElement('div'); /// Create new div
				criteriaDiv.className = 'criteriaDiv'; /// With class name
				criteriaDiv.style.left = 10*i + '%'; /// Set left according to number
				var criteriaName = document.createElement('p'); /// New paragraph for facility name
				criteriaName.style.fontSize = '0.8em'; /// Some style
				criteriaName.style.fontWeight = 'bold';
				criteriaName.innerHTML = facility[i].name; /// Display name
				criteriaDiv.appendChild(criteriaName); /// Append
				criteriaDiv.appendChild(document.createElement('br'));
				var criteriaIcon = document.createElement('img'); /// New image for the icon
				criteriaIcon.className = 'icon';
				criteriaIcon.src = facility[i].icon; /// Set source 
				criteriaDiv.appendChild(criteriaIcon); /// Append
				placeDiv.appendChild(criteriaDiv); /// Append all
			}
		}
	}
}

/// Function to get and display comments
function getComments(placeId)
{
	var url = getCookie('ip') + 'place/' + placeId + '/comment'; /// Set uri
	var commentsDiv = $("#comments")[0];
	var client = setClient('GET', url, false); /// Set client
	if (client != null) /// If client ok
	{
		client.send(null); /// Send request
		if ((client.readyState == 4) && (client.status == 200)) /// If successful
		{
			var comment = JSON.parse(client.responseText); /// Get comments
			//console.log(client.responseText);
			for (var i = 0 ; i < comment.length ; ++i) /// For each comment
			{
				var commentDiv = document.createElement('div'); /// Create new div
				commentDiv.className = 'commentDiv'; /// Set name for style
				commentDiv.style.top = i*10 + 4 + '%'; /// Set position
				var color = 202 + 20*(i%2); /// Set color
				commentDiv.style.background = 'rgb(' + color + ',' + color + ',' + color + ')';
				/// Then display comment
				commentDiv.innerHTML = comment[i].text + '<br/><i>' + comment[i].username + '</i>&nbsp;&nbsp;' + comment[i].date;
				commentsDiv.appendChild(commentDiv); /// And append
			}
		}
	}
}
