require('./ux/dynamicSelect.js');
require('./ux/noEnter.js');
require('./ux/number_format.js');

/**
 * Setfocus 
 */
$('form .set-focus').focus();

/**
 * Clickable row in table
 */
$('.table > tbody > tr').on('click', function(){
	if ($(this).attr('href'))
	{
		window.location = $(this).attr('href');
	}
});
$('.table a, .table button').on('click', function(e){
	e.stopPropagation();
});