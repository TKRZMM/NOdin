<?php
/**
 * Copyright (c) 2016 by Markus Melching (TKRZ)
 */

/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 06.01.2016
 * Time: 15:08
 *
 * Vererbungsfolge der (Basis) - Klassen:
 *  	Base									            Adam/Eva
 *  	'-> SystemConfig						            Child
 *  	   	'-> DefaultConfig					            Child
 *  			'-> Messages					            Child
 *  				'-> Debug					            Child
 * 					    '-> MySQLDB			                Child
 *  					    '-> Query		                Child
 *      					    '-> Core			        Child
 * ===>	        					|-> ConcreteClass1	    Core - Child - AnyCreature
 * 			        				|-> ...				    Core - Child - AnyCreatures
 * 				        			|-> ConcreteClass20	    Core - Child - AnyCreature
 *
 *
 */
class DBExportDimari extends Core
{

    public $gDBExportDimari = array();

    private $hCore;	            // Privates Core Objekt





    function __construct($hCore)
    {

        // Debug - Classname ausgeben?!
        $this->debugInitOnLoad('Class', $this->getClassName(false));


        // Speichere das Öffentliche hCore - Objekt zur weiteren Verwendung lokal
        $this->hCore = $hCore;


        parent::__construct();

    }    // END function __construct()





    private function getMyClassName($printOnScreen = false)
    {

        if ($printOnScreen)
            print ("<br>Ich bin Klasse: " . __CLASS__ . "<br>");

        return __CLASS__;

    }    // END function getMyClassName(...)





    function getClassName($printOnScreen = false)
    {

        $myClassNmae = $this->getMyClassName($printOnScreen);

        return $myClassNmae;

    }    // END function getClassName(...)





    // NULL - Funktion ... wird benötigt in der Action - Steuerung und dient als Platzhalter bzw. als Default - Aufruf
    function doNothing()
    {

        RETURN TRUE;

    }




    public function getExportsBaseDataDimari()
    {
        $hCore = $this->hCore;

        // Typ bekannt!
        $req_sourceTypeID   = $hCore->gCore['getGET']['subAction'];

        // System bekannt!
        $req_sourceSystemID = $hCore->gCore['getGET']['valueAction'];

        // Daten einlesen

        // Summe der Datensätze
        $query = "SELECT COUNT(*) AS sumBaseData FROM baseDataDimari WHERE 1";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows == '1'){
            $getSumBaseData = 0;
        }
        else{
            $row = $result->fetch_object();
            $getSumBaseData = $row->sumBaseData;
        }
        $hCore->gCore['baseDataInfo']['getSumBaseData'] = $getSumBaseData;
        $this->gCoreDB->free_result($result);



        // Ältester Datensatz
        $query = "SELECT lastUpdate FROM baseDataDimari WHERE 1 ORDER BY lastUpdate ASC LIMIT 1";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows == '1'){
            $getOldestBaseData = 0;
        }
        else{
            $row = $result->fetch_object();
            $getOldestBaseData = $row->lastUpdate;
        }
        $hCore->gCore['baseDataInfo']['getOldestBaseData'] = $getOldestBaseData;
        $this->gCoreDB->free_result($result);



        // Aktuellste Datensatz
        $query = "SELECT lastUpdate FROM baseDataDimari WHERE 1 ORDER BY lastUpdate DESC LIMIT 1";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows == '1'){
            $getNewestBaseData = 0;
        }
        else{
            $row = $result->fetch_object();
            $getNewestBaseData = $row->lastUpdate;
        }
        $hCore->gCore['baseDataInfo']['getNewestBaseData'] = $getNewestBaseData;
        $this->gCoreDB->free_result($result);



        // Benutzer
        $query = "SELECT userName FROM user u, baseDataDimari as b WHERE u.userID = b.userID GROUP BY u.userID";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows >= '1'){
            $userNames[] = '';
        }
        else{
            while($row = $result->fetch_object()){
                $userNames[] = $row->userName;
            }
        }
        $hCore->gCore['baseDataInfo']['userNames'] = $userNames;
        $this->gCoreDB->free_result($result);




        // Sammelkonten
        $query = "SELECT STATUSID FROM baseDataDimari WHERE 1 GROUP BY STATUSID";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows >= '1'){
            $Sammelkonten[] = '';
        }
        else{
            while($row = $result->fetch_object()){
                $Sammelkonten[] = $row->STATUSID;
            }
        }
        $hCore->gCore['baseDataInfo']['Sammelkonten'] = $Sammelkonten;
        $this->gCoreDB->free_result($result);




        // Zahlungsart
        $query = "SELECT ZAHLUNGS_ART FROM baseDataDimari WHERE 1 GROUP BY ZAHLUNGS_ART";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows >= '1'){
            $Zahlungsarten[] = '';
        }
        else{
            while($row = $result->fetch_object()){
                $Zahlungsarten[] = $row->ZAHLUNGS_ART;
            }
        }
        $hCore->gCore['baseDataInfo']['Zahlungsarten'] = $Zahlungsarten;
        $this->gCoreDB->free_result($result);

        RETURN TRUE;
    }





    // INITIAL
    public function doExportsBaseDataDimari()
    {
        echo "hier";

        // Tabellen Felder lesen
        $this->fetchDBFieldnames($_SESSION['customConfig']['Dimari']['baseDataIndexAdd']);


        // Daten aus DB lesen
        $this->readDBData();


        // Daten aufbereiten
        $this->refactorCustomerSet();

        // Datenstamm aus DB lesen wo log_baseDataImportsID = der gewaehlten ImportID ist
        //$zeilen = $this->readDatensatz();

        //$return['csv'] = $this->OBSchnittstelleDimari($zeilen);

        RETURN TRUE;
    }


    private function refactorCustomerSet()
    {
        $hCore = $this->hCore;

        // Setzte Default Array
        $this->setExpFormat();


        foreach ($this->hCore->gCore['customerSet'] as $customerCnt=>$customerKey){

            foreach ($this->hCore->gCore['defaultCustomerData'] as $keyname=>$egal){

                $tmp = '';

                if (isset($this->hCore->gCore['customerSet'][$customerCnt][$keyname]))
                    $tmp = $this->hCore->gCore['customerSet'][$customerCnt][$keyname];


                // Sonderfall Firma
                if ($keyname == 'KD_NAME1'){
                    if ( (isset($this->hCore->gCore['customerSet'][$customerCnt]['FIRMENNAME'])) && (strlen($this->hCore->gCore['customerSet'][$customerCnt]['FIRMENNAME']) > 0) ){

                        // Setzte Name 1 auf Firmenname
                        $tmp = $this->hCore->gCore['customerSet'][$customerCnt]['FIRMENNAME'];

                        // Name 2 in AP_Vorname - Feld setzen
                        if (isset($this->hCore->gCore['customerSet'][$customerCnt]['KD_NAME2']))
                            $this->hCore->gCore['customerSet'][$customerCnt]['AP_VORNAME'] = $this->hCore->gCore['customerSet'][$customerCnt]['KD_NAME2'];

                        // Name 1 in AP_Vorname - Feld setzen
                        if (isset($this->hCore->gCore['customerSet'][$customerCnt]['KD_NAME1']))
                            $this->hCore->gCore['customerSet'][$customerCnt]['AP_NACHNAME'] = $this->hCore->gCore['customerSet'][$customerCnt]['KD_NAME1'];

                        // Verhindern dass KD_NAME2 mit dem Vornamen gefüllt wird
                        $this->hCore->gCore['customerSet'][$customerCnt]['KD_NAME2'] = '';

                        $this->hCore->gCore['customerSet'][$customerCnt]['ORG_STUFE'] = 'F';

                    }
                    else {
                        // DEFAULT Keine Firma
                        $this->hCore->gCore['customerSet'][$customerCnt]['ORG_STUFE'] = 'P';
                    }
                }



                // Sonderfall Mandant_ID
                elseif ($keyname == 'MANDANT_ID'){
                    $tmp = '0';
                }


                // Sonderfall MWST
                elseif ($keyname == 'MWST'){
                    $tmp = '19';
                }



                // Sonderfall Währung
                elseif ($keyname == 'WAEHRUNG'){
                    $tmp = 'EUR';
                }


                // Sonderfall Dokument_Gruppe
                elseif ($keyname == 'DOKUMENT_GRUPPE'){
                    $tmp = '1';
                }



                // Sonderfall Strasse
                elseif ($keyname == 'KD_STRASSE'){
                    $tmp = $this->hCore->gCore['customerSet'][$customerCnt]['STREET'];

                    if (isset($this->hCore->gCore['customerSet'][$customerCnt]['HAUSNUMMER']))
                        $tmp .= ' ' . $this->hCore->gCore['customerSet'][$customerCnt]['HAUSNUMMER'];

                    if (isset($this->hCore->gCore['customerSet'][$customerCnt]['HAUSNUMMER_ZUSATZ']))
                        $tmp .= ' ' . $this->hCore->gCore['customerSet'][$customerCnt]['HAUSNUMMER_ZUSATZ'];


                }



                // Sonderfall Vertrag Nr
                elseif ($keyname == 'VETRAG_NR'){
                    if (isset($this->hCore->gCore['customerSet'][$customerCnt]['SEPA_MANDATSREFERENZ'])) {

                        // Splitten
                        $suchmuster = '/(\d+)(-)(\d+)$/';
                        $zeichenkette = $this->hCore->gCore['customerSet'][$customerCnt]['SEPA_MANDATSREFERENZ'];
                        preg_match($suchmuster, $zeichenkette, $matches);

                        if (isset($matches[3]))
                            $tmp = $matches[3];
                        else
                            $tmp = $this->hCore->gCore['customerSet'][$customerCnt]['KUNDEN_NR'];
                    }
                    else{
                        $tmp = $this->hCore->gCore['customerSet'][$customerCnt]['KUNDEN_NR'];
                    }
                }



                // Sonderfall Zahlungsart
                elseif ($keyname == 'ZAHLUNGS_ART'){
                    // 0 = Überweisung
                    if ($this->hCore->gCore['customerSet'][$customerCnt]['ZAHLUNGS_ART'] == '0')
                        $tmp = 'M';

                    // 1 = Lastschrift
                    elseif ($this->hCore->gCore['customerSet'][$customerCnt]['ZAHLUNGS_ART'] == '1')
                        $tmp = 'LB';

                    // Unbekannt ... setze auf Lastschrift
                    else
                        $tmp = 'LB';
                }



                // Sonderfall SEPA Unterschrift am
                elseif ($keyname == 'SEPA_UNTERCHIFT_AM'){
                    if (isset($this->hCore->gCore['customerSet'][$customerCnt]['SEPA_GUELTIG_AB']))
                        $tmp = $this->hCore->gCore['customerSet'][$customerCnt]['SEPA_GUELTIG_AB'];
                }




                // Sonderfall Varsandart
                elseif ($keyname == 'VERSANDART'){
                    if ($this->hCore->gCore['customerSet'][$customerCnt]['VERSANDART'] == 'Online')
                        $tmp = 'W';

                    elseif ($this->hCore->gCore['customerSet'][$customerCnt]['VERSANDART'] == 'Email')
                        $tmp = 'E';

                    elseif ($this->hCore->gCore['customerSet'][$customerCnt]['VERSANDART'] == 'Papier')
                        $tmp = 'P';
                }



                $this->hCore->gCore['newCustomerSet'][$customerCnt][$keyname] = $tmp;

            }

        }

        $emptyArray = array();
        $this->hCore->gCore['customerSet'] = $emptyArray;
    }








    private function setExpFormat()
    {

        $hCore = $this->hCore;

//        $this->hCore->gCore['defaultCustomerData'] = array( 'KD_NAME1',
//                                                            'KD_NAME2');

        $this->hCore->gCore['defaultCustomerData']['KD_NAME1']                  = '';
        $this->hCore->gCore['defaultCustomerData']['KD_NAME2']                  = '';
        $this->hCore->gCore['defaultCustomerData']['KD_NAME3']                  = '';
        $this->hCore->gCore['defaultCustomerData']['KUNDEN_NR']                 = '';

        $this->hCore->gCore['defaultCustomerData']['ORG_STUFE']                 = '';
        $this->hCore->gCore['defaultCustomerData']['KURZBEZEICHNUNG']           = '';
        $this->hCore->gCore['defaultCustomerData']['BEMERKUNG']                 = '';
        $this->hCore->gCore['defaultCustomerData']['MANDANT_ID']                = '';
        $this->hCore->gCore['defaultCustomerData']['MWST']                      = '';
        $this->hCore->gCore['defaultCustomerData']['WAEHRUNG']                  = '';
        $this->hCore->gCore['defaultCustomerData']['ORG_EINHEIT_GRUPPE_ID']     = '';
        $this->hCore->gCore['defaultCustomerData']['DOKUMENT_GRUPPE']           = '';

        $this->hCore->gCore['defaultCustomerData']['KD_ANREDE']                 = '';
        $this->hCore->gCore['defaultCustomerData']['KD_BRIEF_ANREDE']           = '';
        $this->hCore->gCore['defaultCustomerData']['KD_TITEL']                  = '';
        $this->hCore->gCore['defaultCustomerData']['KD_VORNAME']                = '';
        $this->hCore->gCore['defaultCustomerData']['KD_NACHNAME']               = '';
        $this->hCore->gCore['defaultCustomerData']['KD_STRASSE']                = '';
        $this->hCore->gCore['defaultCustomerData']['KD_PLZ']                    = '';
        $this->hCore->gCore['defaultCustomerData']['KD_ORT']                    = '';
        $this->hCore->gCore['defaultCustomerData']['KD_POSTFACH']               = '';
        $this->hCore->gCore['defaultCustomerData']['KD_PLZ_POSTFACH']           = '';
        $this->hCore->gCore['defaultCustomerData']['KD_TEL']                    = '';
        $this->hCore->gCore['defaultCustomerData']['KD_FAX']                    = '';
        $this->hCore->gCore['defaultCustomerData']['KD_MOBIL']                  = '';
        $this->hCore->gCore['defaultCustomerData']['KD_EMAIL']                  = '';
        $this->hCore->gCore['defaultCustomerData']['KD_WWW_ADRESSE']            = '';
        $this->hCore->gCore['defaultCustomerData']['KD_GEBURT_DATUM']           = '';
        $this->hCore->gCore['defaultCustomerData']['KD_ABTEILUNG_POSITION']     = '';
        $this->hCore->gCore['defaultCustomerData']['KD_ADR_BEZEICHNUNG']        = '';

        $this->hCore->gCore['defaultCustomerData']['AP_ANREDE']                 = '';
        $this->hCore->gCore['defaultCustomerData']['AP_BRIEF_ANREDE']           = '';
        $this->hCore->gCore['defaultCustomerData']['AP_TITEL']                  = '';
        $this->hCore->gCore['defaultCustomerData']['AP_VORNAME']                = '';
        $this->hCore->gCore['defaultCustomerData']['AP_NACHNAME']               = '';
        $this->hCore->gCore['defaultCustomerData']['AP_STRASSE']                = '';
        $this->hCore->gCore['defaultCustomerData']['AP_PLZ']                    = '';
        $this->hCore->gCore['defaultCustomerData']['AP_ORT']                    = '';
        $this->hCore->gCore['defaultCustomerData']['AP_POSTFACH']               = '';
        $this->hCore->gCore['defaultCustomerData']['AP_PLZ_POSTFACH']           = '';
        $this->hCore->gCore['defaultCustomerData']['AP_ABTEILUNG_POSITION']     = '';
        $this->hCore->gCore['defaultCustomerData']['AP_TEL']                    = '';
        $this->hCore->gCore['defaultCustomerData']['AP_FAX']                    = '';
        $this->hCore->gCore['defaultCustomerData']['AP_MOBIL']                  = '';
        $this->hCore->gCore['defaultCustomerData']['AP_EMAIL']                  = '';
        $this->hCore->gCore['defaultCustomerData']['AP_WWW_ADRESSE']            = '';
        $this->hCore->gCore['defaultCustomerData']['AP_GEBURT_DATUM']           = '';
        $this->hCore->gCore['defaultCustomerData']['AP_ADR_BEZEICHNUNG']        = '';

        $this->hCore->gCore['defaultCustomerData']['VETRAG_NR']                 = '';
        $this->hCore->gCore['defaultCustomerData']['KREDITINSTITUTNAME']        = '';
        $this->hCore->gCore['defaultCustomerData']['BLZ']                       = '';
        $this->hCore->gCore['defaultCustomerData']['KTONR']                     = '';
        $this->hCore->gCore['defaultCustomerData']['IBAN']                      = '';
        $this->hCore->gCore['defaultCustomerData']['BIC']                       = '';
        $this->hCore->gCore['defaultCustomerData']['ZAHLUNGS_ART']              = '';
        $this->hCore->gCore['defaultCustomerData']['INHABER_KONTO']             = '';
        $this->hCore->gCore['defaultCustomerData']['ZAHLUNGSZIEL_TAGE']         = '';

        $this->hCore->gCore['defaultCustomerData']['SEPA_MANDATSREFERENZ']      = '';
        $this->hCore->gCore['defaultCustomerData']['SEPA_UNTERCHIFT_AM']        = '';
        $this->hCore->gCore['defaultCustomerData']['SEPA_GUELTIG_AB']           = '';

        $this->hCore->gCore['defaultCustomerData']['BILLINGLAUF']               = '';
        $this->hCore->gCore['defaultCustomerData']['EGN']                       = '';
        $this->hCore->gCore['defaultCustomerData']['VERSANDART']               = '';

    }









    private function readDBData()
    {
        $hCore = $this->hCore;

        $query = "SELECT * FROM `baseDataDimari` WHERE 1 ORDER BY baseDataDimariID";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows >= '1'){
            $customerSet[] = '';
        }
        else{
            $cnt = 0;
            while($row = $result->fetch_array(MYSQLI_ASSOC)){

                foreach ($this->hCore->gCore['DBFieldnames'] as $index=>$DBFielname){
                    $customerSet[$cnt][$DBFielname] = $row[$DBFielname];
                }

                $cnt++;
            }
        }

        $hCore->gCore['customerSet'] = $customerSet;

        // $hCore->gCore['baseDataInfo']['userNames'] = $userNames;
        $this->gCoreDB->free_result($result);
    }





    // Ermittelt die Feldnamen der Datenbank
    private function fetchDBFieldnames($noFirstRowsNum=0)
    {
        $hCore = $this->hCore;

        $query = "SHOW COLUMNS FROM `baseDataDimari`";

        // Resultat der Login - Prüfung
        $result = $this->gCoreDB->query($query);

        // Betroffene Zeilen, bzw. erhaltene
        $num_rows = $this->gCoreDB->num_rows($result);


        // Keine Import Datei gefunden!
        if (!$num_rows >= '1'){

            // Breche Methode hier ab und liefere false - Wert zurück

            RETURN FALSE;
        }


        // Ergebnis in $row speichern
        $curRow = 0;
        while ($row = $result->fetch_object()){
            $curRow++;
            if ($curRow > $noFirstRowsNum)
                $this->hCore->gCore['DBFieldnames'][] =  $row->Field;;
        }

        $this->gCoreDB->free_result($result);

    }











































    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
























    function OLD_OBSchnittstelleDimari($zeilen)
    {

        $hCore = $this->hCore;

        // .csv Variable initialisieren
        $csv = "";

        // .csv Array initialisieren
        $csvA = array();

        // Kundenzähler (Durchlauf) initialisieren, den brauchen wir auch für die Summenprüfung ganz am Ende der .csv
        $cntKunden = 0;

        // Durchlaufzähler
        $cnt = 0;

        // Wenn die erste Zeile in der (import) .csv Datei überschriften sprich Feldnamen beinhaltet, hier auf TRUE setzen!
        $skipHeadline = false;

        // Fehler und Warning-Array initialisieren
        $errorArray 	= array();
        $warningArray	= array();

        // cfgSatz einlesen
        $myCfgSatz = array();
        $cfgSatz = $this->readCfgSatz();
        // TODO Wenn $cfgSatz leer ... Fehler abhandeln

        // Refferenz Array fuer Change-Index erstellen
        $refArray = array();
        $refArray = $this->generateChangeIndexCfgSatz($cfgSatz);


        // Jede Zeile der .csv Durchlaufen und dann die enthaltenen Felder auf ihre Gültigkeit prüfen
        foreach ($zeilen as $index=>$kunde){

            // Durchlaufzähler im Gegensatz zum Kundenzähler auf jeden Fall erhöhen
            $cnt++;

            // Headline in Rohdatei? Wenn ja, ueberspringe ich die erste Zeile
            if ( ($skipHeadline) && ($cntKunden == 0) ){
                $skipHeadline = FALSE;
                continue;
            }


            // Gültige Kundennummer vorhanden?
            if (trim($kunde['PERSONENKONTO']) == ""){
                continue;
            }
            else {
                $personenkonto = trim($kunde['PERSONENKONTO']);
            }

            $search = '/^(\d+)/';
            $matches = "";
            preg_match($search, $personenkonto, $matches);

            if ( (isset($matches[0])) && ($matches > 0) ){
                // gültige KdNr.
                $csvA['PERSONENKONTO'] = trim($matches[0]);
            }
            else {
                // ungültige KdNr.
                // TODO Message Fehler hier:
                $errorArray[$cnt]['000000']['PERSONENKONTO'] = 'Kein gültige Kundennummer/Personenkonto';
                continue;
            }

            /////////////////////////////////////////////////////////////////////////
            // Ab hier geht es nur weiter wenn eine gütlite Kundennummer vorliegt! //

            // Durchlauf sprich Kundenzähler erhöhen
            $cntKunden++;

            // Speicher die Index-Refferenzierung in indexKunde
            $indexKunde = $kunde;
            unset($kunde);

            // Index Nummer durch Kennung tauschen
            $kunde = $indexKunde;

            // Speicher die Kunden - Daten bevor sie "gesäubert" werden sprich strlen usw.
            $dirtKunde = $kunde;
            unset($kunde);


            // Aktuellen Kunden-Datensatz (sprich die aktuelle Reihe) zum Pr�fen geben
            $getReturn = $this->checkCustomerRowValue($cfgSatz, $dirtKunde);
            $kunde 		= $getReturn['kundenDaten'];
            if (isset($getReturn['errorArray'])){
                // TODO Message Fehlre hier:
                $errorArray[] = $getReturn['errorArray'];
            }


            // Bereinigte Daten in Export- .csv Datei f�r kVASy - System schreiben
            $csv .= $this->writeToCSVSingleCustomer($kunde);
        }

        // Prüfsumme
        $csv .= "P~";
        $csv .= $cntKunden . "~";	// Gesamtanzahl der S�tze �S� innerhalb der Datei
        $csv .= "~"; 				// Gesamtanzahl der S�tze �A� innerhalb der Datei
        $csv .= "~"; 				// Gesamtsumme aller Bruttobetr�ge der S�tze �A�
        $csv .= "~";				// Gesamtanzahl der S�tze �B� innerhalb der Datei
        $csv .= "~"; 				// Gesamtsumme aller Bruttobetr�ge der S�tze �B�
        $csv .= "~"; 				// Gesamtanzahl der S�tze �C� innerhalb der Datei
        $csv .= "~"; 				// Gesamtsumme aller Nettobetr�ge der S�tze �A�
        $csv .= "~"; 				// Gesamtsumme aller Steuerbetr�ge der S�tze �A�

        $csv .= "\r\n";

        // 		$this->simpleout($errorArray);

        // Informationen aufbereiten
        $typeIndex = array_search($hCore->gCore['getGET']['subAction'], $hCore->gCore['LNav']['ConvertTypeID']);
        $typeInfo = $hCore->gCore['LNav']['ConvertType'][$typeIndex];

        $systemIndex = array_search($hCore->gCore['getGET']['valueAction'], $hCore->gCore['LNav']['ConvertSystemID']);
        $systemInfo = $hCore->gCore['LNav']['ConvertSystem'][$systemIndex];


        // TODO Export - Verzeichnis Funktion erstellen (Dimari)

        $downloadLink = 'DimariStammdatenExport';

        // '/var/www/html/www/uploads/';
        $exportpath = $_SESSION['customConfig']['WebLinks']['MAINUPLOADPATH'];
        $storeFile = 'uploads/' . $downloadLink . '_exp.csv';
        $newDownloadLink = $_SESSION['customConfig']['WebLinks']['EXTHOMESHORT'].$storeFile;

        $fp = fopen($storeFile, 'w');
        fwrite($fp, $csv);
        fclose($fp);

        // Message Ausgabe vorebeiten
        $hCore->gCore['Messages']['Type'][]      = 'Done';
        $hCore->gCore['Messages']['Code'][]      = 'DBImport';
        $hCore->gCore['Messages']['Headline'][]  = 'DB - Export <i class="fa fa-arrow-right"></i> '.$typeInfo.' <i class="fa fa-arrow-right"></i> '.$systemInfo;
        $hCore->gCore['Messages']['Message'][]   = 'DB - Export erfolgreich!<br>Die Datei kann jetzt <a href="'.$newDownloadLink.'" class="std" target=_blank>HIER</a> heruntergeladen werden!';


        $hCore->gCore['getLeadToBodySite']          = 'includes/html/home/homeBody';    // Webseite die geladen werden soll

        return $csv;

    }	// END function OBSchnittstelleDimari(...) {





    function OLD_writeToCSVSingleCustomer($kunde)
    {

        $csv = "";
        $tilde = '~';

        $csv .= "S~";									// Satzart
        $csv .= $kunde['PERSONENKONTO'] . $tilde;		// Personenkonto
        $csv .= $kunde['NAME1_FULL'] . $tilde; 			// Name1
        $csv .= $kunde['NAME2_REST'] . $tilde; 			// Name2
        $csv .= $kunde['SAMMELKONTO'] . $tilde;			// Sammelkonto					// TODO KLAEREN: Was soll ich hier eintragen A?
        $csv .= $kunde['ZAHLART'] . $tilde;				// Zahlungsart					// TODO KLAEREN: Was soll ich hier eintragen B?
        $csv .= "~"; 									// Mandatsreferenznummer
        $csv .= "~"; 									// L�ndercode
        $csv .= $kunde['BLZ'] . $tilde; 				// BLZ
        $csv .= $kunde['BIC'] . $tilde; 				// BIC
        $csv .= $kunde['KONTONUMMER'] . $tilde; 		// Kontonummer
        $csv .= $kunde['IBAN'] . $tilde; 				// IBAN
        $csv .= $kunde['ANREDEBRIEF'] . $tilde; 		// Anrede Brief
        $csv .= $kunde['ANREDEANSCHRIFT'] . $tilde; 	// Anschrift - Anrede
        $csv .= $kunde['NAME1_FULL'] . $tilde;			// Anschrift - Name1
        $csv .= $kunde['NAME2_REST'] . $tilde;			// Anschrift - Name2
        $csv .= "~";									// Anschrift - Name3
        $csv .= "~"; 									// Anschrift - L�nderkennzeichen
        $csv .= $kunde['PLZ'] . $tilde;					// Anschrift - PLZ
        $csv .= $kunde['ORT'] . $tilde;					// Anschrift - Ort
        $csv .= $kunde['STRASSE'] . $tilde; 			// Anschrift - Stra�e
        $csv .= $kunde['HAUSNUMMER'] . $tilde; 			// Anschrift - Hausnummer
        $csv .= $kunde['HAUSNUMMERZUSATZ'] . $tilde; 	// Zusatzhausnummer
        $csv .= "~"; 									// Anschrift - Postfach
        $csv .= "~"; 									// Anschrift Name1 abw. Kontoinhaber
        $csv .= "~"; 									// Anschrift Name2 abw. Kontoinhaber
        $csv .= "~"; 									// Anschrift PLZ abw. Kontoinhaber
        $csv .= "~"; 									// Anschrift Ort abw. Kontoinhaber
        $csv .= "~"; 									// Anschrift Stra�e abw. Kontoinhaber
        $csv .= "~"; 									// Anschrift Hnr abw. Kontoinhaber
        $csv .= "~"; 									// Anschrift zus. Hnr abw. Kontoinhaber
        $csv .= $kunde['TELEFON1'] . $tilde;	 		// Telefon
        $csv .= "~"; 									// Fax
        $csv .= $kunde['EMAIL'] . $tilde;				// Email
        $csv .= "~"; 									// Aktennummer
        $csv .= "~"; 									// Sortierkennzeichen
        $csv .= "~"; 									// EG-Identnummer
        $csv .= "~"; 									// Branche
        $csv .= "~"; 									// Zahl-bed. Auftr.wes
        $csv .= "~"; 									// Preisgruppe Auftr.wes

        $csv .= "\r\n";

        RETURN $csv;

    }	// END function writeToCSV(...){





    function OLD_checkCustomerRowValue($cfgSatz, $kunde)
    {

        $newKundeData 	= array();
        $myErrorArray 	= array();
        $myMatches 		= array();


        // ANMERKUNG:
        // Das Array $cfgSatz wird aus der Datenbank gelesen
        // siehe Funktion readCfgSatz aufgerufen in der OBSchnittstelleDimari

        $indexCnt = 0;

        $newKunde = array();

        foreach ($kunde as $indexKennung=>$value){

            $pflicht 		= $cfgSatz['S'][$indexKennung]['PFLICHT'];
            $vorbedingung 	= $cfgSatz['S'][$indexKennung]['VORBEDINGUNG'];
            $maxLen 		= $cfgSatz['S'][$indexKennung]['MAXLEN'];

            $clearValue = '';

            $tmpCustomerNumber = trim($kunde['PERSONENKONTO']);

            // SAMMELKONTO? ... Hardcodet setzen
            if ($indexKennung == 'SAMMELKONTO')
                $value = $_SESSION['customConfig']['Dimari']['Sammelkonto'];

            // Sonderfall Name 2 und nicht Name 1 gegeben?
            if ($indexKennung == 'NAME1'){
                if ( (strlen($value) < 1) && (strlen($kunde['NAME2']) > 0) ){
                    $value = $kunde['NAME2'];
                }
            }


            // Pflichtfeld?
            // JA Pflichtfeld
            if ($pflicht == 'YES'){

                // Wurden überhaupt Daten übergeben?
                if (strlen($value) < 1){
                    // TODO Message Fehlre hier:
                    $myErrorArray[$tmpCustomerNumber][$indexKennung] = 'Fehlende Daten bei Pflicht-Datensatz';
                }
                else {
                    // Datenlänge ok?
                    $tmpValue = $this->initSubstrStrLen($value, $maxLen);
                    $clearValue = $tmpValue[0];
                }
            }


            // NEIN kein Pflichtfeld
            elseif ($pflicht == 'NO'){

                // Wurden überhaupt Daten übergeben?
                if (strlen($value) > 0){
                    $tmpValue = $this->initSubstrStrLen($value, $maxLen);
                    $clearValue = $tmpValue[0];
                }
            }


            // JA WENN Vorbedingung
            elseif ($pflicht == 'YESIF'){
                // Prüfen wir nach dem foreach-Durchlauf... dann haben alle bereinigten Daten zur Prüfung vorliegen
                $laterCheck[$kunde['PERSONENKONTO']][$indexKennung] = $vorbedingung;

                // Wurden überhaupt Daten übergeben?
                if (strlen($value) > 0){
                    $tmpValue = $this->initSubstrStrLen($value, $maxLen);
                    $clearValue = $tmpValue[0];
                }
            }


            else {
                // ??? Kenne die Bedingungen für Pflichtfeld nicht
            }


            // Neuen Kunden-Datensatz füllen
            $newKunde[$indexKennung] = $clearValue;

            $indexCnt++;

        }	// ENDE foreach ($kunde as $indexKennung=>$value){



        //////////////////////////////////////////////////////////////////////////////////////////////////
        // AUSNAHMEN UND MANUELLE ERSTELLUNGEN

        // NAME1 soll später Vor- und Nachname beinhalten ... ich lege die Daten in einen neuen Index
        $tmpNAME1_FULL = $newKunde['NAME1'] . " " . $newKunde['NAME2'];

        // Datenlänge ok?
        $tmpValue = $this->initSubstrStrLen($tmpNAME1_FULL, $cfgSatz['S']['NAME1']['MAXLEN']);
        $newKunde['NAME1_FULL'] = trim($tmpValue[0]);
        if (isset($tmpValue[1]))
            $newKunde['NAME2_REST'] = trim($tmpValue[1]);
        else
            $newKunde['NAME2_REST'] = '';


        // Zahlart für kVASY - eigene - Kennung passend setzen
        // Die Kennung wird in der (derzeit) defaultConfig.inc.php gesetzt
        if ($newKunde['ZAHLART'] == '1')
            $newKunde['ZAHLART'] = $_SESSION['customConfig']['Dimari']['Zahlart'][1];
        else
            $newKunde['ZAHLART'] = $_SESSION['customConfig']['Dimari']['Zahlart'][0];


        // Pflichtfeld wenn ... Abhandeln
        if (count($laterCheck) > 0){
            // 			$this->simpleout($laterCheck);
            // 			$this->simpleout($kunde);

            foreach ($laterCheck as $customerNumber=>$requireInformationArray){

                foreach ($requireInformationArray as $feldKennung=>$targetFeldKennung){

                    // Zahlart gesondert abfangen
                    if ( ($targetFeldKennung == 'ZAHLART') && ($kunde['ZAHLART'] < 1) ){
                        // Kontonummer usw. nicht pflicht
                        continue;
                    }

                    if (strlen($kunde[$targetFeldKennung]) > 0){

                        $value 				= $kunde[$feldKennung];
                        $tmpCustomerNumber 	= $customerNumber;
                        $indexKennung 		= $feldKennung;
                        $maxLen 			= $cfgSatz['S'][$indexKennung]['MAXLEN'];

                        // Wurden überhaupt Daten übergeben?
                        if (strlen($value) < 1){
                            // Kontonummer?
                            // Wenn keine Kontonummer ... aber IBAN ... dann ist das ok
                            if ( ($feldKennung == 'KONTONUMMER') && (strlen($kunde ['IBAN']) > 1) ){
                                // Alles ok, Kontonummer nicht zwingend notwendig
                            }
                            else {
                                $myErrorArray[$tmpCustomerNumber][$indexKennung] = 'Fehlende Daten bei Pflicht-Datensatz';
                            }
                        }
                        else {
                            // Datenlänge ok?
                            $tmpValue = $this->initSubstrStrLen($value, $maxLen);
                            $clearValue = $tmpValue[0];
                        }
                    }

                }

            }
        }

        $ret['kundenDaten'] = $newKunde;
        if (count($myErrorArray) > 0)
            $ret['errorArray'] = $myErrorArray;


        return $ret;

    }	// END function checkCustomerRowValue(...){





    function OLD_initSubstrStrLen($checkArray, $maxlen){

        // Array als übergebenes Argument notwendig!
        // Wenn wir kein Array erhalten haben, erstellen wir hier eine passende Übergabe!
        if (!is_array($checkArray)){

            $tmpStr = $checkArray;

            $checkArray = array();
            $checkArray[] = $tmpStr;

        }


        // Funktion soll sich selber (erneut) aufrufen?
        $recall = false;


        // Jeden Array - Eintrag pr�fen
        foreach ($checkArray as $index=>$curCheck){

            $curCheck = trim($curCheck);

            if (strlen($curCheck)>$maxlen){
                $recall = true;	// Neuer "selbst"-Aufruf notwendig

                // An substr geben
                $newArrayEntry 	= substr($curCheck, 0, $maxlen);
                $rest 			= substr($curCheck, $maxlen);

                $checkArray[$index] = trim($newArrayEntry);
                $checkArray[] 		= trim($rest);

                break;
            }

        }

        // Selbstaufruf soll durchgef�hrt werden!
        if ($recall)
            $this->initSubstrStrLen($checkArray, $maxlen);

        return $checkArray;

    }	// END function initSubstrStrLen(...){





    function OLD_generateChangeIndexCfgSatz($cfgSatz){

        foreach ($cfgSatz as $cfgSatzIndex=>$value){

            foreach ($value as $indexValue=>$egal){
                $refArray[$cfgSatzIndex][] = $indexValue;
            }

        }

        return ($refArray);
    }





    // Wie .csv einlesen.... nur gehe ich ueber die DB und habe feldnamen als array-index in zeilen
    function OLD_readDatensatz()
    {
         $cfgSatz = $this->readCfgSatz();

        // Erstelle query select
        $sel = '';
        foreach ($cfgSatz['S'] as $index=>$valueArray){
            $sel .= $index . ", ";
        }

        $sel = substr($sel, 0, -2);

        $zeilen = array();

        $query = "SELECT " . $sel . " FROM baseDataDimari WHERE 1 ORDER BY baseDataDimariID";

        $result = $this->gCoreDB->query($query);

        // Betroffene Zeilen, bzw. erhaltene
        $num_rows = $this->gCoreDB->num_rows($result);

        // Keine Import Datei gefunden!
        if (!$num_rows == '1'){

            // Breche Methode hier ab und liefere false - Wert zurück

            RETURN FALSE;
        }

        $indexCnt = 0;
        while($row = $result->fetch_assoc()){

            $zeilen[$indexCnt] = $row;

            $indexCnt++;
        }

        // Gebe DB - Speicher wieder frei
        $this->gCoreDB->free_result($result);

        return $zeilen;
    }





    function OLD_readCFGSatz() {

        $cfgSatz = array();

        $query = "SELECT c.arrayIndex,
						c.indexKennung,
						c.typKennung,
						c.value,
						sourceType.shortCut

					FROM importConditionsDimari AS c

				LEFT JOIN sourceSystem 	ON c.sourceSystemID = sourceSystem.sourceSystemID
				LEFT JOIN sourceType 	ON c.sourceTypeID 	= sourceType.sourceTypeID

				WHERE c.active = 'yes'

					AND sourceType.active 			= 'yes'
					AND sourceType.sourceTypeID = '".$this->hCore->gCore['getGET']['subAction']."'

					AND sourceSystem.active 			= 'yes'
					AND sourceSystem.sourceSystemID = '".$this->hCore->gCore['getGET']['valueAction']."'

				ORDER BY c.arrayIndex";


        // Resultat
        $result = $this->gCoreDB->query($query);

        // Betroffene Zeilen, bzw. erhaltene
        $num_rows = $this->gCoreDB->num_rows($result);

        // Keine Import Datei gefunden!
        if (!$num_rows == '1'){

            // Breche Methode hier ab und liefere false - Wert zurück

            RETURN FALSE;
        }

        // Ergebnis speichern
        while($row = $result->fetch_object()){
            // Format:
            // $cfgSatz['S']['PERSONENKONTO']['PFLICHT'] 		= 'YES';
            $cfgSatz[$row->shortCut][$row->indexKennung][$row->typKennung] = $row->value;
        }

        // Gebe DB - Speicher wieder frei
        $this->gCoreDB->free_result($result);

        return $cfgSatz;

    }	// END function readCfgSatz(...) {





}   // END class DBExportDimari extends Core
