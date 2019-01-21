<?php if($_SESSION['admin_enabled']) :
    $accountID = filter_input(INPUT_POST,'AccountID');
    if(!$accountID) {
        $accountID = filter_input(INPUT_GET,'AccountID');
    }

    if($accountID) {
        $addressesForUser = $db->SafeFetchAll("SELECT * FROM addresses "
            . "WHERE AccountID = :0 ORDER BY ID",array($accountID));
    }
    
    $accountList = $db->SafeFetchAll("SELECT ID FROM accounts WHERE AccountType > 0 ORDER BY ID");
    
    $count = 0;
    ?>
    
<center>
    
<table class="topmargin">
    <tr>
        <th>Account List</th>
    </tr>
    <tr>
        <td>
            <form name="frmAccountList" method="post">
                <input type="hidden" name="action" value="view_address_list"/>
                <select name="AccountID">
                    <?php if(!$accountID) { echo "<option></option>"; } ?>
                    <?php foreach($accountList as $taccountList) : ?>
                        <?php if($taccountList['ID'] == $accountID) : ?>
                            <option value="<?php echo $taccountList['ID']; ?>" selected="true"><?php echo $taccountList['ID']; ?></option>
                        <?php else : ?>
                            <option onclick="frmAccountList.submit();" value="<?php echo $taccountList['ID']; ?>"><?php echo $taccountList['ID']; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </form>
        </td>
    </tr>
</table>

<table class="topmargin"><tr>
    <?php if($addressesForUser) :
        foreach ($addressesForUser as $tAddress) :
            ++$count;
            echo '<td>';
            $header = 'Address #' . $count;
            
            $cAddress->SetFromDB($tAddress['ID']);
            $id = $cAddress->GetID();
            $input_line1 = $cAddress->GetLine1();
            $input_line2 = $cAddress->GetLine2();
            $input_city = $cAddress->GetCity();
            $input_state = $cAddress->GetState();
            $input_zip = $cAddress->GetZip();
            $input_zipPlusFour = $cAddress->GetZipPlusFour();
            $input_country = $cAddress->GetCountry();
            $input_isBilling = $cAddress->IsBilling();
            
            $input_isHistorical = $tAddress['Historical'];
    ?>

    <form name="formAddressAdd<?php echo $id; ?>" action="core/address_verify.php" method="post">
        <input type="hidden" name="ID" value="<?php echo $id; ?>"/>
        <input type="hidden" name="AccountID" value="<?php echo $accountID; ?>"/>
        <table>
            <tr>
                <th colspan="2"><?php echo $header; ?></th>
            </tr>
            <tr>
                <td colspan="2">
                    <?php if($input_isBilling) : ?>
                        <input name="Billing" onclick="document.formAddressAdd<?php echo $id; ?>.submit();" type="checkbox" checked/>Billing?
                    <?php else : ?>
                        <input name="Billing" onclick="document.formAddressAdd<?php echo $id; ?>.submit();" type="checkbox"/>Billing?
                    <?php endif; ?>
                    <?php if($input_isHistorical) : ?>
                        <input name="Historical" onclick="document.formAddressAdd<?php echo $id; ?>.submit();" type="checkbox" checked/>Historical?
                    <?php else : ?>
                        <input name="Historical" onclick="document.formAddressAdd<?php echo $id; ?>.submit();" type="checkbox"/>Historical?
                    <?php endif; ?>
                </td>
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
                        <input onclick="window.location='index.php?action=address_delete&AccountID=<?php echo $accountID; ?>&id=<?php echo $id; ?>'" type="button" value="Delete"/>
                    <?php endif; ?>
                </td>
            </tr>

        </table>
    </form>
    <?php echo '</td>'; endforeach; else : ?>
    <tr>
        <th colspan="2">Addresses on File</th>
    </tr>
    <tr>
        <td colspan="2">
            That account does not have any saved addresses.
        </td>
    <?php endif; ?>
</tr>
<tr>
    <td colspan="<?php echo $count; ?>">
        <?php if($count < 4) : ?>
            <form method="post">
                <input type="hidden" name="action" value="view_address_add"/>
                <input type="hidden" name="AccountID" value="<?php echo $accountID; ?>"/>
                <input type="submit" value="Add an Address"/>
            </form>
        <?php else : ?>
            <input disabled="disabled" type="button" value="Add an Address"/>
        <?php endif; ?>
    </td>
</tr>
</table>
</center>
<?php else : ?>
    <p class="error">You do not have permission to view this page.</p><a href="../index.php">Go Back</a>
<?php endif;