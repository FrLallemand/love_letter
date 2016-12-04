// function get_mes_cartes(){
// 	return Promise.resolve($.ajax({
// 		url : "/partie/mes_cartes",
// 		type: 'get',
// 		dataType: 'json'
// 	}));
// }

// function get_cartes_jouees(){
// 	return Promise.resolve($.ajax({
// 		url : "/partie/cartes_jouees",
// 		type: 'get',
// 		dataType: 'json'
// 	}));
// }

function setup_adversaires(){
	$.ajax({
		url : "/partie/joueurs_max",
		type: 'get',
		dataType: 'json'
	}).done(function(json){
		var col_class = "";
		switch(json.joueurs_max){
		case 2:
			col_class = '"col-md-12"';
			break;
		case 3:
			col_class = '"col-md-6"';
			break;
		case 4:
			col_class = '"col-md-4"';
			break;
		}
		for(var i=1; i<json.joueurs_max; i++){
			var col = '<div class='+col_class+' id="adversaire_'+i+'"><label id="nom_adversaire"></label><div id="cartes_jouees_container"></div></div>';
			$('#row_adversaires').append(col);
		}
	});
}

function setup_liste_joueurs(selecteur, affiche_actuel){
	var promise = get_joueurs();
	promise.then(function(json){
		if(affiche_actuel){
			var option = '<label class="radio-inline"><input type="radio" name="radio_joueurs" value="' + json.moi.id + '">' + json.moi.nom+'</label>';
			$(selecteur).append(option);
		}
		for(var i=0; i<json.joueurs.length; i++){
			var disabled = ''
			if(json.joueurs[i].invulnerable){
				disabled = 'disabled';
			}
			option = '<label class="radio-inline"><input type="radio" name="radio_joueurs" value="' + json.joueurs[i].id + '" '+disabled +'>' + json.joueurs[i].nom+'</label>';
			$(selecteur).append(option);
		}
	});
}

function get_cartes_jouees_adversaires(){
	$.when(
		$.ajax({
			url : "/partie/cartes_jouees_adversaires",
			type: 'get',
			dataType: 'json'
		})
	).done(function(json){
		joueurs = json;
	});
	return joueurs;
}

function get_cartes(){
	return Promise.resolve(
		$.when(
			$.ajax({
				url : "/partie/mes_cartes",
				type: 'get',
				dataType: 'json',
			}),
			$.ajax({
				url : "/partie/cartes_jouees",
				type: 'get',
				dataType: 'json'
			}),
			$.ajax({
				url : "/partie/cartes_jouees_adversaires",
				type: 'get',
				dataType: 'json'
			}),
			$.ajax({
				url : "/partie/cartes_eliminees",
				type: 'get',
				dataType: 'json'
			})
		).done(function(mes_cartes, cartes_jouees, cartes_jouees_adversaires, cartes_eliminees){
			update_affichage(mes_cartes[0].mes_cartes, cartes_jouees[0].mes_cartes, cartes_jouees_adversaires[0].joueurs, cartes_eliminees[0].cartes);
		}).fail(function(){
			console.log("Error");
		})
	);
}

function get_joueurs(){
	return Promise.resolve($.ajax({
		url : "/partie/get_joueurs",
		type: 'get',
		dataType: 'json'
	}));
}

function update_affichage(mes_cartes, cartes_jouees, adversaires, cartes_eliminees){
	if(mes_cartes != undefined && cartes_jouees != undefined){
		$("#mes_cartes_container img").each(function(){
			$(this).remove();
		});
		$("#cartes_jouees_container img").each(function(){
			$(this).remove();
		});
		$("#row_adversaires cartes_jouees").each(function(){
			$(this).remove();
		});
		$("#cartes_eliminees_container img").each(function(){
			$(this).remove();
		});
		for(var i=0; i<mes_cartes.length; i++){
			var carte = '<img class="carte" src="' + mes_cartes[i].chemin_image +
				'" idcarte="' + mes_cartes[i].idcarte +
				'" id="' + mes_cartes[i].nom + '">';
 			$('#mes_cartes_container').append(carte);
		}
		for(var i=0; i<cartes_jouees.length; i++){
			var carte = '<img class="carte" src="' + cartes_jouees[i].chemin_image +
				'" idcarte="' + cartes_jouees[i].idcarte +
				'" id="' + cartes_jouees[i].nom + '">';
 			$('#cartes_jouees #cartes_jouees_container').append(carte);
		}
		for(var i=0; i<adversaires.length; i++){
			$('#adversaire_'+(i+1)+' label').text(adversaires[i].nom);
			for(var j=0; j<adversaires[i].cartes_jouees.length; j++){
				var carte = '<img class="carte" src="' + adversaires[i].cartes_jouees[j].chemin_image +
					'" idcarte="' + adversaires[i].cartes_jouees[j].idcarte + '">';
 				$('#adversaire_'+(i+1)+ ' #cartes_jouees_container').append(carte);
			}
		}
		for(var i=0; i<cartes_eliminees.length; i++){
			var carte = '<img class="carte" src="' + cartes_eliminees[i].chemin_image +
				'" idcarte="' + cartes_eliminees[i].idcarte +
				'" id="' + cartes_eliminees[i].nom + '">';
 			$('#cartes_eliminees_container').append(carte);
		}
	}
}

function attendre_mon_tour(dernier_tour) {
	toggle_selectionner_carte(false);
	toggle_jouer_carte(false);
	toggle_pioche(false);
	$.ajax({
		url : "/partie/attendre/" + dernier_tour,
		type: 'get',
		dataType: 'json',
		success: function(json) {
			$("#joueur_actuel_label").text("Tour de : " + json.joueur_actuel);
			if(json.success){
				if(json.notification){
					get_notification_suivante();
				}else{
					if(json.mon_tour){
						get_action_suivante();
					}
					else {
						attendre_mon_tour(json.tour_actuel);
					}
				}
			}
		}
	});
}


function get_notification_suivante(){
	$.ajax({
		url : "/partie/get_notification_suivante",
		type: 'get',
		dataType: 'json',
		success: function(json) {
			if(json.notification != null){
				switch(json.notification.source){
				case 'notification_baron':
					notification_baron(json.notification);
					break;
				case 'notification_roi':
					notification_roi(json.notification);
					break;
				case 'notification_prince':
					notification_prince(json.notification);
					break;
				case 'notification_fin':
					manche_finie(json.notification);
					break;
				}
			}else{
				tour_fini();
			}
		}
	});
}

function get_action_suivante(){
	$.ajax({
		url : "/partie/get_action_suivante",
		type: 'get',
		dataType: 'json',
		success: function(json) {
			switch(json.action){
			case 'piocher':
				toggle_pioche(true);
				break;
			case 'jouer':
				console.log("jouer");
				toggle_selectionner_carte(true);
				toggle_jouer_carte(true);
				break;
			case 'Garde':
				action_garde();
				break;
			case 'Prêtre':
				action_pretre();
				break;
			case 'Baron':
				action_baron();
				break;
			case 'Prince':
				action_prince();
				break;
			case 'Roi':
				action_roi();
				break;
			case 'Servante':
				action_servante();
				break;
			case 'jouer_comtesse':
				selectionner_comtesse();
				break;
			case null:
				tour_fini();
				break
			}
		}
	});
}

function manche_finie(notification){
	$('#modal_notification_fin').modal({
		backdrop: 'static',
		keyboard: false
	});
	$('#modal_notification_fin #message ').text("Le vainqueur est : " + notification.content.vainqueur + " !");
	$('#modal_notification_fin #fermer').on('click', function(){
		$('#modal_notification_fin #message ').text("");
		confirmer_notification(notification.source);
		window.location.replace('/');
	});
}

function notification_baron(notification){
	content = notification.content;
	if(content != null && content != undefined){
		var text = content.joueur_source + ' vient de jouer le baron contre vous !';
		$('#modal_notification_baron #message ').text(text);
		$('#modal_notification_baron').modal({
			backdrop: 'static',
			keyboard: false
		});
		$("#modal_notification_baron").modal('show');
		for(var i=0; i<content.cartes_joueur.length; i++){
			var carte = '<img class="carte" src="' + content.cartes_joueur[i] + '">';
			$('#modal_notification_baron #cartes_joueur').append(carte);
		}
		for(var i=0; i<content.cartes_adversaire.length; i++){
			var carte = '<img class="carte" src="' + content.cartes_adversaire[i] + '">';
			$('#modal_notification_baron #cartes_adversaire').append(carte);
		}

		$('#modal_notification_baron #feedback').text(content.message);
		$("#modal_notification_baron #fermer").on('click', function(){
			var elements = new Array('#cartes_joueur img',
									 '#cartes_adversaire img');
			clean('#modal_notification_baron', elements);
			$('#modal_notification_baron #message ').text("");
			confirmer_notification(notification.source);
		});
	}
}

function notification_prince(notification){
	content = notification.content;
	if(content != null && content != undefined){
		var text = content.joueur_source + ' vient de jouer le prince contre vous !\n Vous videz votre main et piochez automatiquement une carte.';
		$('#modal_notification_prince #message ').text(text);
		$('#modal_notification_prince').modal({
			backdrop: 'static',
			keyboard: false
		});
		$("#modal_notification_prince").modal('show');
		$('#modal_notification_prince #feedback').text(content.message);
		$("#modal_notification_prince #fermer").on('click', function(){
			$('#modal_notification_prince #message ').text("");
			confirmer_notification(notification.source);
		});
	}
}

function notification_roi(notification){
	content = notification.content;
	if(content != null && content != undefined){
		var text = content.joueur_source + ' vient de jouer le roi contre vous !\n Vous échangez votre main avec la sienne.';
		$('#modal_notification_roi #message ').text(text);
		$('#modal_notification_roi').modal({
			backdrop: 'static',
			keyboard: false
		});
		$("#modal_notification_roi").modal('show');
		$('#modal_notification_roi #feedback').text(content.message);
		$("#modal_notification_roi #fermer").on('click', function(){
			var promise = get_cartes();
			promise.then(function(){
				$('#modal_notification_roi #message ').text("");
				confirmer_notification(notification.source);
			});
		});
	}
}

function jouer_comtesse(){
	selectionner_comtesse();
	toggle_jouer_carte(true);
	var idcarte = $("#mes_cartes_container img").filter(function(){return this.id==='Comtesse';}).attr('idcarte');
	$.ajax({
		url : "/partie/jouer_comtesse/" + idcarte,
		type: 'post',
		dataType: 'json',
		success: function(json){
			var promise = get_cartes();
			promise.then(function(){
				$("#mes_cartes_container img").off("click");
				get_action_suivante();
			});
		}
	});
}

function action_garde(){
	var button_clicked = function(selector){
		var joueur = $(selector+' #joueurs input[name=radio_joueurs]:checked').val();
		var carte = $(selector+' #cartes input[name=cartes]:checked').val();

		if(joueur != undefined && carte != undefined){
			var callback = function(json){
				$(selector+' #feedback').text(json.message);
				if(json.success){
					$(selector+' #button').hide();
					$(selector+' #fermer').show();
					$(selector+' #fermer').on('click', function(){
						var elements = new Array('#joueurs label');
						clean(selector, elements);
						get_action_suivante();
					});
				}
			};
			ajax_for_action("/partie/action_garde/" + joueur + "+" + carte, callback);
		} else {
			$(selector+' #feedback').text("Erreur");
		}
	};
	setup_action_modal('#modal_action_garde', false, button_clicked);
}

function action_pretre(){
	var button_clicked = function(selector){
		var joueur = $(selector+' #joueurs input[name=radio_joueurs]:checked').val();
		if(joueur != undefined){
			var callback = function(json){
				$(selector+' #feedback').text(json.message);
				if(json.success){
					for(var i=0; i<json.cartes.length; i++){
						var carte = '<img class="carte" src="' + json.cartes[i] + '">';
						$(selector+' #cartes').append(carte);
					}

					$(selector+' #button').hide();
					$(selector+' #fermer').show();
					$(selector+' #fermer').on('click', function(){
						var elements = new Array('#joueurs label',
												 '#joueurs img');
						clean(selector, elements);
						get_action_suivante();
					});
				}
			};
			ajax_for_action("/partie/action_pretre/" + joueur, callback);
		} else {
			$(selector+' #feedback').text("Erreur");
		}
	};
	setup_action_modal('#modal_action_pretre', false, button_clicked);
}

function action_baron(){
	$('#modal_action_baron #mes_cartes').hide();
	$('#modal_action_baron #ses_cartes').hide();

	var button_clicked = function(selector){
		var joueur = $(selector+' #joueurs input[name=radio_joueurs]:checked').val();
		if(joueur != undefined){
			var callback = function(json){
				$(selector+' #feedback').text(json.message);
				if(json.success){
					$(selector+' #mes_cartes').show();
					$(selector+' #ses_cartes').show();
					$(selector+' #button').hide();
					$(selector+' #fermer').show();

					for(var i=0; i<json.cartes_joueur.length; i++){
						var carte = '<img class="carte" src="' + json.cartes_joueur[i] + '">';
						$(selector+' #cartes_joueur').append(carte);
					}
					for(var i=0; i<json.cartes_adversaire.length; i++){
						var carte = '<img class="carte" src="' + json.cartes_adversaire[i] + '">';
						$(selector+' #cartes_adversaire').append(carte);
					}

					$(selector+' #fermer').on('click', function(){
						var elements = new Array('#joueurs label',
												 '#joueurs div',
												 '#joueurs img',
												 '#adversaire mg');
						clean(selector, elements);
						get_action_suivante();
					});
				}
			};
			ajax_for_action("/partie/action_baron/" + joueur, callback);
		} else {
			$(selector+' #feedback').text("Erreur");
		}
	};
	setup_action_modal('#modal_action_baron', false, button_clicked);
}


function action_roi(){
	var button_clicked = function(selector){
		var joueur = $(selector+' #joueurs input[name=radio_joueurs]:checked').val();
		if(joueur != undefined){
			var callback = function(json){
				$(selector+' #feedback').text(json.message);
				if(json.success){
					$(selector+' #button').hide();
					$(selector+' #fermer').show();
					$(selector+' #fermer').on('click', function(){
						var promise = get_cartes();
						promise.then(function(){
							var elements = new Array('#joueurs label');
							clean(selector, elements);
							get_action_suivante();
						});
					});
				}
			};
			ajax_for_action("/partie/action_roi/" + joueur, callback);
		} else {
			$(selector+' #feedback').text("Erreur");
		}
	};
	setup_action_modal('#modal_action_roi', false, button_clicked);
}

function action_prince(){
	var button_clicked = function(selector){
		var joueur = $(selector+' #joueurs input[name=radio_joueurs]:checked').val();
		if(joueur != undefined){
			var callback = function(json){
				$(selector+' #feedback').text(json.message);
				if(json.success){
					$(selector+' #button').hide();
					$(selector+' #fermer').show();
					$(selector+' #fermer').on('click', function(){
						var elements = new Array('#joueurs label');
						clean(selector, elements);
						get_action_suivante();
					});
				}
			};
			ajax_for_action("/partie/action_prince/" + joueur, callback);
		} else {
			$(selector+' #feedback').text("Erreur");
		}
	};
	setup_action_modal('#modal_action_prince', true, button_clicked);
}

function confirmer_notification(source){
	$.ajax({
		url : "/partie/confirmer_notification/" + source,
		type: 'post',
		dataType: 'json',
		success: function(){
			var promise = get_cartes();
			promise.then(function(){
				get_notification_suivante();
			});
		}
	});
}

function action_servante(){
	$.ajax({
		url : "/partie/action_servante",
		type: 'post',
		dataType: 'json',
		success: function(json) {
			if(json.success){
				get_action_suivante();
			}
		}
	});
}

function ajax_for_action(action, callback){
	$.ajax({
		url : action,
		type: 'post',
		dataType: 'json',
		success: callback
	});
}

function setup_action_modal(selector, show_all_players, button_clicked){
	$(selector+' #fermer').hide();
	$(selector+' #button').show()
	$(selector+' #feedback').text("");
	setup_liste_joueurs(selector+' #joueurs', show_all_players);
	$(selector).modal({
		backdrop: 'static',
		keyboard: false
	});
	$(selector).modal('show');
	$(selector+' #button').on("click", function(){
		button_clicked(selector);
	});
}

function clean(selector, elements){
	for(var i=0; i<elements.length; i++){
		$(selector+' '+elements[i]).each(function(){
			$(this).remove();
		});
	}
}

function tour_fini(){
	attendre_mon_tour(0);
}

function elimination($carte){
	$('#elimination').modal({
		backdrop: 'static',
		keyboard: false
	});

	var message = '<div id="texte"> Vous avez pioché la ' + $carte + '. Vous êtes éliminé.</div>';
	$('#elimination_texte').append(message);
	$("#elimination_fermer").on('click', function(){
		$('#elimination_texte texte').remove();
	});
}

function piocher_carte(){
	toggle_pioche(false);

	$.ajax({
		url : "/partie/pioche_carte",
		type: 'get',
		dataType: 'json',
		success: function(json){
			if(json.success){
				var promise = get_cartes();
				promise.then(function(){
					$("#mes_cartes_container img").off("click");
					get_action_suivante();
				});
			}
		}
	});
}

function jouer_carte(){
	//$("#mes_cartes_container img").on("click");
	var idcarte = $("#mes_cartes_container img").filter(function(){return this.selected==true;}).attr('idcarte');
	toggle_jouer_carte(false);
	toggle_selectionner_carte(false);
	$.ajax({
		url : "/partie/jouer_carte/" + idcarte,
		type: 'post',
		dataType: 'json',
		success: function(json){
			var promise = get_cartes();
			promise.then(function(){
				$("#mes_cartes_container img").off("click");
				get_action_suivante();
			});
		}
	});
}

function selectionner_comtesse(){
	var comtesse = $("#mes_cartes_container img").filter(function(){return this.id==='Comtesse';});
	if(comtesse != undefined){
		$("#mes_cartes_container img").not(comtesse).each(function(){
			$(this).prop('selected', false);
			$(this).removeClass('selected');
		});
		comtesse.prop('selected', true);
		comtesse.toggleClass('selected');
		toggle_jouer_carte(true);
	}
}

function selectionner_carte(that){
	$("#mes_cartes_container img").not(that).each(function(){
		$(this).prop('selected', false);
		$(this).removeClass('selected');
	});
	that.prop('selected', true);
	that.toggleClass('selected');
	toggle_jouer_carte(true);
}

function toggle_selectionner_carte(statut){
	console.log("selection activee : " + statut);
	if(statut){
		console.log("Patapon");
		$("#mes_cartes_container img").each(function() {
			console.log($(this));
			$(this).on("click", function(){
				selectionner_carte($(this));
			});
		});
	} else {
		$("#mes_cartes_container img").each(function() {
			$(this).off("click");
		});
	}
}

function toggle_jouer_carte(statut){
	if(statut){
		if($("#mes_cartes_container img").hasClass('selected')){
			$("#jouer_carte_label").text("");
			$("#jouer_carte").prop('disabled', false);
		} else {
			$("#jouer_carte_label").text("Selectionnez une carte à jouer");
			$("#jouer_carte").prop('disabled', true);
		}
	} else {
		$("#mes_cartes_container img").each(function() {
			$(this).off("click");
		});
		$("#jouer_carte_label").text("");
		$("#jouer_carte").prop('disabled', true);
	}
}

function get_jouer_carte_statut(statut){
	return !($("#jouer_carte").prop('disabled'));
}

function toggle_pioche(statut){
	$("#pioche_carte").prop('disabled', !statut);
}

function get_pioche_statut(){
	return !($("#pioche_carte").prop('disabled'));
}

$(function() {
	$("#pioche_carte").click(piocher_carte);
	$("#jouer_carte").click(jouer_carte);

	$("#mes_cartes_container img").each(function() {
		$(this).off("click");
	});
	setup_adversaires();
	var promise = get_cartes();
	promise.then(function(){
		attendre_mon_tour(0);
	});

});
