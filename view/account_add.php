<?php include_once $_SESSION['rootDir'] . '../core/account.php';
    if($_SESSION['edit_mode']) {
        $cAccount = unserialize($_SESSION['account_obj']);
        $input_username = $cAccount->GetUsername();
        $input_password = '';
        $input_passwordVerify = '';
        $input_lastname = $cAccount->GetLastName();
        $input_firstname = $cAccount->GetFirstName();
        $input_mi = $cAccount->GetMI();
        $input_email = $cAccount->GetEmail();
        $input_title = $cAccount->GetTitle();
        $setting_rowcount = $cAccount->GetPrefRowCount();
    } else {
        $input_username = '';
        $input_password = '';
        $input_passwordVerify = '';
        $input_lastname = '';
        $input_firstname = '';
        $input_mi = '';
        $input_email = '';
        $input_title = '';
        $setting_rowcount = 10;
        $goto_checkout = filter_input(INPUT_GET, 'checkout');
    }
?>
<center>
<form name="formAccountAdd" action="core/account_verify.php" method="post">
    <input type="hidden" name="ID" value="<?php echo $input_username; ?>"/>
    <table class="topmargin">
        <tr>
            <td style="vertical-align: top;">
                <table class="topmargin">
                    <tr>
                        <th colspan="2">Account Information</th>
                    </tr>
                    <tr>
                        <td>Username:</td>
                        <td>
                            <?php if($_SESSION['valid_user']) : ?>
                                <input autocomplete="off" type="text" maxlength="25" value="<?php echo $input_username; ?>" disabled="disabled"/>
                            <?php else : ?>
                                <input autocomplete="off" type="text" name="ReqUsername" maxlength="25" value="<?php echo $input_username; ?>"/>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php if(!$_SESSION['valid_user']) : ?>
                    <tr>
                        <td>Password:</td>
                        <td><input autocomplete="off" type="password" name="ReqPassword" maxlength="25"/></td>
                    </tr>
                    <tr>
                        <td>Verify Password:</td>
                        <td><input autocomplete="off" type="password" name="ReqPasswordVerify" maxlength="25"/></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td>Title:</td>
                        <td>
                            <select name="Title">
                                <?php switch($input_title) {
                                    case "Mr.": echo '<option value="' . $input_title . '" selected="selected">' . $input_title . '</option>';
                                        echo '<option value="Mrs.">Mrs.</option>';
                                        echo '<option value="Ms.">Ms.</option>';
                                        break;
                                    case "Mrs.": echo '<option value="Mr.">Mr.</option>';
                                        echo '<option value="' . $input_title . '" selected="selected">' . $input_title . '</option>';
                                        echo '<option value="Ms.">Ms.</option>';
                                        break;
                                    case "Ms.": echo '<option value="Mr.">Mr.</option>';
                                        echo '<option value="Mrs.">Mrs.</option>';
                                        echo '<option value="' . $input_title . '" selected="selected">' . $input_title . '</option>';
                                        break;
                                    default:
                                        echo '<option value="Mr.">Mr.</option>';
                                        echo '<option value="Mrs.">Mrs.</option>';
                                        echo '<option value="Ms.">Ms.</option>';
                                        break;
                                } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Last Name:</td>
                        <td><input type="text" name="LastName" maxlength="25" value="<?php echo $input_lastname; ?>"/></td>
                    </tr>
                    <tr>
                        <td>First Name:</td>
                        <td><input type="text" name="FirstName" maxlength="25" value="<?php echo $input_firstname; ?>"/></td>
                    </tr>
                    <tr>
                        <td>Middle Initial:</td>
                        <td><input type="text" name="MI" maxlength="1" value="<?php echo $input_mi; ?>" size="1"/></td>
                    </tr>
                    <tr>
                        <td>Email:</td>
                        <td>
                            <input autocomplete="off" type="text" name="Email" maxlength="255" value="<?php echo $input_email; ?>"/>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="vertical-align: top;">
                <table class="topmargin">
                    <tr>
                        <th>Settings</th>
                    </tr>
                    <tr>
                        <td>Maximum items to display per page: 
                            <select name="PrefRowCount">
                                <?php for($i = 5; $i <= 50; $i+=5) : ?>
                                    <?php if($setting_rowcount == $i) : ?>
                                        <option value="<?php echo $i; ?>" selected="selected"><?php echo $i; ?></option>
                                    <?php else : ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border-top: 1px dotted silver;">
                <?php if($_SESSION['valid_user']) : ?>
                    <input type="submit" value="Save Changes"/>
                <?php else : ?>
                    <input type="submit" value="Create Account"/>
                    <input type="hidden" value="<?php echo $goto_checkout; ?>" name="checkout"/>
                <?php endif; ?>
                    
                <?php if($_SESSION['valid_user']) : ?>
                    <input onclick="window.location='index.php?action=delete_account';" type="button" value="Delete My Account"/>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</form>
</center>