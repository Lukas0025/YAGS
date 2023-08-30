$container = new \wsos\structs\container();

$templates = $container->get("templateLoader");
$context   = $container->get("context");

$templates->load("info.html");    
$templates->render($context);
$templates->show();