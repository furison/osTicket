<?php
$info = ($_POST && $errors)?Format::htmlchars($_POST):array();

$ext_bks = array();
foreach (StaffAuthenticationBackend::allRegistered() as $bk){
    if ($bk instanceof ExternalAuthentication) {
        $ext_bks[] = $bk;
    }
}