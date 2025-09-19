<?php

require_once '../classes/user_class.php';


function register_user_ctr($name, $email, $password, $phone_number, $role)
{
    $user = new User();
    $user_id = $user->createUser($name, $email, $password, $phone_number, $role);
    if ($user_id) {
        return $user_id;
    }
    return false;
}
function login_customer_ctr($email, $password)
{
    $user = new User();
    $customer = $user->getUserByEmail($email);

    if ($customer && password_verify($password, $customer['customer_pass'])) {
        return $customer;
    }
    return false;
}
function get_user_by_email_ctr($email)
{
    $user = new User();
    return $user->getUserByEmail($email);
}
