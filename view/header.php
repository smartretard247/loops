<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <!-- the head section -->
    <head>
        <title>Simply Silver AKY</title>
        <link rel="stylesheet" type="text/css" href="../../CSS/ssa.css" />
        
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
        <style type="text/css">@import "JS/jquery.datepick.css";</style>
        <script type="text/javascript" src="JS/jquery.datepick.js"></script>
        <script type="text/javascript">
            $(function() {
                    $('#popupDatepicker').datepick({dateFormat: 'yyyy-mm-dd'});
                    $('#popupDatepicker2').datepick({dateFormat: 'yyyy-mm-dd'});
            });
        </script>
        <script type="text/javascript">
            function showImage(id, visible) {
                var img = document.getElementById(id);
                img.style.visibility = (visible ? 'visible' : 'hidden');
            }
        </script>
        
        <style type="text/css">
                h5 {font-size: 16pt; margin-top: 0;}
        </style>
    </head>

    <!-- the body section -->
    <body OnLoad="document.frmFocus.Username.focus();">
    <div id="page">
        <div id="header">
        </div>
        <div id="main">
            <center>
                <?php if($_SESSION['valid_user']) : ?>
                    <img src="Images/banneruser.png" width="100%" alt="Simply Silver AKY" usemap="#map-banner-loggedin"/>
                    <map name="map-banner-loggedin" style="cursor:pointer;">
                        <area shape="rect" coords="1175,35,1235,56" href="core/logout.php" alt="Logout"/>
                        <area shape="rect" coords="1075,35,1155,56" href="index.php?action=view_my_cart" alt="View Cart"/>
                    </map>
                    <br />
                <?php else : ?>
                    <img src="Images/banner.png" width="100%" alt="Simply Silver AKY" usemap="#map-banner"/>
                    <map name="map-banner" style="cursor:pointer;">
                        <area shape="rect" coords="1075,35,1155,56" href="index.php?action=view_my_cart" alt="View Cart"/>
                    </map>
                <?php endif; ?>
            </center>