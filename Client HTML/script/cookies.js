/***********************************************************************
 * 
 * This function logs the user out by deleting the cookie and asks him
 * whether he wants to leave the application or just go back to the Log
 * In page.
 * 
 **********************************************************************/

function logOut()
{
	/// We ask the user if he really wants to leave
	if (confirm('Are you sure ?'))
	{
		/// If yes, we delete the cookie containing its informations
		deleteCookie('secret');
		goTo('index'); /// And back to index
	}
}


/***********************************************************************
 * 
 * This function's purpose is to create a new cookie for the application
 * 
 * In  : inName - Name of the cookie
 *       inValue - Value of the cookie
 *       inDays - Duration of the cookie in days
 * 
 * Out : null
 * 
 **********************************************************************/

function createCookie(inName, inValue, inDays)
{
	var exDate = new Date();
	/// We set the date
	exDate.setDate(exDate.getDate() + inDays);
	/// Create the value to set
	var value = escape(inValue) + ((inDays == null) ? '' : '; expires=' + exDate.toUTCString());
	/// Add the cookie
	document.cookie = inName + '=' + value;
}


/***********************************************************************
 * 
 * This function gets a cookie's value with its name.
 * 
 * In  : inName - Name of the cookie
 * 
 * Out : value - Value of the cookie if it exists (null if not)
 * 
 **********************************************************************/

function getCookie(inName)
{
	/// We get all the cookies
	var value = document.cookie;
	/// Set the start to the index of the name of the searched cookie
	var start = value.indexOf(' ' + inName + '=');
	/// If the cookie is the first one
	if (start == -1)
	{
		/// We set start accordingly
		start = value.indexOf(inName + '=');
		/// If the cookie doesn't exist
		if (start == -1)
		{
			/// We set value to null
			value = null;
		}
	}
	/// If the cookie exist
	if (value != null)
	{
		/// We set start to after the "=" sign
		start = value.indexOf('=', start) + 1;
		/// And we set end to the ";" following the value
		var end = value.indexOf(';', start);
		/// If the cookie is the last one
		if (end == -1)
		{
			/// We set end to the length
			end = value.length;
		}
		/// And the we take the value between start and end
		value = unescape(value.substring(start, end));
	}
	/// And we return it
	if (value == 'null')
	{
		value = null;
	}
	return value;
}


/***********************************************************************
 * 
 * This function deletes a cookie by setting its expire time to a past 
 * date, making the browser deleting it
 * 
 * In  : inName - Name of the cookie
 * 
 * Out : null
 * 
 **********************************************************************/

function deleteCookie(inName)
{
	document.cookie = inName + '=null; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}
