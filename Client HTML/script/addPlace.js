/***********************************************************************
 * 
 * Scripts for the page addPlace.
 * 
 **********************************************************************/

/// On page load (can't use window.onload because of chrome)
var secret = getCookie('secret'); /// Get the identification cookie
if ((secret == null) || (secret == '')) /// If cookie is empty
{
	goTo('index'); /// Back to index, user must log in to access
}

/***********************************************************************
 * 
 * Function to add a new place in the database to approve.
 * 
 **********************************************************************/

function addNewPlace()
{
	var data = {};
	if ($("#placeName")[0].value != '') /// If place name was given
	{
		data['name'] = $("#placeName")[0].value; /// Get it
	}
	else /// Else
	{
		alert('Give a name for the place !'); /// Tell the user
		return; /// Stop
	}
	if ($("#summary")[0].value != '') /// Same for other needed values
	{
		data['summary'] = $("#summary")[0].value;
	}
	else
	{
		alert('Give a summary !');
		return;
	}
	if (($("#number")[0].value != '') && ($("#street")[0].value != ''))
	{
		data['address'] = $("#number")[0].value + ' ' + $("#street")[0].value;
	}
	else
	{
		alert('Give an address !');
		return;
	}
	if (($("#zip")[0].value != '') && ($("#city")[0].value != ''))
	{
		data['address'] += ' ' + $("#zip")[0].value + ' ' + $("#city")[0].value;
	}
	else
	{
		alert('Give a city ');
		return;
	}
	var url = getCookie('ip') + 'place'; /// Set url
	var client = setClient('POST', url, true); /// Set client
	if (client != null) /// If client ok
	{
		var request = JSON.stringify(data); /// Stringify datas
		//console.log(request);
		client.send(request); /// Send request
		if (client.readyState == 4) /// When response is here
		{
			if (client.status == 201) /// If successful
			{
				alert('Place has been added.\nModerators will soon check it to approve it.'); /// Tell user
				goTo('index'); /// Go back to index
			}
			else
			{
				alert('Error ' + client.status);
			}
		}
	}
}
