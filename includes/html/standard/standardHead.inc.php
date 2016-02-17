<?php
/*
 * Die Datei "standardHead.inc.php" ...
 *      Liefert den typischen Aufbau einer Header - Datei ohne weitere Details
 *      Sie kann verwendet werden um css, java-script ect. zu laden.
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="<?php print($_SESSION['customConfig']['TextCharset']['Website']); ?>">
    <title><?php print ($_SESSION['customConfig']['Titles']['Website']); ?></title>


    <link rel="stylesheet" type="text/css" href="<?php print ($_SESSION['customConfig']['WebLinks']['INTERNHOMESHORT']); ?>includes/css/defaultCSS.css" />
    <link rel="stylesheet" type="text/css" href="<?php print ($_SESSION['customConfig']['WebLinks']['INTERNHOMESHORT']); ?>includes/css/sizeCSS.css" />
    <link rel="stylesheet" type="text/css" href="<?php print ($_SESSION['customConfig']['WebLinks']['INTERNHOMESHORT']); ?>includes/css/divCSS.css" />
    <link rel="stylesheet" type="text/css" href="<?php print ($_SESSION['customConfig']['WebLinks']['INTERNHOMESHORT']); ?>includes/css/leftnavigationCSS.css" />
    <link rel="stylesheet" type="text/css" href="<?php print ($_SESSION['customConfig']['WebLinks']['INTERNHOMESHORT']); ?>includes/css/buttonCSS.css" />
    <link rel="stylesheet" href="<?php print ($_SESSION['customConfig']['WebLinks']['INTERNHOMESHORT']); ?>includes/css/font-awesome-4.5.0/css/font-awesome.min.css" />


    <script type="text/javascript" src="<?php print ($_SESSION['customConfig']['WebLinks']['INTERNHOMESHORT']); ?>includes/javascript/defaultJavaScript.js"></script>

    <?php
    $sum_news = 123;
    print('<script type="text/javascript">{');
    print ('var	tickernews	=
    [
        {meldung:"Stammdatensätze: '.$sum_news.'", starteffekt:1, endeeffekt:1},
        {meldung:"Buchungsdatensätze: 128", starteffekt:1, endeeffekt:1},
        {meldung:"Und noch viel mehr Text könnte hier stehen.", starteffekt:1, endeeffekt:1}
    ];');
    print('}</script>');
    ?>

    <script type="text/javascript" src="<?php print ($_SESSION['customConfig']['WebLinks']['INTERNHOMESHORT']); ?>includes/javascript/footerTicker.js"></script>

</head>


<body>
