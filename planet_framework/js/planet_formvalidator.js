/**
 * Paramètres des contrôles spécifiques à faire sur les champs usuels (autres que la présence)
 */
var commonFields = {
	code_postal : {	// Valeurs acceptées : tous les codes postaux français
		"constructParams" : { validMessage: ' ', wait: 500, onlyOnBlur: true },
		"validations" : [
		 	{"type" : Validate.Format, "params" : { pattern: /^((0[1-9])|([1-8][0-9])|(9[0-8])|(2A)|(2B))[0-9]{3}$/, failureMessage : "Code postal invalide" }}
		]
	},
    date : {	// Valeurs acceptées : dates au format dd/mm/yyyy
		"constructParams" : { validMessage: ' ', wait: 500, onlyOnSubmit: true },   // Que lors du submit pour éviter les problèmes avec les date pickers
		"validations" : [
		 	{"type" : Validate.Format, "params" : { pattern: /^(0[1-9]|[12][0-9]|3[01])[- \/.](0[1-9]|1[012])[- \/.](19|20)\d\d$/, failureMessage : "Date invalide" }}
		]
	},
	email : {	// Valeurs acceptées : toutes les adresses email syntaxiquement valides
		"constructParams" : { validMessage: ' ', wait: 500, onlyOnBlur: true },
		"validations" : [
		 	{"type" : Validate.Email, "params" : { failureMessage: "Adresse email incorrecte" }}
		]
	},
	email_confirmation : {	// Valeurs acceptées : toutes les adresses email syntaxiquement valides
		"constructParams" : { validMessage: ' ', wait: 500, onlyOnBlur: true },
		"validations" : [
		 	{"type" : Validate.Email, "params" : { failureMessage: "Adresse email incorrecte" }},
		 	{"type" : Validate.Confirmation, "params" : { match: "email", prefix: true, failureMessage: "Ne correspond pas au champ \"Email\"" }}
		]
	},
	fax : {	// Caractères acceptés : Chiffres de 0 à 9, Parenthèses, Plus / Moins (tiret), Point, Espace
		"constructParams" : { validMessage: ' ', wait: 500, onlyOnBlur: true },
		"validations" : [
		 	{"type" : Validate.Format, "params" : { pattern: /^[0-9()+-. ]{10,}$/, failureMessage : "Numéro invalide" }}
		]
	},
	pass_confirmation : {	// Le contenu de ce champ doit correspondre au contenu du champ pass_confirmation
		"constructParams" : { validMessage: ' ', wait: 500, onlyOnBlur: true },
		"validations" : [
		 	{"type" : Validate.Confirmation, "params" : { match: "pfo_pass", failureMessage : "Les mots de passe saisis sont différents" }}
		]
	},
	siret : {	// Caractères acceptés : chiffres et espaces ; Longueur : 14 à 17 caractères
		"constructParams" : { validMessage: ' ', wait: 500, onlyOnBlur: true },
		"validations" : [
			{"type" : Validate.Presence, "params" : { failureMessage : "Champ obligatoire" }},
		 	{"type" : Validate.Format, "params" : { pattern: /^([0-9]{3}( |-)?){3}[0-9]{5}$/, failureMessage : "Numéro invalide" }}
		]
	},
	telephone : {	// Caractères acceptés : Chiffres de 0 à 9, Parenthèses, Plus / Moins (tiret), Point, Espace
		"constructParams" : { validMessage: ' ', wait: 500, onlyOnBlur: true },
		"validations" : [
		 	{"type" : Validate.Format, "params" : { pattern: /^[0-9()+-. ]{10,}$/, failureMessage : "Numéro invalide" }}
		]
	}
}


/**
 * Retourne les éléments d'un formulaire qui ont un id qui commence par "pf_", "pfc_", "pfo_" ou "pfco_"
 * 
 * @param form {Node} Formulaire dont on veut récupérer les éléments
 * @param tagNames {String} Types de balises à récupérer (en l'occurrence div, input, select ou textarea)
 * @param allFieldsMandatory {Bool} Mettre à true si tous les champs sont obligatoires
 * @param customValidations {Object} [optionnel] Validations spécifiques qui prennent le pas sur toutes les autres (structure identique à commonFields)
 * 
 * @returns {Array} Pour chaque élément de formulaire, un objet qui contient les informations suivantes :
 * 						- id,
 * 						- type d'élément (div, text (input), checkbox, radio, select...)
 * 						- le nom du champ commun sur lequel on va se baser pour faire les validations le cas échéant 
 * 						- nécessité d'appliquer des validation spécifiques					
 * 						- nécessité de forcer le remplissage du champ 
 * 						ex : [{"id": "email", "type": "text", "presence": true, "commonField": "email", "hasCustomValidation": false}, ...] 
 */
function getPfFields(form, tagNames, allFieldsMandatory, customValidations)
{
	var prefixes = {"pf" : true, "pfc" : true, "pfo" : true, "pfco" : true};
	
	var pfFields = new Array();
	
	for(var j=0;j<tagNames.length;j++)
	{
		// Eléments du formulaire qui correspondent au type de tag recherché
		elts = form.getElementsByTagName(tagNames[j]);
		
		for(var i=0;i<elts.length;i++)
		{
			var prefix = getPrefix(elts[i].id);
			
			if(prefix != "" && prefixes[prefix])
			{
				// L'id de l'élément commence par l'un des préfixes reconnus
				
				var presence = false;
				
				if(allFieldsMandatory || prefix.indexOf("o") >= 0)
				{
					// Si tous les champs sont obligatoires ou si le préfixe de l'id contient un "o" le test de présence sur le champ courant sera effectué
					
					presence = true;
				}

				var fieldType = tagNames[j];
				if(tagNames[j] == "input")
					fieldType = elts[i].getAttribute("type");
				
				pfFields.push({
					"id": elts[i].id,
					"type" : fieldType,
					"commonField" : isCommonField(elts[i].id),
					"hasCustomValidation": hasCustomValidation(elts[i].id, customValidations),
					"presence": presence}
				);
			}
		}
	}
	
	return pfFields;
}

/**
 * Retourne le préfixe de l'id d'un élément de formulaire
 * 
 * @param {String} id Attribut "id" de l'élément
 * 
 * @return {String} Le préfixe de l'élément s'il en a un, une chaine de longueur 0 sinon
 */
function getPrefix(id)
{
	return id.substr(0, id.indexOf("_"));
}

/**
 * Indique si une validation personnalisée est rattachée à un champ
 * 
 * @param fieldId {String} Id du champ en question
 * @param customValidations {Object} [optionnel] Validations spécifiques qui prennent le pas sur toutes les autres (structure identique à commonFields)
 * 
 * @returns {Boolean}
 */
function hasCustomValidation(fieldId, customValidations)
{
	return (eval("customValidations." + fieldId) != undefined);
}

/**
 * Initialise automatiquement les contrôles sur les champs dont les id sont reconnus
 * 
 * @param form {Node|String} Formulaire dont on veut contrôler les champs OU son id
 * @param params {Object} Paramètres de la fonction (tous optionnels) :
 * 							- allFieldsMandatory {Bool} Lorsque ce paramètre vaut true, le remplissage de tous les champs sera vérifié, sinon seuls les champs dont le label comporte une astérisque le seront
 * 							- defaultValidInputMessage {String} Message qui s'affiche quand la saisie de l'utilisateur est valide
 * 							- defaultMandatoryFieldMessage {String} Message qui s'affiche quand un champ obligatoire n'a pas été rempli
 * 							- mandatoryFieldsMessages {Object} Messages d'erreur personnalisés pour les champs obligatoires (ex: {pf_condtions_generales : "Vous devez accepter..."})
 * 							- defaultWaitTime {int}	Temps qui s'écoule entre la dernière frappe de l'utilisateur et l'affichage du message de validation
 * 							- customValidations {Object} Validations spécifiques qui prennent le pas sur toutes les autres (structure identique à commonFields)
 * 							  ATTENTION : pour les validations personnalisées il ne faut pas utiliser un autre préfixe que "pf_"
 * 
 * @returns void
 */
function initValidation(form, params)
{
	var form = form.nodeName ? form : document.getElementById(form);	// Le paramètre "form" peut être un id ou un noeud
	var params = params == undefined ? new Object() : params;
	var allFieldsMandatory = params.allFieldsMandatory == undefined ? false : params.allFieldsMandatory;
	var defaultValidInputMessage = params.defaultValidInputMessage == undefined ? " " : params.defaultValidInputMessage;
	var defaultMandatoryFieldMessage = params.defaultMandatoryFieldMessage == undefined ? "Champ obligatoire" : params.defaultMandatoryFieldMessage;
	var mandatoryFieldsMessages = params.mandatoryFieldsMessages == undefined ? new Object() : params.mandatoryFieldsMessages;
	var defaultWaitTime = params.defaultWaitTime == undefined ? 500 : params.defaultWaitTime;
	var customValidations = params.customValidations == undefined ? new Object() : params.customValidations;
	
	if(form != undefined)
	{
		// Le formulaire "pf_contact" existe
		
		// Récupération de toutes les balises de type div, input et textarea dont l'id commence par "pf_"
		var tagNames = new Array("div", "input", "select", "textarea");
		var pfFields = getPfFields(form, tagNames, allFieldsMandatory, customValidations);
		
		// Mise en place de la validation pour chaque champ
		for(var i=0;i<pfFields.length;i++)
		{
			var currentField = pfFields[i];
			
			/*
			 * Que le champ courant fasse partie des champs usuels ou qu'on lui ait attaché des validations personnalisées, la manière de construire l'objet LiveValidation est la même
			 * Seules deux choses changent :
			 *  - le nom de l'objet où l'on va aller chercher les règles de validation et les paramètres du constructeur
			 *  - l'id du champ qui contient précisément les règles de validation et les paramètres du constructeur que l'on va appliquer au champ
			 * 
			 * On mémorise donc d'emblée le nom de l'objet et l'id du champ 
			 */
			var paramsObjectName = eval("customValidations." + currentField.id) != undefined ? "customValidations" : "commonFields";
			var validationFieldId = eval("customValidations." + currentField.id) ? currentField.id : currentField.commonField;
			
			// Construction de l'objet LiveValidation
			if(currentField.commonField || currentField.hasCustomValidation)
			{
				// Le champ courant fait partie des champs usuels OU fait l'objet de validations personnalisées
				
				// On construit l'objet LiveValidation en se basant sur les paramètres contenus dans l'objet commonFields
				eval("var val_" + currentField.id + " = new LiveValidation( '" + currentField.id + "', " + paramsObjectName + "." + validationFieldId + ".constructParams )");
			}
			else if(currentField.presence)
			{
				// Le champ courant ne fait pas partie des champs usuels MAIS est obligatoire
				// (Si le champ n'est ni usuel, ni obligatoire et qu'on ne lui a pas attaché des validations personnalisées on n'a pas l'utilité de construire cet objet)
				
				// On construit l'objet LiveValidation par défaut
				
				// Selon le type de champ il peut y avoir une manière spéficique de construire cet objet
				switch(currentField.type)
				{
					case "div":	// Bouton radio
						eval("var val_" + currentField.id + ' = new LiveValidation( "' + currentField.id + '", { validMessage: "' + defaultValidInputMessage + '", insertAfterWhatNode: document.getElementById("' + currentField.id + '").lastChild } )');	// Ajoute le message d'erreur après le dernier bouton radio du groupe
						//eval("var val_" + currentField.id + ' = new LiveValidation( "' + currentField.id + '", { validMessage: "' + defaultValidInputMessage + '" } )');	// Ajoute le message d'erreur après le div qui contient les boutons radio
						break;
						
					case "checkbox":
						eval("var val_" + currentField.id + ' = new LiveValidation( "' + currentField.id + '", { validMessage: "' + defaultValidInputMessage + '", wait: ' + defaultWaitTime + ', insertAfterWhatNode: document.getElementById("' + currentField.id + '").nextSibling.nextSibling } )');
						break;
						
					default:	// Champ texte, textarea ou select
						eval("var val_" + currentField.id + ' = new LiveValidation( "' + currentField.id + '", { validMessage: "' + defaultValidInputMessage + '", wait: ' + defaultWaitTime + ' } )');
				}
			}
			
			// Gestion de la validation de type "Presence" (champ obligatoire), ce bloc est commun aux champs usuels et non usuels
			if(currentField.presence || allFieldsMandatory)
			{
				// Le champ courant est obligatoire OU tous les champs du formulaire sont obligatoires
				
				var mandatoryFieldMessage = (mandatoryFieldsMessages[currentField.id] != undefined) ? mandatoryFieldsMessages[currentField.id] : defaultMandatoryFieldMessage;
				
				// Selon le type de champ il peut y avoir une manière spéficique d'ajouter le contrôle de présence (champ obligatoire)
				switch(currentField.type)
				{
					case "div":	// Bouton radio
						eval("val_" + currentField.id + '.add(Validate.PresenceRadio, { elementId: "' + currentField.id + '", failureMessage: "' + mandatoryFieldMessage + '"})');
						break;
						
					case "checkbox":
						eval("val_" + currentField.id + '.add(Validate.Acceptance, { failureMessage: "' + mandatoryFieldMessage + '"})');
						break;
						
					default:	// Champ texte, textarea ou select
						eval("val_" + currentField.id + '.add(Validate.Presence, { failureMessage: "' + mandatoryFieldMessage + '"})');
				}
			}
			
			// Ajout des validations spécifiques au champ courant
			if(currentField.commonField || currentField.hasCustomValidation)	// Peut être remplacé par paramsObjectName
			{
				// Si le champ courant fait partie des champs usuels OU fait l'objet de validations personnalisées
				
				for(var j=0;j<eval(paramsObjectName + "." + validationFieldId + ".validations.length");j++)
				{
					// Ajout de chacune des validations rattachées au champ courant (listées dans l'objet commonFields)
					eval("val_" + currentField.id + ".add(" + paramsObjectName + "." + validationFieldId + ".validations[j].type, " + paramsObjectName + "." + validationFieldId + ".validations[j].params)");
				}
			}
		} // fin for(var i=0;i<pfFields.length;i++)
	} // fin if(form != undefined)
}

/**
 * Indique si un champ fait partie des champs communs
 * 
 * Deux possibilités :
 *  - L'id du champ correspond exactement à l'id d'un champ commun (sans son préfixe)
 *  - L'id du champ contient l'id d'un champ commun (peu importe ce qui suit)
 * 
 * @param field	{String} Id du champ concerné
 * 
 * @returns {String|false} Le nom (id) du champ commun trouvé ou false s'il n'y a aucune correspondance
 */
function isCommonField(fieldId)
{
	var prefix = getPrefix(fieldId);
	
	if(prefix.indexOf("c") >= 0)
	{
		// Le préfixe de l'id du champ indique qu'il s'agit d'un champ commun
		
		var prefixLessFieldId = fieldId.replace(prefix + "_", "");

        if(commonFields[prefixLessFieldId] != undefined)
		{
			// L'id exact du champ a été retrouvé dans l'objet commonFields (champs usuels)
			return prefixLessFieldId;
		}
		else
		{
			// On regarde si une partie de l'id correspond à une entrée dans l'objet commonFields (champs usuels)
			
			for(var key in commonFields)
			{
				if(prefixLessFieldId.indexOf(key) >= 0)
				{
					return key;
				}
			}
		}
	}
	
	return false;
}