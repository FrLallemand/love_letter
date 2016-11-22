function attente(timestamp) {
	$.when(		
		$.ajax({
			url : window.location.href + "/joueurs_max",
			type: 'post',
			dataType: 'json'
		})
	).done(function(r1){
		$.ajax({
			url : window.location.href + "/joueurs_actuel/" + timestamp,
			type: 'post',
			dataType: 'json',
			success: function(json) {
				$("#joueurs_compteur_label").text(json.joueurs_actuel + "/" + r1.joueurs_max);	
				if(json.joueurs_actuel < r1.joueurs_max){
					attente(json.timestamp);
				}
			}
		});
	});
}


$(function() {
    attente();
});
