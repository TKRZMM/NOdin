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
 * KDNummer 10348
 */
class OLD_DBExport extends Core
{

    public $gDBExport = array();

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




    public function getExportsBaseDataCentron()
    {
        $hCore = $this->hCore;

        // Typ bekannt!
        $req_sourceTypeID   = $hCore->gCore['getGET']['subAction'];

        // System bekannt!
        $req_sourceSystemID = $hCore->gCore['getGET']['valueAction'];

        // Daten einlesen

        // Summe der Datensätze
        $query = "SELECT COUNT(*) AS sumBaseData FROM baseDataCentron WHERE 1";
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
        $query = "SELECT lastUpdate FROM baseDataCentron WHERE 1 ORDER BY lastUpdate ASC LIMIT 1";
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
        $query = "SELECT lastUpdate FROM baseDataCentron WHERE 1 ORDER BY lastUpdate DESC LIMIT 1";
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
        $query = "SELECT userName FROM user u, baseDataCentron as b WHERE u.userID = b.userID GROUP BY u.userID";
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
        $query = "SELECT Sammelkonto FROM baseDataCentron WHERE 1 GROUP BY Sammelkonto";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows >= '1'){
            $Sammelkonten[] = '';
        }
        else{
            while($row = $result->fetch_object()){
                $Sammelkonten[] = $row->Sammelkonto;
            }
        }
        $hCore->gCore['baseDataInfo']['Sammelkonten'] = $Sammelkonten;
        $this->gCoreDB->free_result($result);




        // Zahlungsart
        $query = "SELECT Zahlungsart FROM baseDataCentron WHERE 1 GROUP BY Zahlungsart";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows >= '1'){
            $Zahlungsarten[] = '';
        }
        else{
            while($row = $result->fetch_object()){
                $Zahlungsarten[] = $row->Zahlungsart;
            }
        }
        $hCore->gCore['baseDataInfo']['Zahlungsarten'] = $Zahlungsarten;
        $this->gCoreDB->free_result($result);

        RETURN TRUE;
    }













    public function doExportsBaseDataCentron()
    {
        $hCore = $this->hCore;

        // Feldnamen einlesen
        $query = "SHOW COLUMNS FROM baseDataCentron";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows >= '1'){
            RETURN FALSE;
        }

        while($row = $result->fetch_object()) {
            $dbFieldnames[] = $row->Field;
        }
        $this->gCoreDB->free_result($result);






        // Stammdaten einlesen
        $query = "SELECT * FROM baseDataCentron ORDER BY Name1, Name2";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows >= '1'){
            RETURN FALSE;
        }


        $cntIndex = 0;
        while($row = $result->fetch_object()) {

            foreach ($dbFieldnames as $curFieldname){
                $hCore->gCore['csvDaten'][$cntIndex][$curFieldname] = $row->$curFieldname;  // Automatisch die Feldnamen als Variable benutzen
            }
            $cntIndex++;
        }
        $this->gCoreDB->free_result($result);





        $csv = "";

        $cnt_kunden = 0;

        foreach ($hCore->gCore['csvDaten'] as $kunde){

            $cnt_kunden++;

            $personenkonto 	= trim($kunde['Personenkonto']);	// Personenkonto sprich Kundennummer

            $tilde = '~';

            $csv .= "S~";
            $csv .= $personenkonto . $tilde;    // Personenkonto
            $csv .= $kunde['Name1'] . "~";               // Name1
            $csv .= $kunde['Name2'] . "~";               // Name2
            $csv .= $kunde['Sammelkonto']. "~";                  // Sammelkonto
            $csv .= $kunde['Zahlungsart']. "~";                      // Zahlungsart
            $csv .= "~";                        // Mandatsreferenznummer
            $csv .= "~";                        // Ländercode
            $csv .= "~";                        // BLZ
            $csv .= "~";                        // BIC
            $csv .= "~";                        // Kontonummer
            $csv .= "~";                        // IBAN
            $csv .= "~";                        // Anrede Brief
            $csv .= "~";                        // Anschrift - Anrede
            $csv .= $kunde['Anschrift_Name1'] . "~";    // Anschrift - Name1
            $csv .= $kunde['Anschrift_Name2'] . "~";    // Anschrift - Name2
            $csv .= "~";                        // Anschrift - Name3
            $csv .= "~";                        // Anschrift - Länderkennzeichen
            $csv .= $kunde['Anschrift_PLZ'] . $tilde;              // Anschrift - PLZ
            $csv .= $kunde['Anschrift_Ort'] . $tilde;              // Anschrift - Ort
            $csv .= $kunde['Anschrift_Strasse'] . "~";        // Anschrift - Straße
            $csv .= $kunde['Anschrift_Hausnummer'] . "~";          // Anschrift - Hausnummer
            $csv .= $kunde['Zusatzhausnummer'] . "~";    // Zusatzhausnummer
            $csv .= "~";                        // Anschrift - Postfach
            $csv .= "~";                        // Anschrift Name1 abw. Kontoinhaber
            $csv .= "~";                        // Anschrift Name2 abw. Kontoinhaber
            $csv .= "~";                        // Anschrift PLZ abw. Kontoinhaber
            $csv .= "~";                        // Anschrift Ort abw. Kontoinhaber
            $csv .= "~";                        // Anschrift Stra�e abw. Kontoinhaber
            $csv .= "~";                        // Anschrift Hnr abw. Kontoinhaber
            $csv .= "~";                        // Anschrift zus. Hnr abw. Kontoinhaber
            $csv .= $kunde['Telefon'] . $tilde;          // Telefon
            $csv .= "~";                        // Fax
            $csv .= $kunde['Email'] . $tilde;            // Email
            $csv .= "~";                        // Aktennummer
            $csv .= "~";                        // Sortierkennzeichen
            $csv .= "~";                        // EG-Identnummer
            $csv .= "~";                        // Branche
            $csv .= "~";                        // Zahl-bed. Auftr.wes
            $csv .= "~";                        // Preisgruppe Auftr.wes

            $csv .= "\r\n";

        }   // END foreach ($hCore->gCore['csvDaten'] as $kunde){

        // Prüfsumme
        $csv .= "P~";
        $csv .= $cnt_kunden . "~";
        $csv .= "~"; // Gesamtanzahl der Sätze "A" innerhalb der Datei
        $csv .= "~"; // Gesamtsumme aller Bruttobeträge der Sätze "A"
        $csv .= "~"; // Gesamtanzahl der Sätze "B" innerhalb der Datei
        $csv .= "~"; // Gesamtsumme aller Bruttobeträge der Sätze "B"
        $csv .= "~"; // Gesamtanzahl der Sätze "C" innerhalb der Datei
        $csv .= "~"; // Gesamtsumme aller Nettobeträge der Sätze "A"
        $csv .= "~"; // Gesamtsumme aller Steuerbeträge der Sätze "A"

        $csv .= "\r\n";


        // Informationen aufbereiten
        $typeIndex = array_search($hCore->gCore['getGET']['subAction'], $hCore->gCore['LNav']['ConvertTypeID']);
        $typeInfo = $hCore->gCore['LNav']['ConvertType'][$typeIndex];

        $systemIndex = array_search($hCore->gCore['getGET']['valueAction'], $hCore->gCore['LNav']['ConvertSystemID']);
        $systemInfo = $hCore->gCore['LNav']['ConvertSystem'][$systemIndex];


        // TODO Export - Verzeichnis Funktion erstellen (Centron)

        $downloadLink = 'CentronStammdatenExport';

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
        $hCore->gCore['Messages']['Headline'][]  = 'DB - Import <i class="fa fa-arrow-right"></i> '.$typeInfo.' <i class="fa fa-arrow-right"></i> '.$systemInfo;
        $hCore->gCore['Messages']['Message'][]   = 'DB - Import erfolgreich!<br>Die Datei kann jetzt <a href="'.$newDownloadLink.'" class="std" target=_blank>HIER</a> heruntergeladen werden!';


        $hCore->gCore['getLeadToBodySite']          = 'includes/html/home/homeBody';    // Webseite die geladen werden soll


        RETURN TRUE;

    }   // END public function doExportsBaseDataCentron()









    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // INITIAL Methode: Export Centron Buchungsdaten
    public function getExportsBookingDataCentronInitial()
    {

        $hCore = $this->hCore;

        // Initial Aufruf für Centron Buchungssatz export
        $this->doExportsBookingDataCentronInitial();


        RETURN TRUE;

    }   // END public function getExportsBookingDataCentronInitial()







    private function doExportsBookingDataCentronInitial()
    {

        $hCore = $this->hCore;

        // Benötigte Kundendaten anhand der anstehenden Rechnungen und KundenNr. ermitteln
        $this->getRelevantBaseData();

        // Buchungssatz einlesen
        $this->getBookingData();

        // Kundendaten zu Buchungssatz einlesen

        // A B C Stamm aufbauen
        $this->generateSets();

        // csv-Datei erstellen
        $this->generateBooginCSV();

        RETURN TRUE;

    }   // END private function doExportsBookingDataCentronInitial()






    // Benötigte Kundendaten anhand der anstehenden Rechnungen und KundenNr. ermitteln
    private function getRelevantBaseData()
    {
        $hCore = $this->hCore;

        // Feldnamen einlesen
        $query = "SHOW COLUMNS FROM baseDataCentron";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows >= '1'){
            RETURN FALSE;
        }

        while($row = $result->fetch_object()) {
            $dbFieldnames[] = $row->Field;
        }
        $this->gCoreDB->free_result($result);




        $query = "SELECT c.*
                    FROM bookingDataCentron as r,
                         baseDataCentron as c
                    WHERE c.Personenkonto = r.KundenNummer
                    GROUP BY r.KundenNummer
                    ORDER BY r.KundenNummer";
//        $query = "SELECT c.*
//                    FROM bookingDataCentron as r,
//                         baseDataCentron as c
//                    WHERE c.Personenkonto = r.KundenNummer
//                    AND c.Personenkonto = '10348'
//                    GROUP BY r.KundenNummer
//                    ORDER BY r.KundenNummer";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows >= '1'){
            RETURN FALSE;
        }


        $cntIndex = 0;
        while($row = $result->fetch_object()) {

            foreach ($dbFieldnames as $curFieldname){
                $hCore->gCore['CustomerData'][$row->Personenkonto][$curFieldname] = utf8_encode($row->$curFieldname);  // Automatisch die Feldnamen als Variable benutzen
            }
            $cntIndex++;
        }

        $this->gCoreDB->free_result($result);

        RETURN TRUE;

    }   // END private function getBookingData()








    // Buchungssatz einlesen
    private function getBookingData()
    {
        $hCore = $this->hCore;


        // Feldnamen einlesen
        $query = "SHOW COLUMNS FROM bookingDataCentron";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows >= '1'){
            RETURN FALSE;
        }

        while($row = $result->fetch_object()) {
            $dbFieldnames[] = $row->Field;
        }
        $this->gCoreDB->free_result($result);





        // Buchungssatz einlesen
//        $query = "SELECT * FROM bookingDataCentron WHERE KundenNummer > '10881' ORDER BY RechnungsNr, KundenNummer";
        $query = "SELECT * FROM bookingDataCentron WHERE 1 ORDER BY RechnungsNr, KundenNummer";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows >= '1'){
            RETURN FALSE;
        }


        $cntIndex = 0;
        while($row = $result->fetch_object()) {

            foreach ($dbFieldnames as $curFieldname){
                $hCore->gCore['BuchungsDaten'][$cntIndex][$curFieldname] = utf8_encode($row->$curFieldname);  // Automatisch die Feldnamen als Variable benutzen
            }
            $cntIndex++;
        }
        $this->gCoreDB->free_result($result);


        RETURN TRUE;

    }   // END private function getBookingData()










    private function generateSets()
    {

        $hCore = $this->hCore;

        // Initial Variable definieren
//        $lastCustomerNumber = 0;
        $lastBookingNumber = 0;

//        $curIndex = 0;
//        $curIndexC = 0;
//        $curIndexB = 0;
//        $curIndexA = 0;
//
//        $mainArrayIndex = 0;
//
//
//        $indexMain = 0;
//        $indexA = 0;
//        $indexB = 0;
        $indexC =0;

//        $setCnt =0;

//        $indexA = 0;
        foreach ($hCore->gCore['BuchungsDaten'] as $bookingSet) {

//            if ($bookingSet['KundenNummer'] != '10348'){
//                continue;
//            }

            $curCustomerNumber  = $bookingSet['KundenNummer'];
            $curBookingNumber   = $bookingSet['RechnungsNr'];


            // Wenn Neue Rechnungsnummer, dann neuen Rechnungssatz erstellen
            if ($curBookingNumber != $lastBookingNumber){

                // Index C resetten
                $indexC = 0;

                //2016-01-12
                preg_match_all("/(\d+)\-(\d+)\-(\d+)/i", $bookingSet['Datum'], $splitDate);
                $curBuchungsperiode = $splitDate[1][0] . '.' . $splitDate[2][0];
                $curBelegdatum      = $splitDate[1][0] . $splitDate[2][0]. $splitDate[3][0];

                $curBuchungsdatum = $curBelegdatum;

                $curZahlungsbedingungen = $_SESSION['customConfig']['Centron']['Zahlungsbedingung'];


                if (!isset($hCore->gCore['CustomerData'][$bookingSet['KundenNummer']])){
                    // Habe den Stammdatensatz nicht!
                    // Message Ausgabe vorebeiten
                    $hCore->gCore['Messages']['Type'][]      = 'Fehler';
                    $hCore->gCore['Messages']['Code'][]      = 'Error';
                    $hCore->gCore['Messages']['Headline'][]  = 'DB - Exort';
                    $hCore->gCore['Messages']['Message'][]   = 'FEHLER: fehlender Stammdatensatz KDNr.: ' . $bookingSet['KundenNummer'] . '<br>';
                    continue;
                }

                // Rechnungsnummer:
                if ($bookingSet['Brutto'] < 0){
                    $preReNummer = 'VR';
                }
                else{
                    $preReNummer = 'AR';
                }
                $tmpReNummer = sprintf("%'.010d", $curBookingNumber);
                $curReNummer = $preReNummer . $tmpReNummer;

                // Erzeuge neuen A Satz
                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Satzart'] = 'A';
                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Satzart']                    = 'A';                                      // Satzart
                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Personenkonto']              = $bookingSet['KundenNummer'];              // Personenkonto
                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Belegnummer']                = $bookingSet['KundenNummer'];              // Belegnummer
                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Rechnungsnummer']            = $curReNummer;      // Rechnungsnummer
                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Buchungsperiode']            = $curBuchungsperiode;                      // Buchungsperiode
                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Belegdatum']                 = $curBelegdatum;                           // Belegdatum
                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Buchungsdatum']              = $curBuchungsdatum;                        // Buchungsdatum
                // PFLICHT (Wird im B - Teil behandelt und gesetzt)
//                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Bruttobetrag'] = ''; // Bruttobetrag
                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Waehrung']                   = $_SESSION['customConfig']['Centron']['Waehrung'];   // Waehrung
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Skonto']                     = '';    // Skontofähiger Betrag
                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Zahlungsbedingungen']        = $curZahlungsbedingungen;    // Zahlungsbedingungen
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['ABWZahlungsart']             = '';   // Abweichende Zahlungsart
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Faelligkeit']                = '';   // Fälligkeit
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Valuta']                     = '';   // Valuta
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['PLZ']                        = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_PLZ'];      // PLZ
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Ort']                        = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Ort'];   // Ort
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Strasse']                    = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Strasse'];   // Strasse
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Hausnummer']                 = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Hausnummer'];   // Hausnummer
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Zusatzhausnummer']           = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Zusatzhausnummer'];   // Zusatzhausnummer
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Wohnungsnummer']             = '';   // Wohnungsnummer
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['ABWKontoInhaberName1']       = '';   // Abweichen-der-Kontoinhaber_Name1
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['ABWKontoInhaberName2']       = '';   // Abweichen-der-Kontoinhaber_Name2
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Laendercode']                = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Laendercode'];   // Laendercode
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['ABWBLZ']                     = '';   // BLZ abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['ABWKontoNr']                 = '';   // Konto_Nr abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['ABWIBAN']                    = '';   // IBAN abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['ABWAnschriftName1']          = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Name1_abw_Kontoinhaber'];   // Anschrift - Name 1 abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['ABWAnschriftName2']          = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Name2_abw_Kontoinhaber'];   // Anschrift - Name 2 abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['ABWAnschriftPLZ']            = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_PLZ_abw_Kontoinhaber'];   // Anschrift - PLZ abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['ABWAnschriftOrt']            = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Ort_abw_Kontoinhaber'];   // Anschrift - Ort abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['ABWAnschriftStrasse']        = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Strasse_abw_Kontoinhaber'];   // Anschrift - Strasse abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['ABWAnschriftHausNr']         = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Hnr_abw_Kontoinhaber'];   // Anschrift - HausNr. abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['ABWAnschriftHausNrZusatz']   = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_zus_Hnr_abw_Kontoinhaber'];   // Anschrift - Zus. HausNr. abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Prenotifcation']             = 'j';  // Prenotification erfolgt (J)
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['MandatsRefNr']               = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Mandatsreferenznummer'];   // Mandatsreferenz-nummer
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['AnkZahlungseinzgZum']        = '';   // Anküendigung des Zahlungseinzugs zum
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['AnkZahlungseinzgAm']         = '';   // Ankündigung des Zahlungseinzugs am
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['ABWAnkZahlungseinzg']        = '';   // Ankündigung des Zahlunseinzugs am für den abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['BuchungszeichenAvviso']      = '';   // Buchungszeichen Avviso








                // Erzeuge neuen B Satz
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['B']['Satzart']              = 'B';                            // Satzart
                //$hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['B']['Bruttobetrag']         = $bookingSet['Brutto'];   // Bruttobetrag
                //$hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['B']['Nettobetrag']        = '';                       // Nettobetrag
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['B']['Steuerkennzeichen']    = $bookingSet['MwSt'];      // Steuerkennzeichen
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['B']['Geschaeftsbereich']    = $_SESSION['customConfig']['Centron']['GeschaeftsbereichNonPrivate'];    // Geschäftsbereich


            }  //  END if ($curBookingNumber != $lastBookingNumber)





            // C Satz hinzufügen
            $brutto = $bookingSet['Brutto'];
            $brutto = $this->cleanMoney($brutto);

            $mwst = $bookingSet['MwSt'];
            $mwst = $this->cleanMoney($mwst);


            $prozentBerechnung = 100 + $mwst;
//            $curCSteuerbetrag =  $brutto * ($b/100);
            $curCSteuerbetrag =  $brutto * ($mwst/$prozentBerechnung);
            $curCSteuerbetrag = $this->cleanMoney($curCSteuerbetrag);
            $curCNetto = $brutto - $curCSteuerbetrag;
            $curCNetto = $this->cleanMoney($curCNetto);

            $curBNetto = 0;
            $curBBrutto = 0;
            if (isset($hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['B']['Nettobetrag'])){
                $curBNetto  = $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['B']['Nettobetrag'];
                $curBBrutto = $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['B']['Bruttobetrag'];
            }
            $curNewNetto = $curBNetto + $curCNetto;
            $curNewNetto = $this->cleanMoney($curNewNetto);
            $curNewBBrutto = $curBBrutto + $brutto;
            $curNewBBrutto = $this->cleanMoney($curNewBBrutto);

            // B Netto / Brutto Betrag:
            $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['B']['Nettobetrag'] = $curNewNetto;
            $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['B']['Bruttobetrag'] = $curNewBBrutto;



            // A Brutto Betrag
            if (isset($hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Bruttobetrag'])){
                $curABrutto = $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Bruttobetrag'];
            }
            else{
                $curABrutto = 0;
            }
            $curNewABrutto = $curABrutto + $brutto;
            $curNewABrutto = $this->cleanMoney($curNewABrutto);
            $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Bruttobetrag'] = $curNewABrutto;




            $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['C'][$indexC]['Satzart']        = 'C';
            $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['C'][$indexC]['Erloeskonto']    = $bookingSet['Erloeskonto'];          // Konto/Erlöskonto
            $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['C'][$indexC]['Nettobetrag']    = $curCNetto;                          // Nettobetrag
            $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['C'][$indexC]['Steuerbetrag']   = $curCSteuerbetrag;                   // Steuerbetrag
            $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['C'][$indexC]['KST']            = $bookingSet['Kostenstelle'];         // KST
            $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['C'][$indexC]['KTR']            = '';                                  // KTR
            $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['C'][$indexC]['Buchungstext']   = $bookingSet['Buchungstext'];         // Buchungstext




            // C Index erhöhen
            $indexC++;

            // Verarbeitete Rechnungsnummer speichern
            $lastBookingNumber = $curBookingNumber;
            $lastCustomerNumber = $curCustomerNumber;

        }   // END foreach ($hCore->gCore['BuchungsDaten'] as $bookingSet){


        RETURN TRUE;

    }   // END private function generateSetA()




    private function cleanMoney($arg)
    {
        $arg = str_replace(",",".", $arg);
        $arg = round($arg, 2);
        $arg = number_format($arg, 2, '.', '');

        return $arg;
    }




    // CSV - Datei Buchungssatz erstellen
    private function generateBooginCSV()
    {

        $hCore = $this->hCore;


        if (!isset($hCore->gCore['ExportBuchungsDaten']['Rechnungen'])){
            RETURN FALSE;
        }


        $tilde = '~';

        $csv = '';

        $cntA = 0;
        $cntB = 0;
        $cntC = 0;
        $cntSum = 0;
        $sumBruttoA = 0;
        $sumBruttoB = 0;
        $sumNettoC = 0;
        $sumSteuerBetragC = 0;

        foreach ($hCore->gCore['ExportBuchungsDaten']['Rechnungen'] AS $index=>$set){

            // Für Prüfsumme berechnen
            $sumBruttoA += $set['A']['Bruttobetrag'];

            // A Satz erstellen
            $csv .= $set['A']['Satzart'] . $tilde;
            $csv .= $set['A']['Personenkonto'] . $tilde;
            $csv .= $set['A']['Belegnummer'] . $tilde;
            $csv .= $set['A']['Rechnungsnummer'] . $tilde;
            $csv .= $set['A']['Buchungsperiode'] . $tilde;
            $csv .= $set['A']['Belegdatum'] . $tilde;
            $csv .= $set['A']['Buchungsdatum'] . $tilde;
            $csv .= $set['A']['Bruttobetrag'] . $tilde;
            $csv .= $set['A']['Waehrung'] . $tilde;
            $csv .= $set['A']['Skonto']. $tilde;
            $csv .= $set['A']['Zahlungsbedingungen'] . $tilde;
            $csv .= $set['A']['ABWZahlungsart'] . $tilde;
            $csv .= $set['A']['Faelligkeit'] . $tilde;
            $csv .= $set['A']['Valuta'] . $tilde;
            $csv .= $set['A']['Valuta'] . $tilde;
            $csv .= $set['A']['PLZ'] . $tilde;
            $csv .= $set['A']['Ort'] . $tilde;
            $csv .= $set['A']['Strasse'] . $tilde;
            $csv .= $set['A']['Hausnummer'] . $tilde;
            $csv .= $set['A']['Zusatzhausnummer'] . $tilde;
            $csv .= $set['A']['Wohnungsnummer'] . $tilde;
            $csv .= $set['A']['ABWKontoInhaberName1'] . $tilde;
            $csv .= $set['A']['ABWKontoInhaberName2'] . $tilde;
            $csv .= $set['A']['Laendercode'] . $tilde;
            $csv .= $set['A']['ABWBLZ'] . $tilde;
            $csv .= $set['A']['ABWKontoNr'] . $tilde;
            $csv .= $set['A']['ABWIBAN'] . $tilde;
            $csv .= $set['A']['ABWAnschriftName1'] . $tilde;
            $csv .= $set['A']['ABWAnschriftName2'] . $tilde;
            $csv .= $set['A']['ABWAnschriftPLZ'] . $tilde;
            $csv .= $set['A']['ABWAnschriftOrt'] . $tilde;
            $csv .= $set['A']['ABWAnschriftStrasse'] . $tilde;
            $csv .= $set['A']['ABWAnschriftHausNr'] . $tilde;
            $csv .= $set['A']['ABWAnschriftHausNrZusatz'] . $tilde;
            $csv .= $set['A']['Prenotifcation'] . $tilde;
            $csv .= $set['A']['MandatsRefNr'] . $tilde;
            $csv .= $set['A']['AnkZahlungseinzgZum'] . $tilde;
            $csv .= $set['A']['AnkZahlungseinzgAm'] . $tilde;
            $csv .= $set['A']['ABWAnkZahlungseinzg'] . $tilde;
            $csv .= $set['A']['BuchungszeichenAvviso'] . $tilde;
            $csv .= "\r\n";
            $cntA++;




            // Für Prüfsumme berechnen
            $sumBruttoB += $set['B']['Bruttobetrag'];

            // B Satz erstellen
            $csv .= $set['B']['Satzart'] . $tilde;
            $csv .= $set['B']['Bruttobetrag'] . $tilde;
            $csv .= $set['B']['Nettobetrag'] . $tilde;
            $csv .= $set['B']['Steuerkennzeichen'] . $tilde;
            $csv .= $set['B']['Geschaeftsbereich'] . $tilde;
            $csv .= "\r\n";
            $cntB++;






            // C Satz erstellen
            foreach ($set['C'] as $indexC=>$valueC){

                // Für Prüfsumme berechnen
                $sumNettoC += $valueC['Nettobetrag'];

                // Für Prüfsumme berechnen
                $sumSteuerBetragC += $valueC['Steuerbetrag'];

                $csv .= $valueC['Satzart'] . $tilde;
                $csv .= $valueC['Erloeskonto'] . $tilde;
                $csv .= $valueC['Nettobetrag'] . $tilde;
                $csv .= $valueC['Steuerbetrag'] . $tilde;
                $csv .= $valueC['KST'] . $tilde;
                $csv .= $valueC['KTR'] . $tilde;
                $csv .= $valueC['Buchungstext'] . $tilde;
                $csv .= "\r\n";
                $cntC++;
            }



        }   // END foreach ($hCore->gCore['ExportBuchungsDaten']['KdNr'] AS $kdNummer=>$setArray){



        // Prüfsumme
        $csv .= "P~";
        $csv .= "~";                    // Gesamtanzahl der Sätze "S" innerhalb der Datei
        $csv .= $cntA . "~";            // Gesamtanzahl der Sätze "A" innerhalb der Datei
        $csv .= $sumBruttoA. "~";       // Gesamtsumme aller Bruttobeträge der Sätze "A"
        $csv .= $cntB . "~";            // Gesamtanzahl der Sätze "B" innerhalb der Datei
        $csv .= $sumBruttoB. "~";       // Gesamtsumme aller Bruttobeträge der Sätze "B"
        $csv .= $cntC . "~";            // Gesamtanzahl der Sätze "C" innerhalb der Datei
        $csv .= $sumNettoC. "~";        // Gesamtsumme aller Nettobeträge der Sätze "C"
        $csv .= $sumSteuerBetragC . "~";  // Gesamtsumme aller Steuerbeträge der Sätze "C"
        $csv .= "\r\n";


        $hCore->gCore['BookingCSV'] = $csv;

        RETURN TRUE;

    }   // END private function generateBooginCSV()







    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////











}   // END class DBExport extends Core
