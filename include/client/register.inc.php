<?php
$info = $_POST;
if (!isset($info['timezone']))
    $info += array(
        'backend' => null,
    );
if (isset($user) && $user instanceof ClientCreateRequest) {
    $bk = $user->getBackend();
    $info = array_merge($info, array(
        'backend' => $bk::$id,
        'username' => $user->getUsername(),
    ));
}
$info = Format::htmlchars(($errors && $_POST)?$_POST:$info);
