
<HTML>


<BODY>
	
	<h2 id="welcome">Welcome</h2>

	<ul id="roster">
		<li><a id = "bitly" href="http://bitly.com" onclick="manageLink('bitly')">bitly.com</a></li>
		<li><a id = "facebook" href="http://facebook.com" onclick="manageLink('facebook')">facebook.com</a></li>
		<li><a id = "amazon" href="http://amazon.com" onclick="manageLink('amazon')">amazon.com</a></li>
		<li><a id = "twitter" href="http://twitter.com" onclick="manageLink('twitter')">twitter.com</a></li>
	</ul>


<script>
	
	// ___________________________________QUESTON_ONE_____________________________________________________________________________________________________________
	// ***********************************************************************************************************************************************************
	
	
	// The script is at the bottom of the page in order to allow everything else to load first
	// NOTE: I'm assuming elements are being passed around by id. You can use name to, but id should allow the document to be cleaner
	
	// check if the class exists in an element that exists
	function hasClass(element, className)
	{
		var element_data = document.getElementById(element)
		var string_array = null;

		// check that an actual object is returned frmom getElementById
		if(element_data !== null && typeof(element_data) === 'object')
		{
			// return based on the results of the class name
			// making sure to account for multiple classes in the declaration
			string_array = element_data.className.split(' ');
			
			for(var i=0; i < string_array.length; i++)
			{
				if(string_array[i] === className)
				{
					return true;
				}
			}
			
			return false;
				
		} else {
			return false
		}
	}
	
	// NOTE: this function returns true unless the element in question does not exist
	function addClass(element, className)
	{
		var element_data = null;
				
		element_data = document.getElementById(element);
			
		// check that the element exists and only add if it does
		if(element_data !== null && typeof(element_data) === 'object')
		{
			// make sure that a class is not being added repeatedly
			if(hasClass(element, className))
			{
				// no need to re-add the class 
				return true;
			} 
			
			// this is the case where the class is added again avoiding that extra space
			if(element_data.className == false)
			{
				document.getElementById(element).className = className;
			} else {			
				document.getElementById(element).className  = document.getElementById(element).className + " " + className; 			
			}
			
		} else {
			return false;				
		}
		
		return true;
	}
	
	// again this is checking for elements by id, its removing classes without a regex
	// returns false unless the class existed to be removed and was removed
	function removeClass(element, className)
	{
		//first check that the class exists
		element_data = document.getElementById(element);
		var string_array = null; 	
		var reconstructed_classes = '';	
			
		// check that the element exists and only add if it does
		if(element_data !== null && typeof(element_data) === 'object')
		{
			if(hasClass(element, className))
			{
				// this is the case where a class can be deleted
				string_array = element_data.className.split(' ');
				
				// loop through the list of classes and remove the right one
				// NOTE a cooler version of my function would delete an array of classes, maybe if I have time
				for(var i=0; i < string_array.length; i++)
				{
					// put together a new string of classes
					if(className !== string_array[i]) 
					{
						// deal with string edge cases for displaying the classes
						if(reconstructed_classes === '')
						{
							reconstructed_classes = string_array[i];
						} else {
							reconstructed_classes = reconstructed_classes + ' ' + string_array[i]; 
							
						}
					}
				}
				
				// add ever class back in that is needed
				document.getElementById(element).className = reconstructed_classes;
				
				return true;
			} else {
				// the class does not exist to delete
				return false
			}
			
		} else {
			return false;
		}
		
	}
	
	// these are the test operators for this function		
	// call these functions to opperate across question one
	addClass('welcome', 'beauty');
	addClass('welcome', 'up');
	removeClass('welcome', 'beauty');
	
	
	// ___________________________________QUESTON_TWO_____________________________________________________________________________________________________________
	// ***********************************************************************************************************************************************************
	
	// a global variable, only because this is a small project, this should not be done in production
	var roster = [
		{first_name:"Bit", last_name:"Lee", website:"http://bitly.com"},
		{first_name:"Face", last_name:"Book", website:"http://facebook.com"},
		{first_name:"Ama", last_name:"Zon", website:"http://amazon.com"},
		{first_name:"Twit", last_name:"Ter", website:"http://twitter.com"}
	];
	
	// this is a helper function that kills the default behavior so that we can do other stuff
	function killDefault(event)
	{	
		// manage this for other browser types
		event.preventDefault();
		
	}
	
	// this function swithes the default behavior and takes arguments from the given element to check what is needed
	// NOTE: again I'm going to use ids, also, this returns false to block the default behavior
	function manageLink(element_id)
	{
		// get the eventhandler
		var element_data = document.getElementById(element_id);
		
		if(element_data !== null && typeof(element_data) === 'object')
		{
			// start by killing the default behavior
			element_data.addEventListener('click', killDefault, false);
			
			return false;
			
		} else {
			// this error case will not occur, but its there for best practice reasons
			return false;
		}
	}
	
	
		
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
</script>	

</BODY>
</HTML>



