/***********************************************************************
 * 
 * This simple function is here to go from one page to an other. (there
 * is no history because we want the application to behave like an 
 * application and not like a website, the user shouldn't think it's 
 * comfortable to browse it without the app context.
 * 
 * In  : page - Name of the page to go to.
 * 
 * Out : null
 * 
 **********************************************************************/

function goTo(page)
{
	window.location.href = page + '.html'; 
}

/***********************************************************************
 * 
 * This function is here to enable the search of the header from 
 * any page in the application.
 * 
 **********************************************************************/

function search()
{
	var query = ($("#search")[0]).value;
	createCookie('search', query, 1); /// Save the search query
	goTo('index'); /// Go to index (search) page
}

/***********************************************************************
 * 
 * Shows or hides the login popup.
 * 
 * In  : show - Show popup if true, hide if false
 * 
 * Out : null
 * 
 **********************************************************************/

function displayLogIn(show)
{
	if (show)
	{
		$("#loginBox")[0].style.display = 'inline-block';
	}
	else
	{
		$("#loginBox")[0].style.display = 'none';
	}
}
