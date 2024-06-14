<?php

$usuario = null;

if (isset($usuario)) {
    echo "Usuario definido";
} else {
    include_once("errors/403.php");
}