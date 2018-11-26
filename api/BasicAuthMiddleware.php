<?php
/**
 * Created by PhpStorm.
 * User: jeffmiller
 * Date: 2018-11-19
 * Time: 1:35 PM
 */

namespace Api;

use Firebase\Auth\Token\Exception\InvalidToken;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Factory;
use League\Flysystem\Exception;

class BasicAuthMiddleware
{
    public function __invoke($request, $response, $next)
    {
        $authHeader = $request->getHeader('Authorization');
        if ($authHeader == null) {
            return $this->getUnauthorizedResponse();
        }

        $token = str_replace("token ", "", $authHeader[0]);

        $verifiedIdToken = $this->verifyToken($token);

        if (!$verifiedIdToken) {
            return $this->getUnauthorizedResponse();
        }
        $response = $next($request, $response);

        //CORS SETUP
        $response = $response->withHeader('Access-Control-Allow-Origin', '*');
        $response = $response->withHeader('Access-Control-Allow-Methods', '*');
        $response = $response->withHeader("Access-Control-Allow-Credentials", "true");
        return $response;
    }

    function getUnauthorizedResponse(){
        $response = new \Slim\Http\Response(401);
        $response->write("Unauthorized");
        return $response;
    }

    function verifyToken($tokenString){
        if ($tokenString == "Jeff")
            return true;
        return false;
    }
}