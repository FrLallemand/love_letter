$(function() {
	$('#join').on('submit', function(e) {
		//Empeche le comportement par défaut
		e.preventDefault();
		window.location.replace('/creer_partie');
	});
});
