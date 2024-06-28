<?php

define('TRANSISTOR_BASEURL','http://api.transistor.fm/v1/');
define('TRANSISTOR_APIKEY', 'MkmgsSLr0MEuGtdNPclS_g');

if (!class_exists('Islandsvefir_Subscription_Transistor_Request')):
    class Islandsvefir_Subscription_Transistor_Request
    {
        public static function get_private_shows()
        {
            $params = array(
                'private' => 'true'
            );

            $results = self::request('shows',$params);
            if(!isset($results['results'])) {
                return array();
            }
            return $results['results'];
        }

        public static function create_subscriber($email, $show_id)
        {
            $params = array(
                'show_id' => $show_id,
                'email' => $email
            );

            $results = self::request('subscribers',$params, "POST");
            return $results['results']['data'];
        }

        public static function remove_subscriber($email, $show_id)
        {
            $params = array(
                'show_id' => $show_id,
                'email' => $email
            );

            $results = self::request('subscribers', $params, "DELETE");
            return $results['results'];
        }
        public static function request($endpoint, $params = array(), $method = 'GET')
        {

            $response = array();
            $curl = curl_init();
            $headers = array(
                'x-api-key: ' . TRANSISTOR_APIKEY
            );

            $url =  'https://api.transistor.fm/v1/'. $endpoint;
            $curl_options = array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_HTTPHEADER => $headers,
            );


            $methods = array(
                'PUT',
                'POST',
                'DELETE',
            );

            if (in_array($method, $methods) && count($params) > 0) {

                $postfields = $params;
                $postfields_json = json_encode($postfields);
                $curl_options[CURLOPT_POSTFIELDS] = $postfields_json;


            }
            curl_setopt_array($curl, $curl_options);
            $result = curl_exec($curl);
            $response['results'] = json_decode($result, true);
            if (curl_errno($curl)) {
                $response['error'] = curl_error($curl);
                Islandsvefir_Logger::add_error('Request', $response);
            } else {
                $response['http_code'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            }
            Islandsvefir_Logger::add_success('Request', $response);
            curl_close($curl);

            return $response;

        }
    }
endif;