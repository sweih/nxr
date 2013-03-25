<?php

//Catalan messages for PhpDig
//by Xavier de Pedro

//'keyword' => 'translation'
$phpdig_mess = array (
'mode'          =>'mode',
'query'         =>'query',
'list_meanings' =>'� Total - lists the total number of searches for each query
� Query - lists the various keywords for each search query
� Mode - lists the "and, exact, or" search mode per query
� Links - lists the average number of links found per query
� Time - lists the most recent GMT timestamp of each query',
'with_no_results' =>'with no results',
'with_results' =>'with results',
'searches'     =>'searches',
'page'         =>'Page',
'of'           =>'of',
'to'           =>'to',
'listing'      =>'Listing',
'viewList'     =>'View List of Queries',
'one_per_line' =>'Introdueix un enlla� per l�nia',

'StopSpider'   =>'Atura l\'aranya',
'id'           =>'ID',
'url'          =>'URL',
'days'         =>'Dies',
'links'        =>'Enlla�os',
'depth'        =>'Fond�ria',
'viewRSS'      =>'Visualitza RSS per a aquesta Plana',
'powered_by'   =>'Potenciat per PhpDig',
'searchall'    =>'Cerca Totes',
'wait'         =>'Espera... ',
'done'         =>'Fet!',
'limit'        =>'Limita',
'manage'       =>'Aqu� pots gestionar:',
'dayscron'     =>'- el n�mero de <b>dies</b> que crontab espera per reindexar (0 = ignora)',
'links_mean'   =>'- el n�mero m�xim d\'<b>enlla�os</b> per fond�ria per lloc (0 = il�limitat)',
'depth_mean'   =>'- la <b>fond�ria</b> m�xima de cerca per lloc (0 = cap, la fond�ria multiplica els enlla�os)',
'max_found'    =>'El nombre m�xim d\'enlla�os trobat �s ((enlla�os * fond�ria) + 1) quan els enlla�os son m�s grans que zero.',
'default_vals' =>'Valors per defecte',
'use_vals_from' =>'Empra valors des de',
'table_present' =>'taula si �s present i empra <br/>valors per defecte si hi ha valors absents a la taula?',
'admin_msg_1'   =>'- Per buidar la taula tempspider clica el bot� esborra <i>sense</i> seleccionar cap lloc',
'admin_msg_2'   =>'- La fond�ria de cerca de zero intenta recollir nom�s aquella p�gina, sense tenir en compte els enlla�os per',
'admin_msg_3'   =>'- Especifica enlla�os per fond�ria al m�xim nombre d\'enlla�os per comprovar a cada fond�ria',
'admin_msg_4'   =>'- Els enlla�os per fond�ria de zero volen dir comprovar tots els enlla�os a cada fond�ria de cerca',
'admin_msg_5'   =>'- Els guions simples eliminen \'-\' planes �ndex dels llistats de planes amb fletxes blaves',
'admin_panel'   =>'Panel d\'Administraci�',

'choose_temp'  =>'Escull una plantilla',
'select_site'  =>'Selecciona un lloc on cerca',
'restart'      =>'Reinicia',
'narrow_path'  =>'Fes m�s estreta la ruta a cercar',
'upd_sites'    =>'Actualitza llocs',
'upd2'         =>'Actualitzaci� feta',
'links_per'    =>'Enlla�os per',
'yes'          =>'s�',
'no'           =>'no',
'delete'       =>'eliminar',
'reindex'      =>'Reindexar',
'back'         =>'Enrere',
'files'        =>'arxius',
'admin'        =>'Administraci�',
'warning'      =>'Advert�ncia!',
'index_uri'    =>'Quina URI desitja indexar?',
'spider_depth' =>'Profunditat de la cerca',
'spider_warn'  =>'Si su plau, aseguri\'s que ning� m�s estigui actualitzant aquest mateix lloc.
S\'inclour� un mecanisme de bloqueig en properes versions',
'site_update'  =>'Actualitzar un lloc o una de les seves ramificacions',
'clean'        =>'Netejar',
't_index'      =>'�ndex',
't_dic'        =>'diccionari',
't_stopw'      =>'paraules comuns',
't_dash'       =>'guions',

'update'       =>'Actualitzar',
'exclude'      =>'Esborra i exclou la branca',
'excludes'     =>'Exclou les rutes',
'tree_found'   =>'Arbre trobat',
'update_mess'  =>'Reindexar o esborrar un arbre ',
'update_warn'  =>'L\'exclusi� �s permanent',
'update_help'  =>'Faci clic en l\'aspa per a esborrar la ramificaci�
Faci clic en el bot� verd per actualitzar
Faci clic en el bot� Prohibit_El_Pas per excloure de futurs indexats',
'branch_start' =>'Seleccioni la carpeta per mostrar-la en el costat esquerra',
'branch_help1' =>'Seleccioni els documents per actualitzar-los',
'branch_help2' =>'Faci clic en l\'aspa per eliminar un document
Faci clic en el bot� verd per reindexar
La fletxa inicia una recol�lecci�',
'redepth'      =>'Graus de profunditat',
'branch_warn'  =>'L\'eliminaci� �s permanent',
'to_admin'     =>'a la p�gina d\'administraci�',
'to_update'    =>'a la interf�cie d\'actualitzaci�',

'search'       =>'Cerca',
'results'      =>'resultats',
'display'      =>'Mostrar',
'w_begin'      =>'A l\'inici',
'w_whole'      =>'Paraules exactes',
'w_part'       =>'En qualsevol lloc',
'alt_try'      =>'Volies dir',

'limit_to'     =>'Limitar a',
'this_path'    =>'aquesta ruta',
'total'        =>'total',
'seconds'      =>'segons',
'w_common_sing'     =>'la paraula com� ha estat obviada.',
'w_short_sing'      =>'la paraula curta ha estat obviada.',
'w_common_plur'     =>'les paraules comuns han estat obviades.',
'w_short_plur'      =>'les paraules curtes han estat obviades.',
's_results'    =>'resultats de la cerca',
'previous'     =>'Anterior',
'next'         =>'Seg�ent',
'on'           =>'a',

'id_start'     =>'Inicia indexat del lloc',
'id_end'       =>'Indexat complet!',
'id_recent'    =>'Ha estat recentment indexat',
'num_words'    =>'N�mero de paraules',
'time'         =>'temps',
'error'        =>'Error',
'no_spider'    =>'Col�lecta no iniciada',
'no_site'      =>'No s\'ha trobat el lloc a la base de dades',
'no_temp'      =>'No existeix l\'enlla� a la taula temporal',
'no_toindex'   =>'No hi ha res per indexar',
'double'       =>'Existeix un duplicat del document ',

'spidering'    =>'Col�lecta en progr�s',
'links_more'   =>'m�s enlla�os nous',
'level'        =>'nivell',
'links_found'  =>'enlla�os trobats',
'define_ex'    =>'Definir exclusions',
'index_all'    =>'indexar tot',

'end'          =>'fi',
'no_query'     =>'Escrigui en el requadre la paraula o la frase que desitja cercar',
'pwait'        =>'Esperi, si us plau',
'statistics'   =>'Estad�stiques',

// INSTALL
'slogan'        =>'El motor de cerca m�s petit de l\'univers: versi�',
'installation'  =>'Instal�laci�',
'instructions'  =>'Escriu aqu� els par�metres MySql. Especifica un usuari v�lid existent que pugui crear bases de dades si esculls crear o actualitzar.',
'hostname'      =>'Nom del servidor  :',
'port'          =>'Port (cap = per defecte) :',
'sock'          =>'Sock (cap = per defecte) :',
'user'          =>'Usuari :',
'password'      =>'Contrasenya :',
'phpdigdatabase' =>'Base de dades de PhpDig :',
'tablesprefix'   =>'Prefix de les taules :',
'instructions2'  =>'* opcional. Empra car�cters en min�scules, 16 car�cters m�x.',
'installdatabase' =>'Instal�la la base de dades de phpdig',
'error1'   =>'No he pogut trobar la plantilla de connexi�. ',
'error2'   =>'No he pogut escriure la plantilla de connexi�. ',
'error3'   =>'No he pogut trobar el fitxer init_db.sql. ',
'error4'   =>'No he pogut crear les taules. ',
'error5'   =>'No he pogut trobar tots els fitxers de configuraci� de la base de dades. ',
'error6'   =>'No he pogut crear la base de dades.<br />Verifica els permisos de l\'usuari. ',
'error7'   =>'No he pogut connectar amb la base de dades<br />Verifica les dades de connexi�. ',
'createdb' =>'Crea la base de dades',
'createtables' =>'Crea nom�s les taules',
'updatedb' =>'Actualitza la base de dades existent',
'existingdb' =>'Escriu nom�s els par�metres de connexi�',
// CLEANUP_ENGINE
'cleaningindex'   =>'Netejant �ndex',
'enginenotok'   =>' les refer�ncies de l\'index apuntaven a una paraula clau inexistent.',
'engineok'   =>'El motor �s coherent.',
// CLEANUP_KEYWORDS
'cleaningdictionnary'   =>'Netejant el diccionari de paraules clau',
'keywordsok'   =>'Totes les paraules clau s�n en una o m�s p�gines.',
'keywordsnotok'   =>' les paraules clau no hi eren en una p�gina com a m�nim.',
// CLEANUP_COMMON
'cleanupcommon' =>'NEtejant les paraules comuns',
'cleanuptotal' =>'Total ',
'cleaned' =>' netejat.',
'deletedfor' =>' esborrat per a ',
// INDEX ADMIN
'digthis' =>'Cava (dig) aix�!',
'databasestatus' =>'Estatus de la base de dades',
'entries' =>' Entrades ',
'updateform' =>'Actualitza des de',
'deletesite' =>'Esborra lloc',
// SPIDER
'spiderresults' =>'Resultats de la col�lecta de l\'Aranya',
// STATISTICS
'mostkeywords' =>'Paraules clau majorit�ries',
'richestpages' =>'P�gines m�s riques',
'mostterms'    =>'Termes de cerca majoritaris',
'largestresults'=>'Resultats m�s grans',
'mostempty'     =>'Majoria de cerques donant resultats buits',
'lastqueries'   =>'Darreres consultes de cerca',
'lastclicks'   =>'Darrerts clics de cerca',
'responsebyhour'=>'Temps de resposta per hora',
// UPDATE
'userpasschanged' =>'Usuari/Contrasenya canviat !',
'uri' =>'URI : ',
'change' =>'Canvia',
'root' =>'Arrel (Root)',
'pages' =>' p�gines',
'locked' => 'Bloquejat',
'unlock' => 'Desbloqueja el lloc',
'onelock' => 'Un lloc es troba bloquejat, a causa de l\'activitat de col�lecci� de l\'aranya. No pots fer aix� per ara',
// PHPDIG_FORM
'go' =>'Anar...',
// SEARCH_FUNCTION
'noresults' =>'No s\'han trobat resultats'
);
?>