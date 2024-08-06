<?php
    $container = new \wsos\structs\container();
    $auth      = $container->get("auth");
    
    $auth->logout();

    header('Location: /');
?>