/***********************************************************************
 * 
 * Specific script for the header. Here because of chrome not launching
 * some onload events.
 * Will create the cookie containing the ip of the server with the 
 * address of the whole api.
 * 
 **********************************************************************/
 
var ip = '78.124.135.14'; /// Set the server ip here
/// Then set api url cookie for the application
createCookie('ip', 'http://' + ip + '/HereWeCode/index.php/rest/api/v0.1/', 1);
var secret = getCookie('secret'); /// Get secret
var profile = $('#profile')[0];
var log = $('#log')[0];
if (secret != null) /// If user is logged in
{
	profile.value = 'Profile'; /// Change profile input value
	profile.onclick = function() { goTo('profile'); }; /// And click action
	log.value = 'Log out'; /// Same for log input
	log.onclick = function() { logOut(); };
}
else
{
	profile.value = 'Register';
	profile.onclick = function() { goTo('register'); };
	log.value = 'Log in';
	log.onclick = function() { displayLogIn(true); };
}

/***********************************************************************
 * 
 * Function to log in the user.
 * 
 **********************************************************************/

function identify()
{
	var url = 'http://' + ip + '/HereWeCode/index.php/rest/auth/session'; /// Set url
	var data = {};
	/// We store the username and the password
    var username = $("#username")[0].value;
    var password = $("#password")[0].value;
	
	/// We store it in an object in order to convert it in a json format
	data['username'] = username;
	data['password'] = btoa(password); /// Encrypts password in b64
	var request = JSON.stringify(data);
	var client = setClient('POST', url, false); /// Set client
	if (client != null) /// If client ok
	{
		console.log(request);
		client.send(request); /// Send request
		if (client.readyState == 4)
		{
			if (client.status == 201) /// If successful
			{
				var currentUser = client.responseText; /// Get infos
				createCookie('secret', btoa(username + ':' + password), 1); /// Create login cookie
				createCookie('currentUser', currentUser, 1); /// Save user infos
				//console.log(client.responseText);
				alert('Welcome ' + username);
				goTo('index'); /// Go to index page
			}
			else if (client.status == 401)
			{
				alert("Invalid credentials."); /// User made a mistake
			}
			else
			{
				alert("Error " + client.status);
			}
		}
	}
}

/***********************************************************************
 * 
 * This function implements the connection to a client through a 
 * XMLHttpRequest and manages the settings of the request headers
 * 
 * In  : method - Request method (POST, GET, PUT, DELETE)
 *       url - url of the request
 * 		 secure - Tell if connexion has to be secure
 * 
 * Out : client - The XMLHttpRequest when it's done and ready to use
 * 
 **********************************************************************/

function setClient(method, url, secure)
{
	var client = null;
	/// We get the user's informations
	var secret = getCookie('secret');
	if (!secure || (secure && (secret != null)))
	{
		/// We declare a new XMLHttpRequest to send our datas
		client = new XMLHttpRequest();
		/// We open the client with the method and the url
		client.open(method, url, false);
		/// We set the content-type header to JSON
		client.setRequestHeader('Content-Type', 'application/json');
		if (secure) /// If user has to be logged in
		{
			/// We set the authorization header to Basic HTML and append the login in 64
			client.setRequestHeader('Authorization', 'Basic ' + secret);
		}
	}
	/// And then we return the client which is ready to go (Solveig)
	return client;
}
