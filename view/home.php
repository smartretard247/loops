<?php if(!$_SESSION['valid_user']) : ?>
<table id="barcrumb">
    <tr>
        <th colspan="2">Login Information</th>
    </tr>
    <form action="core/login.php" method="post">
    <tr>
        <td  colspan="2" style="text-align: right;">
            Username: <input name="Username" type="text">
        </td>
    </tr>
    <tr>
        <td>
            Password: <input name="ThePassword" type="password">
        </td>
    </tr>
    <tr>
        <td colspan="2"><input type="submit" value="Login"/></td> 
    </tr>
    </form>
</table>
<?php endif;