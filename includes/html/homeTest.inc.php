<?php
/**
 * Copyright (c) 2016 by Markus Melching (TKRZ)
 */

/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 12.01.2016
 * Time: 17:50
 */
?>


<?php
// Deklariere Div-Tag Navigation
?>
<div style="display: block" id="divLeftNavigationOuter" class="divLeftNavigationOuter">
    <?php include 'includes/html/home/body/bodyLeftNavigation.inc.php'; ?>
</div>


<?php
class baseDataSet {

    private static $baseDataSettings = array(
                                            'Vorname'=>array('min'=>'1',
                                                                'max'=>'30'
                                                            ),

                                            'Nachname'=>array('min'=>'1',
                                                                'max'=>'30'
                                                            ),

                                            'Email'=>array('min'=>'1',
                                                            'max'=>'60'
                                                            ),

                                            'Telefon'=>array('min'=>'0',
                                                                'max'=>'10'
                                                            ),

                                            'Brutto'=>array('min'=>'0',
                                                                'max'=>'10',
                                                                'callFunc'=>'ref_formatMyMoney',
                                                                'argFunc'=>'yes'
                                                            ),

                                            );


    function __construct(){

        RETURN TRUE;

    }




    // INITIAL Methode ... Durchläuft Feldname und Dateninhalt entsprechend aller Notwendigkeiten
    public function ref_checkDataSet(& $getFieldname, & $getValue)
    {
        // Datensatz ... "säubern" (Leerzeichen usw)
        $this->ref_getClearData($getFieldname);
        $this->ref_getClearData($getValue);



        // Datensatz ... soll weitere Methode aufgerufen werden?
        $this->ref_checkDynFunctionCall($getFieldname, $getValue);



        // Datensatz ... Min - Max - Länge prüfen
        if (!$this->checkMinMaxLen($getFieldname, $getValue))
            RETURN FALSE;


        RETURN TRUE;

    }





    // Dynamisch pro Datensatz eine Funktion/Methode aufrufen?
    private function ref_checkDynFunctionCall(& $getFieldname, & $getValue)
    {

        // Dynamisch eine Methode aufrufen?
        if (isset(self::$baseDataSettings[$getFieldname]['callFunc'])){
            $callMethod = self::$baseDataSettings[$getFieldname]['callFunc'];

            if ( (isset(self::$baseDataSettings[$getFieldname]['argFunc'])) && (self::$baseDataSettings[$getFieldname]['argFunc'] == 'yes') ){
                $this->$callMethod($getValue);
            }
            else{
                $this->$callMethod();
            }
        }

    }





    // Formatiert Geldbetrag
    private function ref_formatMyMoney(& $arg)
    {

        $arg = str_replace(",",".", $arg);
        $arg = round($arg, 2);
        $arg = number_format($arg, 2, '.', '');

    }





    // Datensatz ... "säubern" (Leerzeichen usw)
    private function ref_getClearData(& $arg){

        $arg = trim($arg);

    }





    // Min - Max - Länge prüfen und TRUE oder FALSE zurück geben
    private function checkMinMaxLen($getFieldname, $getValue){

        // Min - Max - Länge ermitteln
        $getRetArray = $this->getMinMaxLen($getFieldname);

        $min = $getRetArray[$getFieldname]['min'];
        $max = $getRetArray[$getFieldname]['max'];

        if (strlen($getValue) < $min)
            RETURN FALSE;

        if (strlen($getValue) > $max)
            RETURN FALSE;

        RETURN TRUE;

    }





    // Liefert die benötigte min und max Länge eines Datensatzes
    private function getMinMaxLen($getFieldname)
    {

        $return = array();
        $return[$getFieldname]['min'] = 0;
        $return[$getFieldname]['max'] = 0;

        if (key_exists($getFieldname, self::$baseDataSettings)){
            $return[$getFieldname]['min'] = self::$baseDataSettings[$getFieldname]['min'];
            $return[$getFieldname]['max'] = self::$baseDataSettings[$getFieldname]['max'];
        }

        RETURN $return;

    }

}

?>






<?php
// Deklariere Div-Tag Content
?>

    <div style="display: block" id="divContentOuter" class="divContentOuter">
        <table border=0 class="standard" style="width:100%">
            <tr>
                <td>
                    Code Testing <i class="fa fa-arrow-right"></i> Startseite
                </td>
            </tr>
        </table>


        <?php
        /*



            echo "<hr>";



            $obj = new baseDataSet();


            $datensatz = '12';
            $fieldName = 'Brutto';


            echo "<pre>";
            print_r($fieldName); echo "<br>";
            print_r($datensatz); echo "<br>";
            echo "</pre>";

            echo "<hr>";

            if (!$obj->ref_checkDataSet($fieldName, $datensatz)) {
                echo "Datensatz geht nicht<br>";
            }
            else{
                echo "Datensatz ok<br>";
            }


            echo "<pre>";
            print_r($fieldName); echo "<br>";
            print_r($datensatz); echo "<br>";
            echo "</pre>";

            echo "<hr>";



        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



            $pattern = 'Marienhospital Münsterland,';
            $search = '/,$/i';

            if ( preg_match_all($search, $pattern, $result)) {
                // $strassenname       = trim($result[1][0]);
            }
            $newValue = '';
            $pattern = preg_replace($search, $newValue, $pattern);


            echo "$pattern<br>";
            echo "<pre>";
            print_r($result);
            echo "</pre>";
            echo "$pattern<br>";
        */



        $p = 'Paul';

        $a = (isset($p)) ? $p : 'Markus';


        echo "$a<br>";

        ?>



<?php

echo "<hr>";


echo "<br>";



// Format NL: NL pp bbbb kkkkkkkkkk
$curIBAN   = 'NL 76 RABO 0150859295';

// Format DE: DE pp bbbbbbbb kkkkkkkkkk
//$curIBAN   = 'DE 91 40153768 0000035367';

$curIBAN = trim($curIBAN);

// Mögliche Leerzeichen entfernen
$curIBAN = preg_replace('/ /', '', $curIBAN);
$tmpLandID = substr($curIBAN, 0, 2);

if ($tmpLandID == 'DE'){

    // Deutsche IBAN

    $curBLZ = substr($curIBAN, 4, 8);
    $curKontoNummer = substr($curIBAN, 12);

}
elseif ($tmpLandID == 'NL'){

    // Niederländische IBAN
    $curBLZ = substr($curIBAN, 4, 4);
    $curKontoNummer = substr($curIBAN, 8);

}
?>


    </div>


<div style="display: block" id="footer_ticker_container" class="footer_ticker_container">
    <FORM NAME="NewsTicker">
        <INPUT TYPE="TEXT" READONLY id="inputFooterTicker" class="inputFooterTicker" NAME="Zeile" SIZE=50 MAXLENGTH=60">
    </FORM>
    <SCRIPT>StartTicker();</SCRIPT>
</div>
