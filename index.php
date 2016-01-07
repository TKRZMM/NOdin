<?php
/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 06.01.2016
 * Time: 13:48
 *
 * 0010) Initial - Starte Session - Management
 *
 * 0020) PHP Error - Handling
 *
 * 0030) System - Check durchführen
 *
 * 0040) Core - Klassen implementieren
 *
 * 0050) Debug - Dateinamen ausgeben?!
 *
 * 0060) Lade die systemMain - Webseite ... in ihr wird das eigentliche Frame-Gebilde erzeugt
 *          Head und Body werden dort geladen
 *          Action wird dort verarbeitet bzw. includet
 *
 * 0070) Debug - Variable augeben?!
 *
 * 0080) Dynamischer Include HTML - Footer
 *          Head und Body wurdn in der includes/system/systemMain.inc.php geladen!
 *
 */

// 0010) Initial - Starte Session - Management
session_start();




// 0020) PHP Error - Handling
// Muss hier manuell eingesetzt werden, weil alle weiteren Daein erst noch kommen
function indexErrorHandling()
{
    if ( (isset($_SESSION['systemConfig']['Debug']['enableDebug'])) && ($_SESSION['systemConfig']['Debug']['enableDebug'] == 'yes') ){
        if ( (isset($_SESSION['systemConfig']['Debug']['PHPErrors'])) && ($_SESSION['systemConfig']['Debug']['PHPErrors'] == 'all') ){
            error_reporting (E_ALL | E_STRICT);
        }
        else {
            error_reporting(E_ALL & ~E_NOTICE);
        }
        ini_set ('display_errors', 'On');
    }
    else {
        error_reporting(0);
        ini_set ('display_errors', 'Off');
    }
}
// PHP Error - Handling
indexErrorHandling();




// 0030) System - Check durchführen
require_once 'includes/system/systemCheck.inc.php';

// PHP Error - Handling
//indexErrorHandling();




// 0040) Core - Klassen implementieren
require 'includes/system/systemClassLoad.inc.php';

// PHP Error - Handling
//indexErrorHandling();




// 0050) Debug - Dateinamen ausgeben?!
$hCore->initDebugOnLoad('File',__FILE__);




// 0060) Lade die systemMain - Webseite ... in ihr wird das eigentliche Frame-Gebilde erzeugt
require 'includes/system/systemMain.inc.php';


//DEBUG
$hCore->detaileout('$gCore',$hCore->gCore);



// 0070) Debug - Variable augeben?!
$hCore->initDebugVarOutput();





// 0080) Dynamischer Include HTML - Footer
// Wird in der index.php geladen!
// Grund: Eine formatierte Ausgabe der Debug und Zusatzinformationen ist sonst nicht möglich.
// Erzeuge Footer - Klassen - Objekt (Dynamisch nach Default (s.o.) und ggf. Änderungen durch die Action (s.o.)
$hFooter = new $getLeadToFooterClass($hCore);	// WICHTIG Übergebe hCore - Objekt

$getLeadToFooterSite = $hFooter->getLeadToFooterSite();
// --> load Footer
//include $getLeadToFooterSite . '.inc.php';
$hCore->simpleout('Geladene Footer-Seite: ' . $getLeadToFooterSite);

