<?php

function payment_made($subscription){
    if(!$subscription){
        Islandsvefir_Logger::add_error('payment made', 'No Subscription');
        return;
    }
    Islandsvefir_Logger::add_success('payment made -> Started',$subscription);
    $user_id = $subscription->get_user_id();
    $email = $subscription->billing_email;
    if($user_id) {
       $user = get_userdata($user_id);
       $email = $user->user_email;
        Islandsvefir_Logger::add_success('payment_made -> User -> Email', $email);
    }
    else {
        Islandsvefir_Logger::add_error('payment_made -> User -> Email', $email);
    }
    $shows = Islandsvefir_Subscription_Transistor_Request::get_private_shows();
    if(is_array($shows)) {
        Islandsvefir_Logger::add_success('payment_made -> get_private_shows', $shows);
        foreach ($shows as $show) {
            $subscribed = Islandsvefir_Subscription_Transistor_Request::create_subscriber($email,$show['id']);
            if(isset($subscribed['relationships']['id'])) {
                if($subscribed['relationships']['id'] == $show['id']) {
                    Islandsvefir_Logger::add_success('payment_made -> create_subscriber', $email);
                }
            }
            else {
                Islandsvefir_Logger::add_error('payment_made -> create_subscriber', $email);
            }
        }
    }
    else {
        Islandsvefir_Logger::add_error('payment_made -> get_private_shows', $shows);
    }
}
add_action("woocommerce_subscription_payment_complete", "payment_made",10,1);

function status_updated($subscription, $old_status, $new_status){
    if(!$subscription){
        Islandsvefir_Logger::add_error('status_update', 'No Subscription');
        return;
    }
    Islandsvefir_Logger::add_success('status_update -> Started',$subscription);
        $user_id = $subscription->get_user_id();
        if(isset($subscription->billing_email)){
        $email = $subscription->billing_email;
        }
        if($user_id) {
            $user = get_userdata($user_id);
            $email = $user->user_email;
            Islandsvefir_Logger::add_success('status_update -> User -> Email', $email);
        }
        else {
            Islandsvefir_Logger::add_error('status_update -> User -> Email', $email);
        }
        $shows = Islandsvefir_Subscription_Transistor_Request::get_private_shows();
        if(is_array($shows)) {
            Islandsvefir_Logger::add_success('status_update -> get_private_shows', $shows);
            foreach ($shows as $show) {
                if ($new_status == 'active') {
                    $subscribed = Islandsvefir_Subscription_Transistor_Request::create_subscriber($email, $show['id']);
                    if (isset($subscribed['relationships']['id'])) {
                        if ($subscribed['relationships']['id'] == $show['id']) {
                            Islandsvefir_Logger::add_success('status_update -> create_subscriber', $email);
                        }
                    } else {
                        Islandsvefir_Logger::add_error('status_update -> create_subscriber', $email);
                    }
                } else {
                    $subscribed = Islandsvefir_Subscription_Transistor_Request::remove_subscriber($email, $show['id']);
                    if (isset($subscribed['relationships']['id'])) {
                        if ($subscribed['relationships']['id'] == $show['id']) {
                            Islandsvefir_Logger::add_success('status_update -> remove_subscriber', $email);
                        }
                    } else {
                        Islandsvefir_Logger::add_error('status_update -> remove_subscriber', $email);
                    }
                }
            }
        }
        else {
                Islandsvefir_Logger::add_error('status_update -> get_private_shows', $shows);
            }


   }
add_action("woocommerce_subscription_status_updated", "status_updated",10,3);