<?php

/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 06.01.2016
 * Time: 15:08
 *
 * Vererbungsfolge der (Basis) - Klassen:
 *  	Base							    		        Adam/Eva
 *  	'-> SystemConfig				    		        Child
 *  	   	'-> DefaultConfig			    		        Child
 *  			'-> Messages			    		        Child
 * ===>				'-> Debug			    		        Child
 * 					    '-> MySQLDB		    	            Child
 *  					    '-> Query	    	            Child
 *       					    '-> Core    			    Child
 * 		    					    |-> ConcreteClass1	    Core - Child - AnyCreature
 * 			    				    |-> ...				    Core - Child - AnyCreatures
 * 				    			    |-> ConcreteClass20	    Core - Child - AnyCreature
 *
 */
class Debug extends Messages
{

    public $gDebug = array();





    function __construct()
    {

        // Debug - Classname ausgeben?!
        $this->debugInitOnLoad('Class', __CLASS__);


        parent::__construct();

    }	// END function __construct()





    private function getMyClassName($printOnScreen=false)
    {

        if ($printOnScreen)
            print ("<br>Ich bin Klasse: " . __CLASS__ . "<br>") ;

        return __CLASS__;

    }	// END function getMyClassName(...)





    function getClassName($printOnScreen=false)
    {

        $myClassNmae = $this->getMyClassName($printOnScreen);

        return $myClassNmae;

    }	// END function getClassName(...)





    // INITIAL Methode ... die Methode steuert grundlegende Debug - Funktionen
    // Wird aufgerufen beim laden einer Datei
    static function debugInitOnLoad($getType, $getValue)
    {

        // Debug eingeschaltet?
        if (!self::debugGetDebugStatus('enableDebug'))
            RETURN FALSE;


        // Debug auf Monitor ausgeben?
        if (self::debugGetDebugStatus('ShowOnScreen')){

            // Klassennamen ausgeben?
            if ( ($getType == 'Class') && (self::debugGetDebugStatus('ShowClassname')) )
                    self::simpleout('Ich bin Klasse: '.$getValue);


            // Dateinamen ausgeben?
            elseif ( ($getType == 'File') && (self::debugGetDebugStatus('ShowFilename')) )
                    self::simpleout(basename($getValue));

        }

        RETURN TRUE;

    }   // END function initDebugOnLoad(...)





    // Prüft ob ein Debug Einstellungswert yes/no ist
    private static function debugGetDebugStatus($arg)
    {
        if ( (isset($_SESSION['systemConfig']['Debug'][$arg])) && ($_SESSION['systemConfig']['Debug'][$arg] == 'yes') )
            RETURN TRUE;

        RETURN FALSE;

    }   // END private function getDebugStatus(...)





    // Debug - GET, POST, SESSSION, GLOBAL - Variable ausgeben?
    function debugInitDebugVarOutput()
    {

        // SICHERHEIT
        // 1. Abfangen ob der Benutzer eingeloggt ist
        // 2. Abfangen ob der Benutzer den Status 'Entwickler' hat
        // Wenn nicht, hier abbrechen und nichts weiter ausgeben
        if ( (!isset($_SESSION['Login']['User']['roleID'])) || ($_SESSION['Login']['User']['roleID'] > '1') )
            RETURN TRUE;


        $curVarArray = array(	'ShowGET' 		=> $_GET,
                                'ShowPOST' 		=> $_POST,
                                'ShowSession' 	=> $_SESSION,
                                'ShowGLOBALS' 	=> $GLOBALS
        );


        $curNameArray = array(	'ShowGET' 		=> '$_GET',
                                'ShowPOST' 		=> '$_POST',
                                'ShowSession' 	=> '$_SESSION',
                                'ShowGLOBALS' 	=> '$GLOBALS'
        );


        foreach ($curVarArray as $key=>$var){

            // <hr> Tag ausgeben? - Steuerung
            $htmlHRTagDone = false;

            // Soll der Schlüssel bzw.die Variable ausgegeben werden?
            if ($this->debugGetDebugStatus($key)){

                // <hr> Tag ausgeben?
                if (!$htmlHRTagDone)
                    $this->debugSimpleout('<hr>');

                // <hr> Tag ausgegeben, also auf true setzen
                $htmlHRTagDone = true;

                // Variable plus Headline ausgeben
                $this->detaileout($curNameArray[$key], $var);

            }

        }

        RETURN TRUE;

    }   // END function initDebugVarOutput()





    // Wechselt die Ausgabe/Anzeige des angegebenen Debug-Fensters (div-Tag) (an/aus)
    public function debugViewChange($arg)
    {

        // Message Ausgabe vorebeiten
        $this->gCore['Messages']['Type'][]      = 'Info';
        $this->gCore['Messages']['Code'][]      = 'Debug';
        $this->gCore['Messages']['Headline'][]  = 'Debug Informations- Fenster ein/aus!';


        if ($_SESSION['systemConfig']['Debug'][$arg] == 'yes'){
            $_SESSION['systemConfig']['Debug'][$arg] = 'no';

            // Message Ausgabe vorebeiten
            $this->gCore['Messages']['Message'][] = 'Debug Informations- Fenster ausgeschaltet!';

            RETURN TRUE;
        }


        // Message Ausgabe vorebeiten
        $this->gCore['Messages']['Message'][] = 'Debug Informations- Fenster eingeschaltet!';

        $_SESSION['systemConfig']['Debug'][$arg] = 'yes';

        RETURN TRUE;

    }   // END public function debugViewChange(...)





}   // END class Debug extends Messages
