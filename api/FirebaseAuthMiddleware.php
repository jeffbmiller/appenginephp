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

class FirebaseAuthMiddleware
{
    public function __invoke($request, $response, $next)
    {
        if ($request->isOptions()) {
            return $response
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        }

//        ServiceAccount::discover(); //Todo This should be all required on App Engine.
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/tidy-muse-841-firebase-adminsdk-16exb-845798e107.json');

        $firebase = (new Factory)
            ->withServiceAccount($serviceAccount)->create();

        $authHeader = $request->getHeader('Authorization');
        if ($authHeader == null) {
            return $this->getUnauthorizedResponse($response);
        }

        $token = str_replace("token ", "", $authHeader[0]);

        $verifiedIdToken = $this->verifyFirebaseToken($firebase, $token);

        if ($verifiedIdToken == null) {
                return $this->getUnauthorizedResponse($response);
        }

        $userId = $verifiedIdToken->getClaim('user_id');
        $user = $firebase->getAuth()->getUser($userId);
        $newRequest = $request->withAttribute('firebase_token', $verifiedIdToken);
        $newRequest = $newRequest->withAttribute('user', $user);
        $response = $next($newRequest, $response);

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

    function verifyFirebaseToken($firebase, $tokenString){
        try {
            $verifiedIdToken = $firebase->getAuth()->verifyIdToken($tokenString);
            return $verifiedIdToken;
        }
        catch (InvalidToken $e) {
            return null;
        }
    }
}