<?php

function maskPhone($number){
    $number = preg_replace('/\D/', "", $number);
    $number = preg_replace('/^0/', "", $number);
    switch(strlen($number)){
        case 10:
            preg_match('/(.{2})(.{4})(.{4})/', $number,  $matches);
            array_shift($matches);
            $telephone = vsprintf('(%s) %s-%s', $matches);
            break;
        case 11:
            preg_match('/(.{2})(.{5})(.{4})/', $number,  $matches);
            array_shift($matches);
            $telephone = vsprintf('(%s) %s-%s', $matches);
            break;
        case 8:
            preg_match('/(.{4})(.{4})/', $number,  $matches);
            array_shift($matches);
            $telephone = vsprintf('%s-%s', $matches);
            break;
        case 9:
            preg_match('/(.{5})(.{4})/', $number,  $matches);
            array_shift($matches);
            $telephone = vsprintf('%s-%s', $matches);
            break;
    }
    return $telephone;
}