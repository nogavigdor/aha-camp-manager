<?php

/**
 * Plugin Name: Aha camp manager
 * Description: Manages camp form, submissions and emailing
 * Version: 1.0
 * Author: Noga Vigdor
 * Author URI: https://noga.digital
 * Text Domain: aha-camp-manager
 * License: GPLv2 or later
 *
 * Copyright 2023 noga.digital
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

 add_filter('the_content', 'add_a_line_to_end_of_post');

 function add_a_line_to_end_of_post($content) {

    if (is_page() && is_main_query()) {
        return $content.'<p>Howdy from Noga</>';
    }

    return $content;
    }
   