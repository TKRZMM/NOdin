<?php
/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 07.01.2016
 * Time: 10:02
 */

// Aktueller Host oder IP des Servers
$myHost = '192.168.6.11';  // Emsdetten
//$myHost = '192.168.178.42';  // Rheine


// Buchung und System
// Stammdaten - Sammelkonto für Centron Kunden
$_SESSION['customConfig']['Centron']['Sammelkonto'] = '122800';

// Stammdaten - Zahlungsart (Selbstzahler sprich Überweiser) ?!
$_SESSION['customConfig']['Centron']['Zahlungsart'] = 'SZ';

// Centron Geschäftsbereich Geschäfts-Kunden ?!
$_SESSION['customConfig']['Centron']['GeschaeftsbereichNonPrivate'] = '814';
// Centron Geschäftsbereich Privat-Kunden ?!
$_SESSION['customConfig']['Centron']['GeschaeftsbereichPrivate'] = '813';
// Währung
$_SESSION['customConfig']['Centron']['Waehrung'] = 'EUR';
$_SESSION['customConfig']['Centron']['Zahlungsbedingung'] = '10';

// Dimari
// Dimari generelles Sammelkonto
$_SESSION['customConfig']['Dimari']['Sammelkonto'] = 122800;

// Zahlarten ID Übersetzung
$_SESSION['customConfig']['Dimari']['Zahlart'][0] = 'SZ';
$_SESSION['customConfig']['Dimari']['Zahlart'][1] = 'BL';



// Path & Links
// Link - Full external link
$_SESSION['customConfig']['WebLinks']['EXTHOME'] = 'http://'.$myHost.'/NOdin/index.php';

// Link - External (short) link
$_SESSION['customConfig']['WebLinks']['EXTHOMESHORT'] = 'http://'.$myHost.'/NOdin/';

// Path - Internal (short) link ... Notice: leading- and end / (slash) required
$_SESSION['customConfig']['WebLinks']['INTERNHOMESHORT'] = '/NOdin/';

// Path - Upload - Directory
$_SESSION['customConfig']['WebLinks']['MAINUPLOADPATH'] = '/var/www/html/NOdin/uploads/';

// Link - PHP MyAdmin
$_SESSION['customConfig']['WebLinks']['PHPMYADMIN'] = 'http://'.$myHost.'/phpmyadmin/';



// Title Header
$_SESSION['customConfig']['Titles']['Website'] = 'Odin Konverter (Development)';

// Website Charset
$_SESSION['customConfig']['TextCharset']['Website'] = 'UTF-8';



// Database Settings
include 'databaseConfig.inc.php';
//$_SESSION['customConfig']['DBSettings']['DBHOST'] 		= '';
//$_SESSION['customConfig']['DBSettings']['DBNAME'] 		= '';
//$_SESSION['customConfig']['DBSettings']['DBUSER'] 		= '';
//$_SESSION['customConfig']['DBSettings']['DBPASSWORD'] 	= '';



// Login - Conditions
$_SESSION['customConfig']['Login']['MinLenUsername'] 	= '3';
$_SESSION['customConfig']['Login']['MaxLenUsername'] 	= '30';
$_SESSION['customConfig']['Login']['MinLenPassword'] 	= '3';
$_SESSION['customConfig']['Login']['MaxLenPassword'] 	= '30';
