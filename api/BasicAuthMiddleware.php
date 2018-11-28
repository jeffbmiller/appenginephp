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
        if ($request->isOptions()) {
            return $response
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        }

        $authHeader = $request->getHeader('Authorization');
        if ($authHeader == null) {
            return $this->getUnauthorizedResponse($response);
        }

        $token = str_replace("token ", "", $authHeader[0]);

        $verifiedIdToken = $this->verifyToken($token);

        if (!$verifiedIdToken) {
            return $this->getUnauthorizedResponse($response);
        }
        $response = $next($request, $response);

        //CORS SETUP
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }

    function getUnauthorizedResponse($response){
        return $response->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withStatus(401)
            ->write("Unauthorized");
    }

    function verifyToken($tokenString){
        if ($tokenString == "Jeff")
            return true;
        return false;
    }
}