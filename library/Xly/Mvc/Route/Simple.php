<?php

namespace Xly\Mvc\Route;

use Xly\Mvc\Http\Request;

class Simple extends AbstractRoute {
    
    const DEFAULT_CONTROLLER = "IndexController.php";

    const DEFAULT_ACTION = "index";    

    public function match(Request $request) {
        $uri = ltrim($request->getRequestURI(), DIRECTORY_SEPARATOR);
        $uri = !empty($uri) ? strtolower($uri) : DIRECTORY_SEPARATOR;
        return $this->setUri($uri);
    }

}// END OF CLASS
