<?php
//header_inc.php

$navLinks = array();
$navLinks['upload.php'] = "Upload Data";
$navLinks['select_data.php'] = "Select Data";
//$navLinks['scattering.php'] = "Scattering";
$navLinks['show_chart.php'] = "Chart";

$navIcons = array();
$navIcons['upload.php'] = '<i class="fa fa-upload fa-fw"></i> ';
$navIcons['select_data.php'] = '<i class="fa fa-table fa-fw"></i> ';
//$navIcons['scattering.php'] = '<i class="fa fa-arrows-alt fa-fw"></i> ';
$navIcons['show_chart.php'] = '<i class="fa fa-line-chart fa-fw"></i> ';


function makeLinks($Array, $icons) {
    $myReturn = "";
    //$arrow_count = 0;
    //$arrow = ' <span><i class="fa fa-arrow-right"></i></span> ';
    foreach ( $Array as $url => $text ) {
        $url == THIS_PAGE ? $current = ' class="current" ' : $current = '';
        $url == 'show_chart.php' ? $href = '' : $href = 'href = "'.$url.'"';
        $url == 'show_chart.php' ? $id = ' id="chart" ' : $id = '';
        //dumpDie($href);
        $myReturn .= '<li' . $current . $id . '><a '. $href.'>' . $icons[$url] . $text . '</a></li>';
//        if ($arrow_count < 2) {
//            $myReturn .= $arrow;
//            $arrow_count++;
//        }
    }

    return $myReturn;
}

$pageTitle = $navLinks[THIS_PAGE];

?>
<!DOCTYPE html>
<html>

<head lang="en">

    <title><?= $pageTitle ?></title>

    <meta charset="utf-8"/>

    <meta name="viewport" content="width=device-width"/>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <link rel="stylesheet" type="text/css" href="css/nav_style.css">
    <link rel="stylesheet" type="text/css" href="css/tooltip.css">

<!--    <link rel="icon" type="image/png" href="images/charts.ico"/>-->
    <link rel="icon" type="image/png" href="images/favicon.ico"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

    <!-- jQuery -->
    <script src="js/jquery-2.1.4.js"></script>
    <!-- ICanHaz templating -->
    <script type="text/javascript" src="js/ICanHaz.js"></script>
    <!-- kolorwheel.js -->
    <script src="js/KolorWheel.js"></script>

</head>


<body class="<?= explode('.', THIS_PAGE)[0]?>">

<header>
    <nav>

        <ul id="menu">
            <div class="nav">
                <?= makeLinks($navLinks, $navIcons) ?>
            </div>
        </ul>

    </nav>
</header>
