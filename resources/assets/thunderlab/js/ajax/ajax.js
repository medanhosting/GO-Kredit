/*
	=====================================================================
	Ajax
	=====================================================================
	Version		: 0.3
	Author 		: Budi
	Requirement : jQuery
	if using webpack import jQuery
*/

window.ajax = new function () {

    // debug?
    const debug =  true;

    // internal declare
    var on_success;
    var on_error;
    var xhr;

    // interface ajax actions
    this.defineOnSuccess = function (syntax) {
        on_success = syntax;
    }
    this.defineOnError = function (syntax) {
        on_error = syntax;
    }

    // interface ajax function
    this.get = function (url, data, token) {
        console.log('yes');
        console.log(data);
        if (data) {
            console.log(1);
            send(url + "?" + jsonToQueryString(data), null, 'GET', token);
            console.log(jsonToQueryString(data));
        } else {
            console.log(2);
            send(url, null, 'GET', token);
        }
    }
    this.post = function (url, data, token) {
        send(url, data, 'POST', token);
    }
    this.cancel = function () {
        xhr.abort();
    }
    function jsonToQueryString(obj) {
        var str = [];
        for (var p in obj)
            if (obj.hasOwnProperty(p)) {
                if (obj[p] || obj[p] == 0) {
                    if (Object.prototype.toString.call(obj[p]) === '[object Array]') {
                        Object.keys(obj[p]).forEach(function (key) {
                            str.push(encodeURIComponent(p) + "[" + key + "]=" + encodeURIComponent(obj[p][key]));
                        });
                    } else {
                        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                    }
                }
            }
        return str.join("&");
    }

    // core
    function validateResult(resp) {
        if (debug == true) {
            console.log(resp);
        }
        try{
            // set data
            if (resp.status == 1) {
                var model = {
                    status: resp.status,
                    data: resp.data,
                    error: null,
                };
                on_success(model);
            } else {
                var model;
                if (Object.prototype.toString.call(resp.error.message) == '[object Array]' || Object.prototype.toString.call(resp.error.message) == '[object Object]') {
                    model = {
                        status: resp.status,
                        data: resp.data,
                        error: {
                            code: 204,
                            message: resp.error.message
                        }
                    };
                } else {
                    model = {
                        status: resp.status,
                        data: resp.data,
                        error: {
                            code: 500,
                            message: resp.error.message
                        }
                    };
                }

                on_error(model);
            }
        } catch (ex) {
            console.log('[ERROR] ajax.js \n Modul : post/get ajax \n Function : validateResult \n Resolution: make sure retrieved data format match agreed expected format. see on ajax.js!');
            console.log(ex);
            console.log(resp);
            return false;
        }
    }
    function throwError(resp) {
        if (debug == true) {
            console.log(resp);
        }

        // check no network error
        var no_net = null;
        try {
            if (resp.responseJSON.code == "ENETUNREACH" || resp.responseJSON.code == "EHOSTUNREACH") {
                resp.status = 0;
                resp.statusText = "Can't connect";
            }
        } catch (ex) {}

        try{
            var model = {
                status: 0,
                data: null,
                error: {
                    code: $.isNumeric(resp.statusCode) ? resp.statusCode : $.isNumeric(resp.status) ? resp.status : 0,
                    message: { 0: resp.responseJSON ? resp.responseJSON.message : resp.statusText }
                }
            };
            on_error(model);
        } catch (ex) {
            console.log('[ERROR] ajax.js \n Modul : post/get ajax \n Function : validateResult \n Resolution: make sure retrieved data format match agreed expected format. see on ajax.js!');
            console.log(ex);
            console.log(resp);
            return false;
        }
    }

    // ajax engine
    function send(url, data, type, token) {
        if (debug == true) {
            console.log(url);
            console.log(data);
            console.log(token);
        }

        xhr = $.ajax({
            url: url,
            type: type,
            data: data,
            timeout: 10000,
            accept: 'application/json',
            contentType: "application/json",
            processData: false,
            dataType: 'json',
            success:  on_success,
            error: throwError,
            headers: { "Authorization": 'Bearer ' + token }
        });
	}
}


// 	Documentation:
	
// 	I. define ajax event action
	
// 	1. defineOnSuccess
// 		definition: A function to be called if the request succeeds.
// 		data: ajax response
// 		usage: 
// 			ajax.defineOnSuccess(function(response){
// 				// your code here
// 				console.log(response);
// 			});

// 	2. defineOnError
// 		definition: A function to be called if the request fails.
// 		data: ajax response
// 		usage: 
// 			ajax.defineOnError(function(response){
// 				// your code here
// 				console.log(response);
// 			});

// 	3. defineOnComplete
// 		definition: A function to be called when the request finishes
// 					(after success and error are executed)
// 		data: ajax response
// 		usage: 
// 			ajax.defineOnComplete(function(response){
// 				// your code here
// 				console.log(response);
// 			});

// 	II. Ajax call
// 	1. Get
// 		definition: ajax call using GET method
// 		parameter: url
// 		usage:
// 			ajax.get(YOUR URL);

// 	2. Post
// 		definition: ajax call using POST method
// 		parameter: url, data
// 		usage:
// 			ajax.get(YOUR URL, YOUR DATA);
	

// */

// /*
//   	prototype using promise
// 	------------------------------------------------------

// 	var ajax = function(){

// 	  this.get = function(url){
// 	      return $.ajax({
// 	          url: url,
// 	          type: 'GET',
// 	          dataType: 'json'
// 	      });
// 	  }
// 	}

// 	var success = function( resp ) {
// 	  console.log( resp );
// 	};

// 	var err = function( req, status, err ) {
// 	  console.log( err );
// 	};


// 	var qs = new ajax();
// 	qs.get('http://localhost:3000/ajax/akta/get/123').then( success, err ).done(function(){alert('done');});  
// */

// // import jquery
// import $ from 'jquery';

// window.ajax = new function(){
	
// 	// internal declare
// 	var on_success;
// 	var on_error;
// 	var on_complete;

// 	// interface ajax actions
// 	this.defineOnSuccess = function(syntax){
// 		on_success = syntax;
// 	}
// 	this.defineOnError = function(syntax){
// 		on_error = syntax;
// 	}
// 	this.defineOnComplete = function(syntax){
// 		on_complete = syntax;
// 	}

// 	// interface ajax function
// 	this.get = function (url, data = null){
// 		send(url, data, 'GET');
// 	}
// 	this.post = function (url, data){
// 		send(url, data, 'POST');
// 	}  	

// 		// ajax engine
// 		function send(url, data, type){
// 			$.ajax({
// 				url: url,
// 				type: type,
// 				data: data,
// 				timeout: 5000,
// 				contentType: false,
// 				processData: false,
// 				dataType: 'json',
// 				success: on_success,
// 				error: on_error,
// 				complete: on_complete
// 			});
// 		}
// }
	
// // This the interface
// // window.thunder.ajax = new ajax();