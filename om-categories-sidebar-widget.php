<?php
class OM_CategoryHierarchyWidget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'category_hierarchy_widget',
            'OM Category Hierarchy Widget',
            array('description' => 'Displays the hierarchy of the current product category.')
        );
    }

    public function widget($args, $instance) {
        // dont load on search page
        if (is_search()) {
            return;
        }
        // get current category
        $current_category = get_queried_object();
        // check if it is acctually a category and has id
        if ($current_category && isset($current_category->term_id)) {
            // Get ancestors
            $ancestors = get_ancestors( $current_category->term_id, 'product_cat', 'taxonomy' );
            // Check if current category is first level category or has parent 
            $MainParent = (empty($ancestors)) ?  $current_category->term_id : end($ancestors);
            // Get the formated data for the given id
            $output = $this->get_category_hierarchy($MainParent, $current_category->term_id);
            if (!empty($output)) {
                echo $args['before_widget'];
                if (!empty($instance['title'])) {
                    echo $args['before_title'] . $instance['title'] . $args['after_title'];
                }
                echo '<ul class=" om_childrenWrapper">' . $output . '</ul>';
                echo $args['after_widget'];
            }
        }       
        //  do the same for shop page 
        if (is_shop()) {
            echo $args['before_widget'];
            if (!empty($instance['title'])) {
                    echo $args['before_title'] . $instance['title'] . $args['after_title'];
                }
            $output = '<ul class="om__category_hierarchy_widget om_childrenWrapper">';
            $categories = get_terms(array(
                'taxonomy' => 'product_cat',
                'parent' => 0,
                'hide_empty' => false,
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'exclude' => array(15)
            ));

            foreach ($categories as $category) {
                $output .= $this->get_category_hierarchy($category->term_id,0);
            }

            $output .= '</ul>';
            echo $output;
            echo $args['after_widget'];
        }


    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        return $instance;
    }

    private function get_category_hierarchy($category_id, $CurrentCategory_id) {
        $output  = '';
        $Current = '';
        // get product category object 
        $category = get_term($category_id, 'product_cat');

        if ($category) {
            // Add addition class to current item. We will use it in the future
            $Current = $category->term_id ;
            if ($category->term_id == $CurrentCategory_id) $Current .= " om_CurrentCat_item" ;
            // Get children of current category
            $child_categories = get_terms(array(
                'taxonomy' => 'product_cat',
                'parent' => $category_id,
                'hide_empty' => false,
                'menu_order' => 'ASC'
            ));
           
            // if has children then get the bellow format 
            if ($child_categories) {
                 $output .= '<li class="item om_HasChildren '.$Current.'" > <div class="om_labelWRapper"><a href="' . get_term_link($category->term_id, 'product_cat') . '">' . $category->name . '</a><span class="om_cat_toggler">+</span></div>';

                $output .= '<ul class="om_childrenWrapper">';
                // loop through the children of current category
                foreach ($child_categories as $child_category) {
                    // recursice call of current functions
                    $output .= $this->get_category_hierarchy($child_category->term_id,$CurrentCategory_id );
                }
                $output .= '</ul>';
            }else{
                // display the category element without children 
                 $output .= '<li class="item om_HasChildren '.$Current.'" ><div class="om_labelWRapper"><a href="' . get_term_link($category->term_id, 'product_cat') . '">' . $category->name . '</a></div>';
            }

            $output .= '</li>';
         }

        return $output;
    }


}

function register_category_hierarchy_widget() {
    register_widget('OM_CategoryHierarchyWidget');
}
add_action('widgets_init', 'register_category_hierarchy_widget');

