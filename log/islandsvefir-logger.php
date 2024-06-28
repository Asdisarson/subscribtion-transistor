<?php

define('ISLANDSVEFIR_DEVELOPMENT', true);
global $islandsvefir_logger_db_version;
$islandsvefir_logger_db_version = '0';

if(!class_exists('Islandsvefir_Logger')) :
    class Islandsvefir_Logger {
    public function __construct()
    {

    }

        public static function add_to_log($type, $text)
        {
            global $wpdb;

            $table_name = $wpdb->prefix . 'islandsvefir_log';
            $array = array(
                'Text' => $text
            );
            $wpdb->insert(
                $table_name,
                array(
                    'type' => $type,
                    'text' =>   json_encode($array)
                )
            );
        }
        public static function add_error($function_name,$error_message = '', $type_of_error = 'ERROR')
        {
            if(!ISLANDSVEFIR_DEVELOPMENT) {
                return;
            }

            global $wpdb;

            $table_name = $wpdb->prefix . 'islandsvefir_log';

            $array = array(
                'Function' => $function_name,
                'Error Message' => $error_message
            );

            $wpdb->insert(
                $table_name,
                array(
                    'type' => $type_of_error,
                    'text' =>   json_encode($array)
                )
            );
        }

        public static function add_success($function_name, $success_message = '', $type_of_success = 'SUCCESS')
        {
            if(!ISLANDSVEFIR_DEVELOPMENT) {
                return;
            }
            global $wpdb;

            $table_name = $wpdb->prefix . 'islandsvefir_log';

            $array = array(
                'Function' => $function_name,
                'Success Message' => $success_message
            );

            $wpdb->insert(
                $table_name,
                array(
                    'type' => $type_of_success,
                    'text' =>   json_encode($array)
                )
            );
    }

        public static function get_all($query = '')
        {
            global $wpdb;

            $table_name = $wpdb->prefix . 'islandsvefir_log';

            if($query != '') {
                $query = " WHERE type = $query";
            }

            $sql = "SELECT * FROM $table_name $query";

            $results = $wpdb->get_results($sql,'ARRAY_A');

            if(!$results) {
                return array();
            }
            return $results;

        }

        public static function get_all_errors()
        {
            return self::get_all('ERROR');
        }

        public static function get_all_success()
        {
            return self::get_all('SUCCESS');
        }
        public static function clear()
        {
            global $wpdb;

            $table_name = $wpdb->prefix . 'islandsvefir_log';

            $sql = "TRUNCATE TABLE $table_name";

            $wpdb->query($sql);

        }
    }
    endif;
if(!function_exists('upgrade_islandsvefir_log')) :
function upgrade_islandsvefir_log() {
    $saved_version = (int) get_site_option('islandsvefir_logger_db_version');
    install_islandsvefir_logger_tables();
        update_site_option('islandsvefir_logger_db_version', 100);

}
endif;
if(!function_exists('install_islandsvefir_logger_tables')) :
    function install_islandsvefir_logger_tables() {
        global $wpdb;
        global $islandsvefir_logger_db_version;

        $table_name = $wpdb->prefix . 'islandsvefir_log';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE " . $table_name . " (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                time datetime DEFAULT CURRENT_TIMESTAMP,
                type tinytext NOT NULL, 
                text text NOT NULL,
                PRIMARY KEY  (id)
                ) " . $charset_collate ." ; ";

        require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        add_option('islandsvefir_logger_db_version', $islandsvefir_logger_db_version);

        $success = empty($wpdb->last_error);
        install_islandsvefir_logger_data();
        return $success;

    }
endif;

if(!function_exists('install_islandsvefir_logger_data')):
    function install_islandsvefir_logger_data() {
        global $wpdb;
        global $islandsvefir_logger_db_version;

        $type = 'Install/Update';
        $text = 'Islandsvefir Logger Installed Version' . $islandsvefir_logger_db_version;

        $table_name = $wpdb->prefix . 'islandsvefir_log';

        $wpdb->insert(
            $table_name,
            array(
                'type' => $type,
                'text' => $text
            )
        );

    }
endif;
