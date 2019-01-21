<center>
    <form name="formAccountDelete" action="core/account_delete.php" method="post">
        <input type="hidden" name="AccountID" value="<?php echo $_SESSION['valid_user']; ?>"/><br/>
        <table class="topmargin">
            <tr>
                <th colspan="2">Delete Account</th>
            </tr>
            <tr>
                <td>Please tell us why you are deciding <br/>to close your account with us:</td>
                <td><textarea name="Reason" maxlength="254" cols="50" rows="6" style="resize: none;"></textarea></td>
            </tr>
            <tr>
                <td colspan="2">
                    <?php if($_SESSION['valid_user']) : ?>
                        <input type="submit" value="Submit"/>
                    <?php else : ?>
                        <input type="submit" value="Submit" disabled="disabled"/>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </form>
</center>