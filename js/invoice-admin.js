jQuery(document).ready(function($){
   $.fn.ontouch = function(fnc) {
	   $(this).each(function(){
			$(this).change(fnc);
			$(this).keyup(fnc);
		   
	   });
	  return this;
   }; 
	if ($('.countTrigger').length)
	{
		// $('.countTrigger').change(invoicePageAdminCountProc);
		$('.countTrigger').ontouch(invoicePageAdminCountProc);
	}

	function invoicePageAdminCountProc()
	{
		var total = 0;
		$('input.proceduresBox:checked').each(function(){
			total += parseInt($(this).attr('data-price-usd'));
		});	
		
		total += (!isNaN(parseInt($('.specific_procedure_invoices_price').val()))) ? parseInt($('.specific_procedure_invoices_price').val()) : 0;
		console.log(parseFloat($('.exchange_rate_ameriabank').text));
		$('.invoice_procedures_total_amd').val(total*parseFloat($('.exchange_rate_ameriabank').text().replace(',', '.')));	
		$('.invoice_procedures_total').val(total);	
	}
	

});