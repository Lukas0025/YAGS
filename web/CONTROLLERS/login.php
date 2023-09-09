<?php
    $container = new \wsos\structs\container();

    $templates = $container->get("templateLoader");
    $context   = $container->get("context");
    $auth      = $container->get("auth");

    //add basic info to context
    $context["pagename"] = "login";
    $context["fail"]     = false;
    $context["failInfo"] = [];

    //pass login
    if (isset($_POST["username"]) && isset($_POST["password"])) {

        if ($auth->login($_POST["username"], $_POST["password"])) {
            header("Location: /");
            die("logined");
        }

        $context["fail"] = true;
        $context["failInfo"] = [
            "title"       => "Login failed!",
            "description" => "Login failed because the user does not exist or the correct password was not used. Please try again."
        ];
    }

    $templates->load("login.html");    
    $templates->render($context);
    $templates->show();
?>