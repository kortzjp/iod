<?php

function isEmail($email) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return true;
    } else {
        return false;
    }
}

function hashPassword($password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    return $hash;
}

function isNullLogin($usuario, $password) {
    if (strlen(trim($usuario)) < 1 || strlen(trim($password)) < 1) {
        return true;
    } else {
        return false;
    }
}

function recoge($var) {
    $tmp = (isset( $_REQUEST[$var] ) ) 
            ? trim(htmlspecialchars( $_REQUEST[$var], ENT_QUOTES, "UTF-8") ) 
            : "";
    return $tmp;
}
















