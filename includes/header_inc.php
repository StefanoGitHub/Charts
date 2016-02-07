<?php
//header_inc.php

$navLinks = array();
$navLinks['select_data.php'] = "Select Data";
$navLinks['upload.php'] = "Upload Data";
$navLinks['view_chart.php'] = "Chart";


function makeLinks($Array)
{
    $myReturn = "";
    foreach ( $Array as $url => $text ) {
//        if ($url == THIS_PAGE || THIS_PAGE == "projects_view.php" && $url == "projects_list.php") {
        if ($url == THIS_PAGE) {
                $current = ' class="current" ';
        } else {
            $current = '';
        }
        $myReturn .= '<li' . $current . '><a href="' . $url . '">' . $text . '</a></li>';
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
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/tooltip.css">
<!--    <link rel="icon" type="image/png" href="images/charts.ico"/>-->
    <link rel="icon" type="image/png" href="images/favicon.ico"/>

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
            <?= makeLinks($navLinks) ?>
        </ul>

    </nav>
</header>
