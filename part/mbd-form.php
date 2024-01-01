<div class="wrap">
    <div class="mbd-from-container">
        <form method="POST">
            <h2>Add a new Restriction</h2>
            <label for="province"> Select A Province:</label>
            <select name="province" id="province">
                <option value="AB">AB</option>
                <option value="BC">BC</option>
                <option value="MB">MB</option>
                <option value="NB">NB</option>
                <option value="NL">NL</option>
                <option value="NS">NS</option>
                <option value="NT">NT</option>
                <option value="NU">NU</option>
                <option value="QC">QC</option>
                <option value="PE">PE</option>
                <option value="SK">SK</option>
                <option value="ON">ON</option>
                <option value="YT">YT</option>
            </select>
            <br><br>
            <label for="category_restrictions">Restriciton Segval</label>
            <input type="text" name="category_restrictions" id="category_restrictions">
            <br><br>
            <label for="in_store_restrictions">In Store Restriction Segval</label>
            <input type="text" name="in_store_restrictions" id="in_store_restrictions">
            <br><br>
            <label for="online_delivery_restrictions">Online Delivery Restriction Segval</label>
            <input type="text" name="online_delivery_restrictions" id="online_delivery_restrictions">
            <br><br>
        
            <?php submit_button('Add Restriction');?>
        
            <!-- <button type="submit">Add</button> -->
            <br><br>
        
            <?php 
        
                global $wpdb; 
                $table_name = $wpdb->prefix.'mb_restrictions';
        
                $province = $_POST['province'] ?? '';
                $category_restrictions = $_POST['category_restrictions'] ?? '';
                $in_store_restrictions = $_POST['in_store_restrictions'] ?? '';
                $online_delivery_restrictions = $_POST['online_delivery_restrictions'] ?? '';
        
                if($category_restrictions || $in_store_restrictions || $online_delivery_restrictions){
                    echo "Data Added Successful";
        
                    $wpdb->insert($table_name, [
                        'province' => $province,
                        'category_restrictions' => $category_restrictions,
                        'in_store_restrictions' => $in_store_restrictions,
                        'online_delivery_restrictions' => $online_delivery_restrictions
                    ]);
                }
        
            ?>
        </form>
    
    </div>
</div>