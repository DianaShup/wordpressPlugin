<?php
/** 
 * Plugin Name:       Content In The Middle
 * Plugin URI:        https://example.com/plugins/ContentInTheMiddle/ 
 * Description:       Shows content between title and post
 * Version:           1.0 
 * Requires at least: 5.0 
 * Author:            Dziyana Shupliakova, Uladzislau Lapko
 * Author URI:        https://darksource.pl
 * License:           GPL v2 or later 
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html 
 */


//rejestrowanie linku w menu administratora
function citm_post_admin() {
    add_menu_page("Content in the Middle", "Add content", 'manage_options', "add_content", "citm_post_admin_page", "dashicons-welcome-add-page");    
} 
add_action('admin_menu', 'citm_post_admin'); 



//obsluga strony administratora
function citm_post_admin_page() { 
     // get _POST variable from globals                                     
    global $_POST; 
    $opContentList  =  is_array(get_option('citm_post_list')) ? get_option('citm_post_list') : [];
                
	//delete
    if(isset($_POST['id_value']) and $_POST['id_value']!=="") {
        
        unset($opContentList[$_POST['id_value']]); //usuwa
        update_option('citm_post_list', $opContentList); 
        echo'<div class="notice notice-error is-dismissible"><p>Removed.</p></div>';    
    } 
	//create
    if(isset($_POST['citm_post_new']) and $_POST['citm_post_new']!=="") { //not null
        if(strip_tags($_POST['citm_post_new']) != $_POST['citm_post_new']){ //zwraca line
            array_push($opContentList, $_POST['citm_post_new']); //dodaje na koniec element
            update_option('citm_post_list', $opContentList); 
            echo'<div class="notice notice-success is-dismissible"><p>New content is added.</p></div>';
        }
        else{
            update_option('citm_post_list', $opContentList); 
            echo'<div class="notice notice-error is-dismissible"><p>Content must have <b>HTML</b> syntax</p></div>'; 
        }    
    }

    ?>
    <div class="wrap center">
        <form name="citm_create_post" method="post">
            <div style="width: 100%;">
                <div >
                    <h1 class="title">Your Content</h1>
                    <textarea name="citm_post_new" class="insertText" type="text"></textarea>
                </div>
                <p class="submitButton">
                    <button type="submit" style="margin: 0 auto;">Create new</button>
                </p>
            </div>
        </form>
        <h1 class="title">Content For Post</h1>
        <div class="spaceAds">
        <?php foreach ($opContentList as $key=>$value) : ?>
            <form class='element' method='post' name='delete_post'>
                <div class='content'>
                    <?= $value ?>
                </div>
                <input type='hidden' id='id_value' name='id_value' value=<?= $key ?> >
                <button class='deleteButton' type='submit'>REMOVE</button>
            </form>
        <?php endforeach ?>    
        </div>
    </div>
    <?php
}


//wstawianie i losowanie
function insertContent($content){ 
    if(is_singular() && in_the_loop() && is_main_query()){
        $opContentList  =  is_array(get_option('citm_post_list')) ? get_option('citm_post_list') : []; //Finds whether the given variable is an array.
        $randomElement = $opContentList[array_rand($opContentList, 1)]; //took random keys of array
        if(!empty($opContentList)){
            $custom = "<div class='center' style='padding: 2rem; border: solid gray 2px; width: 100%'>$randomElement</div>";
            return $custom.$content;
        }
        return $content;
    }
    return $content;
}
add_filter('the_content', "insertContent");

//wyswietlanie
function show_content() { 
    $opContentList  =  is_array(get_option('citm_post_list')) ? get_option('citm_post_list') : [];
        $randomElement = $opContentList[array_rand($opContentList, 1)];
        if(!empty($opContentList)){
            $custom = "<div class='center' style='padding: 2rem; border: solid gray 2px; width: 100%'>$randomElement</div>";
            return $custom;
        }
    return "<div></div>";
}

add_shortcode( 'ad_content', 'show_content'); //Adds a new shortcode.

function citm_register_styles() { 
    wp_register_style('citm_styles', plugins_url('/css/style.css', __FILE__));// rejestruje nasz plik styli pod nazwą z pierwszego argumentu
    wp_enqueue_style('citm_styles'); //wskazuje aby dołączyć plik zarejestrowany pod nazwą z argumentu do htmla strony. 
    
    
} 
add_action('init', 'citm_register_styles');