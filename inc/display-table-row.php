<?php 

function display_group_table_single_row($province){
    global $wpdb; 
    $table_name = $wpdb->prefix.'mb_restrictions';
    ?>
        <tr>
            <td><?php echo $province;?></td>
            <td>
                <?php 
                    $results = $wpdb->get_results(" select * from {$table_name} WHERE province = '{$province}'");
                    foreach($results as $data){
                        echo $data->category_restrictions . " ";
                    }
                ?>
            </td>
            <td>
                <?php 
                    $results = $wpdb->get_results(" select * from {$table_name} WHERE province = '{$province}'");
                    foreach($results as $data){
                        echo $data->in_store_restrictions . " ";
                    }
                ?>
            </td>
            <td>
                <?php 
                    $results = $wpdb->get_results(" select * from {$table_name} WHERE province = '{$province}'");
                    foreach($results as $data){
                        echo $data->online_delivery_restrictions . " ";
                    }
                ?>
            </td>
        </tr>
    <?php 
}