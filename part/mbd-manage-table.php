<!-- DISPLAY ALL RESTRICTION TABLE  -->
<div class="wrap">
    <div class="mbd-manage-res-container">
        <h2>Manage All Restriction</h2>
        <table>
            <tr>
                <th>Action</th>
                <th>Province</th>
                <th>Restriciton</th>
                <th>In Store Restriction</th>
                <th>Online Delivery Restriction</th>
            </tr>
            <?php 
                foreach($results as $result){
                    ?>
                    <tr>
                        <td><?php printf("<a href='?page=manage_restrictions&id=%s&edit=1'>Edit</a> | <a href='?page=manage_restrictions&id=%s&delete=1'>Delete</a>", $result->id, $result->id);?></td>
                        <td><?php echo $result->province;?></td>
                        <td><?php echo $result->category_restrictions;?></td>
                        <td><?php echo $result->in_store_restrictions;?></td>
                        <td><?php echo $result->online_delivery_restrictions;?></td>
                    </tr>
                    <?php 
                }
            ?>
        </table>
    </div>
</div>


<?php 
    //To Delete Data
    $id = $_GET['id'] ?? '';
    $edit = $_GET['edit'] ?? '';
    $delete = $_GET['delete'] ?? '';
    if($id && $edit){
        $results = $wpdb->get_row("select * from {$table_name} WHERE id='{$id}'");
        ?>
        <div class="mbd-edit-form-container">
            <h2>Update Restriciton</h2>
            <form method="POST">
                <label for="province"> Select A Province:</label>
                <select name="province" id="province">
                    <option value="AB" <?php is_selected($results->province,'AB');?>>AB</option>
                    <option value="BC" <?php is_selected($results->province,'BC');?>>BC</option>
                    <option value="MB" <?php is_selected($results->province,'MB');?>>MB</option>
                    <option value="NB" <?php is_selected($results->province,'NB');?>>NB</option>
                    <option value="NL" <?php is_selected($results->province,'NL');?>>NL</option>
                    <option value="NS" <?php is_selected($results->province,'NS');?>>NS</option>
                    <option value="NT" <?php is_selected($results->province,'NT');?>>NT</option>
                    <option value="NU" <?php is_selected($results->province,'NU');?>>NU</option>
                    <option value="QC" <?php is_selected($results->province,'QC');?>>QC</option>
                    <option value="PE" <?php is_selected($results->province,'PE');?>>PE</option>
                    <option value="SK" <?php is_selected($results->province,'SK');?>>SK</option>
                    <option value="ON" <?php is_selected($results->province,'ON');?>>ON</option>
                    <option value="YT" <?php is_selected($results->province,'YT');?>>YT</option>
                </select>
                <br><br>
                <label for="category_restrictions">Not Show Category</label>
                <input type="text" name="category_restrictions" id="category_restrictions" value="<?php echo $results->category_restrictions?>">
                <br><br>
                <label for="in_store_restrictions">In Store Restriction</label>
                <input type="text" name="in_store_restrictions" id="in_store_restrictions" value="<?php echo $results->in_store_restrictions?>">
                <br><br>
                <label for="online_delivery_restrictions">Online Delivery Restriction</label>
                <input type="text" name="online_delivery_restrictions" id="online_delivery_restrictions" value="<?php echo $results->online_delivery_restrictions?>">
                <br><br>

                <?php submit_button('Update Restriction', 'primary' , 'mbd-restricton-update');?>
            </form>
        </div>
        <?php 
    }
?>


<?php 
    if(isset($_POST['mbd-restricton-update'])){
        $province = $_POST['province'] ?? '';
        $category_restrictions = $_POST['category_restrictions'] ?? '';
        $in_store_restrictions = $_POST['in_store_restrictions'] ?? '';
        $online_delivery_restrictions = $_POST['online_delivery_restrictions'] ?? '';
            
        $wpdb->update($table_name, ['province' => $province, 'category_restrictions' => $category_restrictions, 'in_store_restrictions' => $in_store_restrictions, 'online_delivery_restrictions' => $online_delivery_restrictions], ['id' => $id]);

        wp_redirect( admin_url( 'admin.php?page=manage_restrictions' ) );
        exit();
    }
?>


<?php 
if($id && $delete){
    $wpdb->delete($table_name, array('id' => $id));
    wp_redirect( admin_url( 'admin.php?page=manage_restrictions' ) );
    exit();
}
