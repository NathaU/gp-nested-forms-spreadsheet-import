(function($){
$(document).ready(function(){
	
	var modals = {};
	for(var i = 0; i < CSVUpload.forms.length; i++){
		if($("#gform_"+CSVUpload.forms[i][0]).length){
			var modal = new tingle.modal({
				footer: true,
				stickyFooter: false,
				closeMethods: ['button'],
				closeLabel: "Cancel",
				cssClass: ['gpnf-modal'],
			});

			modal.setContent("<div class='gpnf-modal-header'>CSV Upload</div>"+CSVUpload.forms[i][4]);
			
			modal.csvformid = CSVUpload.forms[i][3];
			
			modal.addFooterBtn("Cancel", "tingle-btn tingle-btn--danger", function(){
				modal.close();
			});
			modal.addFooterBtn("Upload", "tingle-btn tingle-btn--primary gpnf-btn-submit", function(){
				document.getElementById("gform_"+modal.csvformid).submit();
			});
			
			$("#gform_"+CSVUpload.forms[i][0]+" .gpnf-csv-upload").on("click", function(){
				modal.open();
			});
			
			modals[CSVUpload.forms[i][2]] = modal;
		}
	}
});
})(jQuery);