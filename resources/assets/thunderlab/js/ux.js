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
$('.table-click > tbody > tr').on('click', function(){
	if ($(this).attr('href'))
	{
		window.location = $(this).attr('href');
	}
});
// $('.table a, .table button').on('click', function(e){
// 	e.stopPropagation();
// });
/**
 * ============
 * MODULE MODAL
 * ============
 */
/** 
 * Modal form parsing
 */
$('.modal-form').on('shown.bs.modal', function(e){
	try {
		var modalID = $(e.relatedTarget).attr('data-target');
		var actionModal = $(e.relatedTarget).attr('data-action');
		var contentModal = $(e.relatedTarget).attr('data-content');

		if ((typeof actionModal !== 'undefined') && (actionModal !== '')) {
			$(modalID).find('form').attr('action', actionModal);
			$(modalID).find('#body-modal').html(contentModal);
			$(modalID).find('form .set-focus').focus();
		}
	} catch(e) {
		console.log(e);
	}
});