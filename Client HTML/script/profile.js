/***********************************************************************
 * 
 * Scripts for the profile page.
 * 
 **********************************************************************/

var secret = getCookie('secret'); /// Get identification cookie
var currentUser = getCookie('currentUser'); /// Get user infos
if ((secret == null) || (secret == '')) /// If no secret cookie
{
	goTo('index'); /// Back to index
}
else /// Else
{
	/// Set username
	$("#pUsername")[0].innerHTML = '<b style="color: rgb(80, 100, 170)">' + JSON.parse(currentUser).username + '</b>';
	$("#pPicture")[0].src = JSON.parse(currentUser).picture; /// Set profile picture
	$("#pImage")[0].value = JSON.parse(currentUser).picture; /// Set profile picture url
	///console.log(currentUser);
	if (parseInt(JSON.parse(currentUser).isAdmin) == 1) /// If user is admin
	{
		$("#adminPanel")[0].style.display = 'inline-block'; /// Show access to admin panel
	}
}

/***********************************************************************
 * 
 * Function to update an user in the database
 * 
 **********************************************************************/

function updateMember()
{
	var data = {};
	var password = '';
	if ($("#nPassword1")[0].value != '') /// If password has been changed
	{
		if ($("#nPassword1")[0].value == $("#nPassword2")[0].value) /// If both passwords are equals
		{
			password = btoa($("#nPassword1")[0].value); /// Save new password
			data['password'] = password; /// And store it
		}
		else /// Else
		{
			alert('Passwords don\'t match !'); /// Tell the user
			$("#nPassword1")[0].value = ''; /// Reset passwords
			$("#nPassword2")[0].value = '';
			return; /// Stop
		}
	}
	else /// Else
	{
		password = btoa((atob(secret).split(':'))[1]); /// Store old password
		data['password'] = password;
	}
	/// Store values for the request
	data['picture'] = $("#pImage")[0].value;
	var url = getCookie('ip') + 'member/' + JSON.parse(getCookie('currentUser')).idMember; /// Set uri
	var client = setClient('PUT', url, true); /// Set client
	if (client != null) /// If client ok
	{
		var request = JSON.stringify(data); /// Stringify
		//console.log('Requete : ' + request);
		client.send(request); /// Send request
		if (client.readyState == 4) /// When response is here
		{
			if (client.status == 200) /// If successful
			{
				currentUser = client.responseText; /// Get infos
				createCookie('currentUser', currentUser, 1); /// Create user cookie
				createCookie('secret', btoa(JSON.parse(currentUser).username + ':' ) + password, 1); /// And session cookie
				alert('Profile updated !'); /// Tell user
				goTo('profile'); /// Go to his profile
			}
			else
			{
				alert('Error ' + client.status);
			}
		}
	}
}
