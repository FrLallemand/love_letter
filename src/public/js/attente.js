function attente(timestamp) {
	$.when(		
		$.ajax({
			url : "/partie/joueurs_max",
			type: 'get',
			dataType: 'json'
		})
	).done(function(r1){
		$.ajax({
			url : "/partie/joueurs_actuel/" + timestamp,
			type: 'get',
			dataType: 'json',
			success: function(json) {
				$("#joueurs_compteur_label").text(json.joueurs_actuel + "/" + r1.joueurs_max);
				if(json.joueurs_actuel < r1.joueurs_max){
					attente(json.timestamp);
				}else{
					window.location.replace('partie/plateau');
				}
			}
		});
	});
}


$(function() {
    attente();
});
