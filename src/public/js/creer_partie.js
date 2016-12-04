function creer_partie() {
	$(document).ready(function(){
		$('#monForm').on('submit', function(e) {
			//Empeche le comportement par défaut
			e.preventDefault();

			//recupere les valeurs
			var joueurs_max = $('#joueurs_maximum').val();
			var nom_joueur = $('#nom_joueur').val();
			$.ajax({
				url : "/creer_partie/" + joueurs_max + "+" + nom_joueur,
				type: 'post',
				dataType: 'json',
				success: function(json) {
					if(json.status_nom === ''){
						$("#nom_joueur_erreur").text("");
						console.log("mlmpo");
						window.location.replace('/partie/attente');
					}else{
						$("#nom_joueur_erreur").text(json.status_nom);
					}
				}
			});
			
		});
	});
}


$(function() {
    creer_partie();
});
