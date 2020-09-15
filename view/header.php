<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <!-- the head section -->
    <head>
        <title><?php echo $_SESSION['page']; ?></title>
        <link rel="stylesheet" type="text/css" href="../../CSS/loop.css" />
        
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
                    <form action="core/change_db.php" method="post">
                        <input name="action" type="hidden" value="change_db"/>
                        <input name="accessCode" type="hidden" value="<?php echo $_SESSION['access_code']; ?>"/>
                        Change Loop Page: <select name="dbId" title="Select a loop page" onchange="this.form.submit()">
                            <?php #need to make this list compiled based on the access code of the logged in user 
                                foreach($allPages as $next) {
                                    $dbIdSigBit = getSigBit($next['ID']);
                                    if($_SESSION['access_code'] & $dbIdSigBit) : ?>
                                        <option value="<?php echo $next['ID']; ?>" <?php echo ($_SESSION['activeDbId'] == $next['ID']) ? "selected":"" ; ?>><?php echo $next['LoopName']; ?></option>
                                    <?php endif;
                                }
                            ?>
                        </select>
                    </form>
                
                    <img src="<?php echo $_SESSION['banneruser']; ?>" width="100%" alt="Anne's Loops" usemap="#map-banner-loggedin"/>
                    <map name="map-banner-loggedin" style="cursor:pointer;">
                        <area shape="rect" coords="880,14,940,56" href="core/logout.php" alt="Logout"/>
                    </map>
                    <br />
                <?php else : ?>
                    <img src="<?php echo $_SESSION['banner']; ?>" width="100%" alt="Anne's Loops" usemap="#map-banner"/>
                    <map name="map-banner" style="cursor:pointer;">
                        
                    </map>
                <?php endif; ?>
            </center>