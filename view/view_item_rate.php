<?php if($id) { //was $_GET['id'] set?
        $cItem->SetFromDB($id);
        $id = $cItem->GetID(); //validate id was of acutal item
        
        $input_description = $cItem->GetDescription();
        $input_categoryID = $cItem->GetCategoryID();
        $input_categoryName = $cItem->GetCategoryName();
        $input_purchasePrice = $cItem->GetPurchasePrice();
        $input_price = $cItem->GetPrice();
        $input_shipping = $cItem->GetShipping();
        $input_purchaseDate = $cItem->GetPurchaseDate();
        $input_QOH = $cItem->GetQOH();
        $input_imgPath = $cItem->GetImgPath();
    } else {
        $id = 0;
        $input_imgPath = 0;
    }
?>

<?php if($id) : ?>
<table class="topmargin" align="center">
    <tr>
        <td style="vertical-align: top;">
            <table>
                <tr rowspan="2">
                    <td>
                        <img src="<?php echo $input_imgPath; ?>" height="<?php echo $_SESSION['image_lw']; ?>px" width="<?php echo $_SESSION['image_lw']; ?>px"/>
                    </td>
                </tr>
            </table>
        </td>
        <td style="vertical-align: top;">
            <table width="<?php echo $_SESSION['image_lw']; ?>px">
                <tr>
                    <th colspan="2">Rate This Item</th>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center;">
                        <form action="core/review_verify.php" method="post">
                            <input name="Approved" type="hidden" value="0"/>
                            <input type="hidden" name="ItemID" value="<?php echo $id; ?>"/>
                            <b>How many stars would you give this item?</b>
                            <select name="Rating">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select><br/><br/>
                            <b>Please comment this item for future buyers below:</b><br/><br/>
                            <textarea cols="50" rows="6" name="Review" maxlength="254" style="resize: none;">Enter your comment here...</textarea><br/><br/>
                            <input type="submit" value="Submit This Review"/><br/><br/>
                        </form>
                        
                        
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<?php else : ?>
<table class="topmargin" align="center">
    <tr>
        <th>Problem Loading Item</th>
    </tr>
    <tr>
        <td>
            <p align="center">We're sorry, the item you are looking for could not be found.</p>
        </td>
    </tr>
</table>
<?php endif;