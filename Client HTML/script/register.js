/***********************************************************************
 * 
 * Function to add an user to the database
 * 
 **********************************************************************/

function addMember()
{
	var data = {};
	/// Store values for the request
	data['username'] = $("#inUsername")[0].value;
	var password = btoa($("#inPassword")[0].value);
	data['password'] = password;
	data['picture'] = $("#inImage")[0].value;
	var url = getCookie('ip') + 'member' /// Set url
	var client = setClient('POST', url, false); /// Set client
	if (client != null) /// If client ok
	{
		var request = JSON.stringify(data);
		client.send(request); /// Send request
		if (client.readyState == 4)
		{
			if (client.status == 201) /// If successful
			{
				var currentUser = client.responseText; /// Get infos
				createCookie('currentUser', currentUser, 1); /// Create user cookie
				//console.log(data['password']);
				createCookie('secret', btoa(data['username'] + ':') + password, 1); /// And session cookie
				alert('Account created !'); /// Tell user
				goTo('profile'); /// Go to his profile
			}
			else if (client.status == 409) /// If conflict
			{
				alert('Username already taken.'); /// Tell the user
			}
			else
			{
				alert('Error ' + client.status);
			}
		}
	}
}
