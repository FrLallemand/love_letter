function attente(timestamp) {
	$.when(
		$.ajax({
			url : "/partie/joueurs_max",
			type: 'get',
			dataType: 'json'
		})
	).done(function(r1){
		$.ajax({
			url : "/partie/joueurs_presents/" + timestamp,
			type: 'get',
			dataType: 'json',
			success: function(json) {
				$("#joueurs_compteur_label").text(json.joueurs_presents + "/" + r1.joueurs_max);
				if(json.joueurs_presents < r1.joueurs_max){
					attente(json.timestamp);
				}else{
					window.location.replace('/partie/plateau');
				}
			}
		});
	});
}


$(function() {
    attente();
});
