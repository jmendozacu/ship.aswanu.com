<?php
//auth
$acl = new Mivec_Acl($_SERVER['PHP_AUTH_USER'] , $_SERVER['PHP_AUTH_PW']);

$acl->setPermission(1)
    ->isAllowed();