<table class="topmargin" align="center">
    <tr>
        <td colspan="2" style="border-top: 1px solid black; padding: 20px 5px 20px 5px;">
            <form action="core/upload_file.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="ID" value="<?php echo $id; ?>"/>
                <label for="CategoryName">Photo category:</label>
                <select name="CategoryName" value="<?php echo $input_categoryName; ?>">
                    <?php if($_SESSION['edit_mode']) : ?>
                        <option value="<?php echo $input_categoryName; ?>"><?php echo $input_categoryName; ?></option>
                    <?php endif; ?>
                    <?php foreach($item_category as $tCategory) : ?>
                        <option value="<?php echo $tCategory['Name']; ?>"><?php echo $tCategory['Name']; ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="file">File to upload:</label>
                <input type="file" name="ImgFile" id="file"><br/>
                <input type="submit" name="submit" value="Upload">
            </form>
        </td>
    </tr>
</table>