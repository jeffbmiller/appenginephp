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
//        ServiceAccount::discover(); //Todo This should be all required on App Engine.
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/tidy-muse-841-firebase-adminsdk-16exb-845798e107.json');

        $firebase = (new Factory)
            ->withServiceAccount($serviceAccount)->create();

        $token = str_replace("token ", "", $request->getHeader('Authorization')[0]);

        $verifiedIdToken = $this->verifyFirebaseToken($firebase, $token);

        if ($verifiedIdToken == null) {
                $response = new \Slim\Http\Response(401);
                $response->write("Unauthorized");
                return $response;
        }

        $userId = $verifiedIdToken->getClaim('user_id');
        $user = $firebase->getAuth()->getUser($userId);
        $newRequest = $request->withAttribute('firebase_token', $verifiedIdToken);
        $newRequest = $newRequest->withAttribute('user', $user);
        $response = $next($newRequest, $response);

        //CORS SETUP
        $response = $response->withHeader('Access-Control-Allow-Origin', '*');
        $response = $response->withHeader('Access-Control-Allow-Methods', '*');
        $response = $response->withHeader("Access-Control-Allow-Credentials", "true");

        return $response;

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