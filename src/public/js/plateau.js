function get_mes_cartes(){
	console.log("plop");
	$.ajax({
		url : "/partie/mes_cartes",
		type: 'get',
		dataType: 'json',
		success: function(json) {
			console.log(json.cartes);
			if(json.cartes.length >= 0 && json.cartes.length<3){
				if(json.cartes[0] != undefined){
					$('#carte_1').prop('src', json.cartes[0].chemin_image);					
				} else {					
					$('#carte_1').prop('src', "/images/Vide.jpg");					
				}

				if(json.cartes[1] != undefined){
					$('#carte_2').prop('src', json.cartes[1].chemin_image);					
				} else {					
					$('#carte_2').prop('src', "/images/Vide.jpg");					
				}

			}
		}
	});
}

function attendre_mon_tour() {
	$("#pioche_carte").prop('disabled', true);
	get_mes_cartes();
	$.ajax({
		url : "/partie/mon_tour",
		type: 'get',
		dataType: 'json',
		success: function(json) {
			if(json.mon_tour === true){
				$("#pioche_carte").prop('disabled', false);
			}
		}
	});
}

function pioche_carte(){
	//	$("#pioche_carte").prop('disabled', true);
	get_mes_cartes();
	
	$.ajax({
		url : "/partie/pioche_carte",
		type: 'get',
		dataType: 'json',
		success: function(json) {
			get_mes_cartes();
			attendre_mon_tour();
		}
	});
}

$(function() {
    attendre_mon_tour();
});
