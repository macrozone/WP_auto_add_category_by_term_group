
<?php
/*
Plugin Name: AutoAddCategoryByTermGroup

Description: This Plugin will assign Tags to Categories when saved if they are in the same term_group
Author: Marco Wettstein
Version: 0.1

License: MIT
*/

class AutoAddCategoryByTermGroup
{

	public static function add_category_automatically($post_ID) {
		global $wpdb;
		$tags = wp_get_post_tags($post_ID);
		$catIDs = wp_get_post_categories($post_ID);
		error_log(json_encode($catIDs));
		$options = array(
			"hide_empty" =>0
			);

		foreach ($tags as $oneTag)
		{

			if($oneTag->term_group!= 0)
			{
				error_log(json_encode($oneTag));
				$categories = self::getCategoriesFromGroup($oneTag->term_group);

				foreach($categories as $oneCategory)
				{

					$catIDs[] = intval($oneCategory->term_id,10);

				}
				error_log(json_encode($catIDs));
				wp_set_object_terms($post_ID, $catIDs, 'category', true);
			}

		}
		


	}

	private static function getCategoriesFromGroup($groupID)
	{

		global $wpdb;
		$categories = $wpdb->get_results( 
			$wpdb->prepare( 
				"
				select terms.* from wp_terms terms
				join wp_term_taxonomy tax on terms.term_id = tax.term_id
				where term_group = %d and tax.taxonomy = 'category'
				", $groupID)


			);

		return $categories;
	}
}
add_action('save_post', array('AutoAddCategoryByTermGroup', 'add_category_automatically'));

