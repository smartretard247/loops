<?php if($id) { //was $_GET['id'] set?
        $cItem->SetFromDB($id);
        
        if($cItem->IsHidden()) {
            $id = null;
        } else {
            $id = $cItem->GetID(); //validate id was of acutal item
            
            $input_description = $cItem->GetDescription();
            $input_categoryID = $cItem->GetCategoryID();
            $input_categoryName = $cItem->GetCategorySingular();
            $input_purchasePrice = $cItem->GetPurchasePrice();
            $input_price = $cItem->GetPrice();
            $input_shipping = $cItem->GetShipping();
            $input_purchaseDate = $cItem->GetPurchaseDate();
            $input_QFS = $cItem->GetQuantityForSale();
            $input_imgFile = $cItem->GetImgFile();
            $input_imgPath = $cItem->GetImgPath();

            $cReview->SetFromDB($id);
            $input_rating = $cReview->GetRating();
        }
    } else {
        $id = 0;
        $input_imgFile = 0;
    }
?>

<?php if($id) : ?>
<table id="itemdes" class="topmargin" align="center">
    <tr>
        <td width="<?php echo $_SESSION['image_lw']; ?>px" style="vertical-align: top;">
            <table>
                <tr rowspan="2">
                    <td>
                        <img src="<?php echo $input_imgPath; ?>" height="<?php echo $_SESSION['image_lw']; ?>px" width="<?php echo $_SESSION['image_lw']; ?>px"/>
                    </td>
                </tr>
            </table>
        </td>
        <td width="<?php echo $_SESSION['image_lw']; ?>px" style="vertical-align: top;">
            <table width="100%">
                <tr>
                    <th colspan="2">Item Information</th>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;" colspan="2">
                        <table width="100%">
                            <?php if($input_QFS<1) : ?>
                            <tr><td colspan="3"><p class="outofstock">This item is currently out of stock.</p></td></tr>
                            <?php endif; ?>
                            <tr>
                                <td class="left" colspan="3"><b>Description:</b> <?php echo $input_description . ' ' . $input_categoryName; ?></td>
                            </tr>
                            <tr>
                                <td class="left" style="width: 30%;"><b>Price:</b> $<?php echo $input_price; ?></td>
                                <td class="center" style="width: 40%;">
                                    <?php if($input_rating) : ?>
                                        <?php $stars = $input_rating;
                                        for($i = 0; $i < $stars; $i++) : ?>
                                            <img src="Images/star.png" height="20px" width="20px"/>
                                        <?php endfor; ?>
                                    <?php else : ?>
                                        <b>Not Yet Rated</b>
                                    <?php endif; ?>
                                </td>
                                <td class="right" style="width: 30%;"><b>In Stock:</b> <?php echo $input_QFS; ?></td>
                            
                            </tr>
                            <tr>
                                <td class="right" colspan="3">
                                    <?php if($input_QFS): ?>
                                        <form action="core/add_to_cart.php" method="post">
                                            <input type="hidden" value="<?php echo $id; ?>" name="ID"/>
                                            <input type="hidden" value="<?php echo $input_QFS; ?>" name="QOH"/>
                                            <select name="Q">
                                                <?php for($i = 1; $i <= $input_QFS; $i++) : ?>
                                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                                <?php endfor; ?>
                                            </select>
                                            <input type="submit" value="Add To Cart"/>
                                        </form>
                                    <!-- PayPal form
                                        <form target="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                                            <input type="hidden" name="cmd" value="_s-xclick">
                                            <input type="hidden" name="hosted_button_id" value="452ZRG39NGXVL">
                                            <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_cart_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                                            <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                                        </form>
                                    -->
                                    <?php else : ?>
                                        <select disabled="disabled">
                                            <option value="0">0</option>
                                        </select>
                                        <input type="button" value="Add To Cart" disabled="disabled"/>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table id="reviews" class="topmargin" align="center" width="80%">
    <tr>
        <th colspan="2">Reviews</th>
    </tr>
        <?php if($input_rating) : 
            $ratings = $cReview->GetRatings();
            $reviews = $cReview->GetReviews();
            foreach($reviews as $tReview => $text) :
                if($text != '') : ?>
                    <tr>
                        <td class="right" width="120px">
                            <?php ShowStars($ratings[$tReview]); ?>
                        </td>
                        <td style="text-align: justify;">
                            <?php echo $text; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else : ?>
            <tr><td class="center" colspan="2">This item has not yet been rated.</td></tr>
        <?php endif; ?>
        <tr>
            <td class="center" colspan="2"><a href="index.php?action=view_rate&id=<?php echo $id; ?>" style="text-decoration: underline;">Review This Item</a></td>
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
<?php endif; ?>