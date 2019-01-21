<?php //saving previous location
    $sortby = filter_input(INPUT_GET, 's');
    if(!$sortby) { $sortby = 'ID'; }
    $isDescending = filter_input(INPUT_GET, 'd'); 
    if(!$isDescending) { $isDescending = 0; }
    $page = filter_input(INPUT_GET, 'p');
    if(!$page) { $page = 0; }

    if($_SESSION['edit_mode']) {
        $cItem = unserialize($_SESSION['item_obj']);
        $id = $cItem->GetID();
        $input_description = $cItem->GetDescription();
        $input_categoryID = $cItem->GetCategoryID();
        $input_categoryName = $cItem->GetCategoryName();
        $input_purchasePrice = $cItem->GetPurchasePrice();
        $input_price = $cItem->GetPrice();
        $input_purchaseDate = $cItem->GetPurchaseDate();
        $input_QOH = $cItem->GetQOH();
        
        if($_SESSION['ImgName'] == '') { $input_imgFile = $cItem->GetImgFile(); }
        else { $input_imgFile = $_SESSION['ImgName']; $_SESSION['ImgName'] = ''; }
        
        $input_imgPath = $cItem->GetImgPath();
        $input_shipping = $cItem->GetShipping();
    } else {
        //empty all input values
        $id = 0;
        $input_description = '';
        $input_categoryID = 0;
        $input_categoryName = '';
        $input_purchasePrice = 0.0;
        $input_price = 0.0;
        $today = getdate();
        $input_purchaseDate = $today['year'] . '-' . $today['mon'] . '-' . $today['mday'];
        $input_QOH = 0;
        
        if($_SESSION['ImgName'] == '') { $input_imgFile = ''; }
        else { $input_imgFile = $_SESSION['ImgName']; $_SESSION['ImgName'] = ''; }
        
        $input_imgPath = '';
        $input_shipping = 2.5;
    }
?>

<table class="topmargin" align="center">
    <tr>
        <td style="vertical-align: top;">
            <table>
                <tr>
                    <th>Item Image</th>
                </tr>
                <tr>
                    <td>
                        <?php if($input_imgFile) : ?>
                            <img src="<?php echo $input_imgPath; ?>" height="<?php echo $_SESSION['image_lw']; ?>px" width="<?php echo $_SESSION['image_lw']; ?>px"/>
                        <?php else : ?>
                            <p style="text-align: center; vertical-align: middle;">Image not found.</p>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </td>
        <td style="vertical-align: top;">
            <form name="formItemAdd" action="core/item_verify.php?s=<?php echo $sortby; ?>&d=<?php echo $isDescending; ?>&p=<?php echo $page; ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="ID" value="<?php echo $id; ?>"/>
                <table>
                    <tr>
                        <th colspan="2">Item Information</th>
                    </tr>
                    <tr>
                        <td>Description:</td>
                        <td><input type="text" name="Description" maxlength="255" value="<?php echo $input_description; ?>"/></td>
                    </tr>
                    <tr>
                        <td>Category:</td>
                        <td>
                            <select name="CategoryID" value="<?php echo $input_categoryID; ?>">
                                <?php if($_SESSION['edit_mode']) : ?>
                                    <option value="<?php echo $input_categoryID; ?>"><?php echo $input_categoryName; ?></option>
                                <?php endif; ?>
                                <?php foreach($item_category as $tCategory) : ?>
                                    <option value="<?php echo $tCategory['ID']; ?>"><?php echo $tCategory['Name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Purchase Price:</td>
                        <td>$<input type="text" name="PurchasePrice" maxlength="6" value="<?php echo number_format($input_purchasePrice,2); ?>"/></td>
                    </tr>
                    <tr>
                        <td>Selling Price:</td>
                        <td>$<input type="text" name="Price" maxlength="6" value="<?php echo number_format($input_price,2); ?>"/></td>
                    </tr>
                    <tr>
                        <td>Shipping:</td>
                        <td>$<input type="text" name="Shipping" maxlength="6" value="<?php echo number_format($input_shipping,2); ?>"/></td>
                    </tr>
                    <tr>
                        <?php if($_SESSION['edit_mode']) : ?>
                            <td>Purchase Date/Qty/QOH:</td>
                        <?php else : ?>
                            <td>Purchase Date/Qty:</td>
                        <?php endif; ?>
                        <td>
                            <input size="10" id="popupDatepicker" name="PurchaseDate" type="text" value="<?php echo $input_purchaseDate; ?>"/>
                            <?php if($_SESSION['edit_mode']) : ?>
                                <input size="2" name="PurchaseQty" type="text" value="0"/>
                                <input name="QOH" type="hidden" value="<?php echo $input_QOH; ?>"/>
                                <input size="2" type="text" value="<?php echo $input_QOH; ?>" disabled="true"/>
                            <?php else : ?>
                                <input size="2" name="PurchaseQty" type="text" value="0"/>
                                <input size="2" name="QOH" type="hidden" value="0"/>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Full Image:</td>
                        <?php if($input_imgFile == '') : ?>
                            <td>
                                <input id="file" type="file" name="ImgFile" maxlength="32"/>
                                <input name="x" type="hidden" value="YES"/>
                            </td>
                        <?php else : ?>
                            <td>
                                <input id="file" type="text" name="ImgFile" maxlength="32" value="<?php echo $input_imgFile; ?>" disabled="disabled"/>
                                <input name="x" type="button" value="X" onclick="document.formItemAdd.ImgFile.disabled = false; document.formItemAdd.ImgFile.type = 'file'; document.formItemAdd.x.type = 'hidden'; document.formItemAdd.x.value = 'YES';">
                            </td>
                        <?php endif; ?>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <?php if($_SESSION['edit_mode']) : ?>
                                <input onclick="document.formItemAdd.ImgFile.disabled = false;" type="submit" value="Save Changes"/>
                            <?php else : ?>
                                <input type="submit" value="Add Item"/>
                            <?php endif; ?>
                            <input onclick="window.location='index.php?action=view_inventory&s=<?php echo $sortby; ?>&d=1&p=<?php echo $page; ?>';" type="button" value="Cancel"/>
                        </td>
                    </tr>
                </table>
            </form>
        </td>
    </tr>
</table>