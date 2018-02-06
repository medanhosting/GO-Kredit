/*
	=====================================================================
	Ajax
	=====================================================================
	Version		: 0.1
	Author 		: Budi
	Requirement : jQuery
	if using webpack import jQuery

	Documentation:
	
	I. define ajax event action
	
	1. defineOnSuccess
		definition: A function to be called if the request succeeds.
		data: ajax response
		usage: 
			ajax.defineOnSuccess(function(response){
				// your code here
				console.log(response);
			});

	2. defineOnError
		definition: A function to be called if the request fails.
		data: ajax response
		usage: 
			ajax.defineOnError(function(response){
				// your code here
				console.log(response);
			});

	3. defineOnComplete
		definition: A function to be called when the request finishes
					(after success and error are executed)
		data: ajax response
		usage: 
			ajax.defineOnComplete(function(response){
				// your code here
				console.log(response);
			});

	II. Ajax call
	1. Get
		definition: ajax call using GET method
		parameter: url
		usage:
			ajax.get(YOUR URL);

	2. Post
		definition: ajax call using POST method
		parameter: url, data
		usage:
			ajax.get(YOUR URL, YOUR DATA);
	

*/

/*
  	prototype using promise
	------------------------------------------------------

	var ajax = function(){

	  this.get = function(url){
	      return $.ajax({
	          url: url,
	          type: 'GET',
	          dataType: 'json'
	      });
	  }
	}

	var success = function( resp ) {
	  console.log( resp );
	};

	var err = function( req, status, err ) {
	  console.log( err );
	};


	var qs = new ajax();
	qs.get('http://localhost:3000/ajax/akta/get/123').then( success, err ).done(function(){alert('done');});  
*/

// import jquery
import $ from 'jquery';

window.ajax = new function(){
	
	// internal declare
	var on_success;
	var on_error;
	var on_complete;

	// interface ajax actions
	this.defineOnSuccess = function(syntax){
		on_success = syntax;
	}
	this.defineOnError = function(syntax){
		on_error = syntax;
	}
	this.defineOnComplete = function(syntax){
		on_complete = syntax;
	}

	// interface ajax function
	this.get = function (url, data = null){
		send(url, data, 'GET');
	}
	this.post = function (url, data){
		send(url, data, 'POST');
	}  	

		// ajax engine
		function send(url, data, type){
			$.ajax({
				url: url,
				type: type,
				data: data,
				timeout: 5000,
				contentType: false,
				processData: false,
				dataType: 'json',
				success: on_success,
				error: on_error,
				complete: on_complete
			});
		}
}
	
// This the interface
// window.thunder.ajax = new ajax();