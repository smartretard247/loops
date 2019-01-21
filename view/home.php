<center>
    <div id="home">
        <img src="Images/SimplySilverAKY.jpg" alt="Simply Silver AKY" style="width: 100%; height: 100%;"/><br/><br/>
        <?php if(!$_SESSION['valid_user']) : ?>
        <table id="barcrumb" style="position: relative; top: <?php echo $topOfLogin; ?>px; right: 320px;">
            <tr>
                <th colspan="2">Login Information</th>
            </tr>
            <form action="core/login.php" method="post">
            <tr>
                <td  colspan="2" style="text-align: right;">
                    Username: <input name="Username" type="text"><br/>
                    Password: <input name="ThePassword" type="password"><br/>
                </td>
            </tr>
            <tr>
                <td><a href="index.php?action=view_create_account">Create Account</a></td>
                <td><input type="submit" value="Login"/></td> 
            </tr>
            </form>
        </table>
        <?php endif; ?>
        
        <?php $newestItems = $db->Query('SELECT ID, CategoryID, ImgFile, MAX(PurchaseDate) AS "PurchaseDate"'
                . ' FROM items WHERE ID IN (SELECT ID FROM items WHERE Hidden = 0 AND (QOH-QOO) > 0)'
                . ' GROUP BY ID, CategoryID, ImgFile, PurchaseDate LIMIT 4');

            $newestItemsPaths = array();
            $newestItemsIDs = array();
            foreach ($newestItems as $tnew) {
                $fileName = GetThumbnailFilename($tnew['ImgFile']);
                array_splice($newestItemsPaths, 0, 0, 'Images/inv/thumbs/' . $fileName);
                array_splice($newestItemsIDs, 0, 0, $tnew['ID']);
            }
        ?>
        
        <table class="alphaenabled" style="top: <?php echo $topOfNewItems; ?>px; right: -50px;">
            <tr>
                <td style="width: <?php echo $_SESSION['thumb_lw']; ?>px; height: <?php echo $_SESSION['thumb_lw']; ?>px;">
                    <img src="Images/newItems.png" alt="Newest Items" width="<?php echo $_SESSION['thumb_lw']; ?>" height="<?php echo $_SESSION['thumb_lw']; ?>"/>
                </td>
                <td>
                    <a href="index.php?action=view_item&id=<?php echo $newestItemsIDs[0]; ?>">
                        <img src="<?php echo $newestItemsPaths[0]; ?>" width="<?php echo $_SESSION['thumb_lw']; ?>" height="<?php echo $_SESSION['thumb_lw']; ?>"/>
                    </a>
                </td>
                <td style="width: <?php echo $_SESSION['thumb_lw']; ?>px; height: <?php echo $_SESSION['thumb_lw']; ?>px;"></td>
            
                <td>
                    <a href="index.php?action=view_item&id=<?php echo $newestItemsIDs[1]; ?>">
                        <img src="<?php echo $newestItemsPaths[1]; ?>" width="<?php echo $_SESSION['thumb_lw']; ?>" height="<?php echo $_SESSION['thumb_lw']; ?>"/>
                    </a>
                </td>
                <td style="width: <?php echo $_SESSION['thumb_lw']; ?>px; height: <?php echo $_SESSION['thumb_lw']; ?>px;"></td>
                <td>
                    <a href="index.php?action=view_item&id=<?php echo $newestItemsIDs[2]; ?>">
                        <img src="<?php echo $newestItemsPaths[2]; ?>" width="<?php echo $_SESSION['thumb_lw']; ?>" height="<?php echo $_SESSION['thumb_lw']; ?>"/>
                    </a>
                </td>
            
                <td style="width: <?php echo $_SESSION['thumb_lw']; ?>px; height: <?php echo $_SESSION['thumb_lw']; ?>px;"></td>
                <td>
                    <a href="index.php?action=view_item&id=<?php echo $newestItemsIDs[3]; ?>">
                        <img src="<?php echo $newestItemsPaths[3]; ?>" width="<?php echo $_SESSION['thumb_lw']; ?>" height="<?php echo $_SESSION['thumb_lw']; ?>"/>
                    </a>
                </td>
                <td style="width: <?php echo $_SESSION['thumb_lw']; ?>px; height: <?php echo $_SESSION['thumb_lw']; ?>px;"></td>
            </tr>
        </table>
        
        <p style="z-index: 0;">
            Welcome to Simply Silver AKY!
            If you enjoy wearing new fashionable statement jewelry, this is the place to look. Simply Silver AKY
            sells unique current fashion jewelry at affordable prices. We deliver right to your door!
            Treat yourself to a little something, and enjoy!
        </p>
        
        <?php ShowAlert(); ?>
    </div>
</center>