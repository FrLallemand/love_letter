function attente(timestamp) {
	var joueurs_actuel = 0;
	var joueurs_max = 0;
	// 	$.when(
	// 		$.ajax({
	// 			url : window.location.href + "/joueurs_actuel",
	// 			type: 'get',
	// 			dataType: 'json'
	// 			// success: function(json) {
	// 			// 	joueurs_actuel = json.joueurs_actuel;
	// 			// }
	// 		}),
	// 		$.ajax({
	// 			url : window.location.href + "/joueurs_max",
	// 			type: 'get',
	// 			dataType: 'json'
	// 			// success: function(json) {
	// 			// 	joueurs_max = json.joueurs_max;
	// 			// }
	// 		})
	// 	).done(function(response1, response2){
	// 		joueurs_actuel = response1[0].joueurs_actuel;
	// 		joueurs_max = response2[0].joueurs_max;
	// 		$("#joueurs_compteur_label").text(joueurs_actuel + "/" + joueurs_max);		
	// 	});
	$.ajax({
		url : window.location.href + "/joueurs_actuel/" + timestamp,
		type: 'post',
		dataType: 'json',
		success: function(json) {
			console.log(json.joueurs_actuel + "   " + json.timestamp);
			//alert(json.timestamp.date);
			attente(json.timestamp);
		}
	});

}
