function creer_partie() {
	$(document).ready(function(){
		$('#monForm').on('submit', function(e) {
			//Empecje le comportement par défaut
			e.preventDefault();

			//recupere les valeurs
			var joueurs_max = $('#joueurs_maximum').val();
			var nom_joueur = $('#nom_joueur').val();
			
			$.ajax({
				url : $(this).attr('action'),
				type: $(this).attr('method'),
				data: $(this).serialize(),
				dataType: 'json',
				success: function(json) {
					if(json.status_nom === ''){
						$("#nom_joueur_erreur").text("");
					}else{
						$("#nom_joueur_erreur").text(json.status_nom);
					}}
			});
			
		});
	});
}
