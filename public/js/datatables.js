/**
 *
 */
function datatable(id, language) {
    $(document).ready( function () {
        $(id).DataTable( {
            language: {
                "url": 'http://cdn.datatables.net/plug-ins/1.10.19/i18n/' + getLanguageName(language) + '.json'
            },
            dom: 'lBfrtip',
            stateSave: true,
            colReorder: true,
            buttons: [
                {
                    extend: 'copy',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'csv',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdf',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'print',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                'colvis'
            ],
        } );
    });
}

/**
 * @author Anatoly Mironov (mirontoli)
 * http://sharepointkunskap.wordpress.com
 * http://www.bool.se
 *
 * http://stackoverflow.com/questions/3605495/generate-a-list-of-localized-language-names-with-links-to-google-translate/14800384#14800384
 * http://stackoverflow.com/questions/10997128/language-name-from-iso-639-1-code-in-javascript/14800499#14800499
 *
 * using Phil Teare's answer on stackoverflow
 * http://stackoverflow.com/questions/3217492/list-of-language-codes-in-yaml-or-json/4900304#4900304
 * Just for testing only. Incorporate in your own javascript namespace
 * Example: getLanguageName("cv-RU") --> Chuvash
 */
 (function() {
  'use strict';

	/**
	 * @author Phil Teare
	 * using wikipedia data
	 */
	var isoLangs = {
		"ab":{
			"name":"Abkhaz",
		},
		"aa":{
			"name":"Afar",
		},
		"af":{
			"name":"Afrikaans",
		},
		"ak":{
			"name":"Akan",
		},
		"sq":{
			"name":"Albanian",
		},
		"am":{
			"name":"Amharic",
		},
		"ar":{
			"name":"Arabic",
		},
		"an":{
			"name":"Aragonese",
		},
		"hy":{
			"name":"Armenian",
		},
		"as":{
			"name":"Assamese",
		},
		"av":{
			"name":"Avaric",
		},
		"ae":{
			"name":"Avestan",
		},
		"ay":{
			"name":"Aymara",
		},
		"az":{
			"name":"Azerbaijani",
		},
		"bm":{
			"name":"Bambara",
		},
		"ba":{
			"name":"Bashkir",
		},
		"eu":{
			"name":"Basque",
		},
		"be":{
			"name":"Belarusian",
		},
		"bn":{
			"name":"Bengali",
		},
		"bh":{
			"name":"Bihari",
		},
		"bi":{
			"name":"Bislama",
		},
		"bs":{
			"name":"Bosnian",
		},
		"br":{
			"name":"Breton",
		},
		"bg":{
			"name":"Bulgarian",
		},
		"my":{
			"name":"Burmese",
		},
		"ca":{
			"name":"Catalan",
		},
		"ch":{
			"name":"Chamorro",
		},
		"ce":{
			"name":"Chechen",
		},
		"ny":{
			"name":"Chichewa",
		},
		"zh":{
			"name":"Chinese",
		},
		"cv":{
			"name":"Chuvash",
		},
		"kw":{
			"name":"Cornish",
		},
		"co":{
			"name":"Corsican",
		},
		"cr":{
			"name":"Cree",
		},
		"hr":{
			"name":"Croatian",
		},
		"cs":{
			"name":"Czech",
		},
		"da":{
			"name":"Danish",
		},
		"dv":{
			"name":"Divehi; Dhivehi; Maldivian;",
		},
		"nl":{
			"name":"Dutch",
		},
		"en":{
			"name":"English",
		},
		"eo":{
			"name":"Esperanto",
		},
		"et":{
			"name":"Estonian",
		},
		"ee":{
			"name":"Ewe",
		},
		"fo":{
			"name":"Faroese",
		},
		"fj":{
			"name":"Fijian",
		},
		"fi":{
			"name":"Finnish",
		},
		"fr":{
			"name":"French",
		},
		"ff":{
			"name":"Fula",
		},
		"gl":{
			"name":"Galician",
		},
		"ka":{
			"name":"Georgian",
		},
		"de":{
			"name":"German",
		},
		"el":{
			"name":"Greek",
		},
		"gn":{
			"name":"Guaraní",
		},
		"gu":{
			"name":"Gujarati",
		},
		"ht":{
			"name":"Haitian",
		},
		"ha":{
			"name":"Hausa",
		},
		"he":{
			"name":"Hebrew",
		},
		"hz":{
			"name":"Herero",
		},
		"hi":{
			"name":"Hindi",
		},
		"hu":{
			"name":"Hungarian",
		},
		"ia":{
			"name":"Interlingua",
		},
		"id":{
			"name":"Indonesian",
		},
		"ie":{
			"name":"Interlingue",
		},
		"ga":{
			"name":"Irish",
		},
		"ig":{
			"name":"Igbo",
		},
		"ik":{
			"name":"Inupiaq",
		},
		"io":{
			"name":"Ido",
		},
		"is":{
			"name":"Icelandic",
		},
		"it":{
			"name":"Italian",
		},
		"iu":{
			"name":"Inuktitut",
		},
		"ja":{
			"name":"Japanese",
		},
		"jv":{
			"name":"Javanese",
		},
		"kl":{
			"name":"Kalaallisut",
		},
		"kn":{
			"name":"Kannada",
		},
		"kr":{
			"name":"Kanuri",
		},
		"ks":{
			"name":"Kashmiri",
		},
		"kk":{
			"name":"Kazakh",
		},
		"km":{
			"name":"Khmer",
		},
		"ki":{
			"name":"Kikuyu",
		},
		"rw":{
			"name":"Kinyarwanda",
		},
		"ky":{
			"name":"Kirghiz",
		},
		"kv":{
			"name":"Komi",
		},
		"kg":{
			"name":"Kongo",
		},
		"ko":{
			"name":"Korean",
		},
		"ku":{
			"name":"Kurdish",
		},
		"kj":{
			"name":"Kwanyama",
		},
		"la":{
			"name":"Latin",
		},
		"lb":{
			"name":"Luxembourgish",
		},
		"lg":{
			"name":"Luganda",
		},
		"li":{
			"name":"Limburgish",
		},
		"ln":{
			"name":"Lingala",
		},
		"lo":{
			"name":"Lao",
		},
		"lt":{
			"name":"Lithuanian",
		},
		"lu":{
			"name":"Luba-Katanga",
		},
		"lv":{
			"name":"Latvian",
		},
		"gv":{
			"name":"Manx",
		},
		"mk":{
			"name":"Macedonian",
		},
		"mg":{
			"name":"Malagasy",
		},
		"ms":{
			"name":"Malay",
		},
		"ml":{
			"name":"Malayalam",
		},
		"mt":{
			"name":"Maltese",
		},
		"mi":{
			"name":"Māori",
		},
		"mr":{
			"name":"Marathi (Marāṭhī)",
		},
		"mh":{
			"name":"Marshallese",
		},
		"mn":{
			"name":"Mongolian",
		},
		"na":{
			"name":"Nauru",
		},
		"nv":{
			"name":"Navajo",
		},
		"nb":{
			"name":"Norwegian",
		},
		"ne":{
			"name":"Nepali",
		},
		"ng":{
			"name":"Ndonga",
		},
		"nn":{
			"name":"Nynorsk",
		},
		"no":{
			"name":"Norwegian",
		},
		"ii":{
			"name":"Nuosu",
		},
		"oc":{
			"name":"Occitan",
		},
		"oj":{
			"name":"Ojibwe",
		},
		"om":{
			"name":"Oromo",
		},
		"or":{
			"name":"Oriya",
		},
		"os":{
			"name":"Ossetian",
		},
		"pa":{
			"name":"Punjabi",
		},
		"pi":{
			"name":"Pāli",
		},
		"fa":{
			"name":"Persian",
		},
		"pl":{
			"name":"Polish",
		},
		"ps":{
			"name":"Pashto",
		},
		"pt":{
			"name":"Portuguese",
		},
		"qu":{
			"name":"Quechua",
		},
		"rm":{
			"name":"Romansh",
		},
		"rn":{
			"name":"Kirundi",
		},
		"ro":{
			"name":"Romanian",
		},
		"ru":{
			"name":"Russian",
		},
		"sa":{
			"name":"Sanskrit",
		},
		"sc":{
			"name":"Sardinian",
		},
		"sd":{
			"name":"Sindhi",
		},
		"sm":{
			"name":"Samoan",
		},
		"sg":{
			"name":"Sango",
		},
		"sr":{
			"name":"Serbian",
		},
		"gd":{
			"name":"Gaelic",
		},
		"sn":{
			"name":"Shona",
		},
		"si":{
			"name":"Sinhala",
		},
		"sk":{
			"name":"Slovak",
		},
		"sl":{
			"name":"Slovene",
		},
		"so":{
			"name":"Somali",
		},
		"es":{
			"name":"Spanish",
		},
		"su":{
			"name":"Sundanese",
		},
		"sw":{
			"name":"Swahili",
		},
		"ss":{
			"name":"Swati",
		},
		"sv":{
			"name":"Swedish",
		},
		"ta":{
			"name":"Tamil",
		},
		"te":{
			"name":"Telugu",
		},
		"tg":{
			"name":"Tajik",
		},
		"th":{
			"name":"Thai",
		},
		"ti":{
			"name":"Tigrinya",
		},
		"bo":{
			"name":"Tibetan",
		},
		"tk":{
			"name":"Turkmen",
		},
		"tl":{
			"name":"Tagalog",
		},
		"tn":{
			"name":"Tswana",
		},
		"to":{
			"name":"Tonga",
		},
		"tr":{
			"name":"Turkish",
		},
		"ts":{
			"name":"Tsonga",
		},
		"tt":{
			"name":"Tatar",
		},
		"tw":{
			"name":"Twi",
		},
		"ty":{
			"name":"Tahitian",
		},
		"ug":{
			"name":"Uighur",
		},
		"uk":{
			"name":"Ukrainian",
		},
		"ur":{
			"name":"Urdu",
		},
		"uz":{
			"name":"Uzbek",
		},
		"ve":{
			"name":"Venda",
		},
		"vi":{
			"name":"Vietnamese",
		},
		"vo":{
			"name":"Volapük",
		},
		"wa":{
			"name":"Walloon",
		},
		"cy":{
			"name":"Welsh",
		},
		"wo":{
			"name":"Wolof",
		},
		"fy":{
			"name":"Frisian",
		},
		"xh":{
			"name":"Xhosa",
		},
		"yi":{
			"name":"Yiddish",
		},
		"yo":{
			"name":"Yoruba",
		},
		"za":{
			"name":"Zhuang",
		}
	};

	var getLanguageName = function(key) {
		key = key.slice(0,2);
		var lang = isoLangs[key];
		return lang ? lang.name : undefined;
	};
	window.getLanguageName = getLanguageName;
})();