<?php $accountID = filter_input(INPUT_POST,'AccountID');
    if(!$accountID) {
        $accountID = filter_input(INPUT_GET,'AccountID');
    }
    
    if($_SESSION['edit_mode']) {
        $cAddress = unserialize($_SESSION['address_obj']);
        $id = $cAddress->GetID();
        $input_line1 = $cAddress->GetLine1();
        $input_line2 = $cAddress->GetLine2();
        $input_city = $cAddress->GetCity();
        $input_state = $cAddress->GetState();
        $input_zip = $cAddress->GetZip();
        $input_zipPlusFour = $cAddress->GetZipPlusFour();
        $input_country = $cAddress->GetCountry();
        $input_isBilling = $cAddress->IsBilling();
    } else {
        //empty all input values
        $id = 0;
        $input_line1 = '';
        $input_line2 = '';
        $input_city = '';
        $input_state = '';
        $input_zip = '';
        $input_zipPlusFour = '';
        $input_country = 'US';
        $input_isBilling = false;
    }
?>
<center>
<form name="formAddressAdd" action="core/address_verify.php" method="post">
    <input type="hidden" name="ID" value="<?php echo $id; ?>"/>
    <input type="hidden" name="AccountID" value="<?php echo $accountID; ?>"/>
    <table class="topmargin">
        <tr>
            <th colspan="2">Address Information</th>
        </tr>
        <tr>
            <td>AccountID:</td>
            <td><input type="text" value="<?php echo $accountID; ?>" disabled="disabled"/></td>
        </tr>
        <tr>
            <td>Address Line 1:</td>
            <td><input type="text" name="Line1" maxlength="100" value="<?php echo $input_line1; ?>"/></td>
        </tr>
        <tr>
            <td>Address Line 2:</td>
            <td><input type="text" name="Line2" maxlength="100" value="<?php echo $input_line2; ?>"/></td>
        </tr>
        <tr>
            <td>City:</td>
            <td><input type="text" name="City" maxlength="40" value="<?php echo $input_city; ?>"/></td>
        </tr>
        <tr>
            <td>State:</td>
                <td><select name="State" value="<?php echo $input_state; ?>">
                        <option value="<?php echo $input_state; ?>"><?php echo $input_state; ?></option>
                        <?php $states = $db->GetTable('states', 'Name');
                            foreach($states as $tState) : ?>
                                <?php if($tState['Name'] == $input_state) : ?>
                                    <option value="<?php echo $tState['Name']; ?>" selected="selected"><?php echo $tState['Name']; ?></option>
                                <?php else : ?>
                                    <option value="<?php echo $tState['Name']; ?>"><?php echo $tState['Name']; ?></option>
                                <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </td>
        </tr>
        <tr>
            <td>Zip+4:</td>
            <td><input type="text" size="5" name="Zip" maxlength="5" value="<?php echo $input_zip; ?>"/><input type="text" size="4" name="ZipPlusFour" maxlength="4" value="<?php echo $input_zipPlusFour; ?>"/></td>
        </tr>
        <tr>
            <td>Country:</td>
            <td><select name="Country" value="<?php echo $input_country; ?>">
                    <option value="<?php echo $input_country; ?>"><?php echo $input_country; ?></option>
                    <?php $countries = $db->GetTable('countries', 'Name');
                        foreach($countries as $tCountry) : ?>
                        <option value="<?php echo $tCountry['Name']; ?>"><?php echo $tCountry['Name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <?php if($_SESSION['edit_mode']) : ?>
                    <input type="submit" value="Save Changes"/>
                <?php else : ?>
                    <input type="submit" value="Save Address"/>
                <?php endif; ?>
                <?php if($action == 'view_address_edit' || $action == 'view_address_add') : ?>
                    <input type="button" onclick="window.location='index.php?action=view_address_list';" value="Cancel"/>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</form>
</center>