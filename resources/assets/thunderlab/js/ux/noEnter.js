/**
 * Disable submit by pressing enter on form
 */
$('form.no-enter input[type=text], form.no-enter select, form.no-enter input[type=checkbox], form.no-enter input[type=radio]').on('keypress', function(e){
	if (e.which == 13)
	{
		e.preventDefault();
	}
});