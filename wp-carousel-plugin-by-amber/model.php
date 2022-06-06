<?php
/**
 * Model dla carousel-plugin
 */
class LogoCarousel {
 
    private $tableName;
    private $wpdb;
 
    public function __construct() {
        global $wpdb;
        $prefix = $wpdb->prefix;
        $this->tableName = $prefix . "rm_logo_carousel";
        $this->wpdb = $wpdb;
    }
 
    /**
     * Pobiera wszystkie obrazki
     * @global $wpdb $wpdb
     * @return array Tablica z obrazkami
     */
    public function getAll() {
        $query = "SELECT * FROM  " . $this->tableName . " ORDER BY id DESC;";
        return $this->wpdb->get_results($query, ARRAY_A);
    }
 
    /**
     * Dodaje obrazki
     * @global $wpdb $wpdb
     * @param array $data
     */
    public function add($data) {
        $this->wpdb->insert($this->tableName, $data, array('%s', '%s', '%s'));
    }
 
    /**
     * Usuwa wszystkie obrazki
     * @global $wpdb $wpdb
     */
    public function deleteAll() {
        $sql = "TRUNCATE TABLE " . $this->tableName;
        $this->wpdb->query($sql);
    }
 
}
 
/**
 * Wyswietla formularz do edycji obrazkow
 */
function rmlc_images() {
    $model=new LogoCarousel();
    if (isset($_POST['rmlc_images'])) {
        $model->deleteAll();
        foreach ($_POST['rmlc_images'] as $img) {
            $model->add(array('image' => $img['image'], 'link' => $img['link'], 'title' => $img['title']));
        }
    }
    $results = $model->getAll();
 
    echo '<h2>' . __('Images') . '</h2>';
    echo '<form action="?page=rmlc_images" method="post">';
    echo '<table class="form-table" style="width:auto;" cellpadding="10">
        <thead>
        <tr>
        <td>' . __('Image') . '</td><td>' . __('Link') . '</td><td>' . __('Title') . '</td><td>' . __('Delete') . '</td>
        </tr>
        </thead>
        <tbody class="items">';
    $i=0;
    foreach ($results as $row) {
        echo '<tr>
            <td><input name="rmlc_images['.$i.'][image]" type="text" value="' . $row['image'] . '" /></td>';
        echo '<td><input name="rmlc_images['.$i.'][link]" type="text" value="' . $row['link'] . '" /></td>';
        echo '<td><input name="rmlc_images['.$i.'][title]" type="text" value="' . $row['title'] . '" /></td>';
        echo '<td><a class="delete" href="">' . __('Delete') . '</a></td>
            </tr>';
        $i++;
    }
    echo '</tbody><tr><td colspan="4"><a class="add" href="">' . __('Add') . '</a></td></tr>';
    echo '<tr><td colspan="4"><input type="submit" value="' . __('Save') . '" /></td></tr>';
    echo '</table>';
    echo '</form>';
     
    echo '
        <script type="text/javascript">
        jQuery(document).ready(function($) {
        $("table .delete").click(function() {
        $(this).parent().parent().remove();
        return false;
        });
        $("table .add").click(function() {
        var count = $("tbody.items tr").length+1;
        var code=\'<tr><td><input type="text" name="rmlc_images[\'+count+\'][image]" /></td><td><input type="text" name="rmlc_images[\'+count+\'][link]" /></td><td><input type="text" name="rmlc_images[\'+count+\'][title]" /></td><td><a class="delete" href="">' . __('Delete') . '</a></td></tr>\';
        $("tbody.items").append(code);
        return false;
        });
        });
        </script>
        ';
}



?>