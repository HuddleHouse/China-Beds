<?php
namespace AppBundle\Security;

use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface {

    protected $router;
    protected $authorizationChecker;

    public function __construct(Router $router, AuthorizationChecker $authorizationChecker) {
        $this->router = $router;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token) {

        $response = null;

//        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
//            $response = new RedirectResponse($this->router->generate('admin_home'));
//        } else if ($this->authorizationChecker->isGranted('ROLE_USER')) {
//
//        session_save_path('/tmp');
//        define('OAUTH_CONSUMER_KEY', 'qyprdYXwOz6ISB9B1i92GSQJRx1Ym6');
//        define('OAUTH_CONSUMER_SECRET', 'gsUUSmzFcbJdP1058nK2ztEcYn9rL2oP2Q4s2cEd');
//
//        define('OAUTH_REQUEST_URL', 'https://oauth.intuit.com/oauth/v1/get_request_token');
//        define('OAUTH_ACCESS_URL', 'https://oauth.intuit.com/oauth/v1/get_access_token');
//        define('OAUTH_AUTHORISE_URL', 'https://appcenter.intuit.com/Connect/Begin');
//// The url to this page. it needs to be dynamic to handle runnable's dynamic urls
//        define('CALLBACK_URL','http://'.$_SERVER['HTTP_HOST'].'/PHPOAuthSample/oauth.php');
//// cleans out the token variable if comming from
//// connect to QuickBooks button
//        if ( isset($_GET['start'] ) ) {
//            unset($_SESSION['token']);
//        }
//
//        try {
//            $oauth = new OAuth( OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
//            $oauth->enableDebug();
//            $oauth->disableSSLChecks(); //To avoid the error: (Peer certificate cannot be authenticated with given CA certificates)
//            if (!isset( $_GET['oauth_token'] ) && !isset($_SESSION['token']) ){
//                // step 1: get request token from Intuit
//                $request_token = $oauth->getRequestToken( OAUTH_REQUEST_URL, CALLBACK_URL );
//                $_SESSION['secret'] = $request_token['oauth_token_secret'];
//                // step 2: send user to intuit to authorize
//                header('Location: '. OAUTH_AUTHORISE_URL .'?oauth_token='.$request_token['oauth_token']);
//            }
//
//            if ( isset($_GET['oauth_token']) && isset($_GET['oauth_verifier']) ){
//                // step 3: request a access token from Intuit
//                $oauth->setToken($_GET['oauth_token'], $_SESSION['secret']);
//                $access_token = $oauth->getAccessToken( OAUTH_ACCESS_URL );
//
//                $_SESSION['token'] = serialize( $access_token );
//                $_SESSION['realmId'] = $_REQUEST['realmId'];  // realmId is legacy for customerId
//                $_SESSION['dataSource'] = $_REQUEST['dataSource'];
//
//                $token = $_SESSION['token'] ;
//                $realmId = $_SESSION['realmId'];
//                $dataSource = $_SESSION['dataSource'];
//                $secret = $_SESSION['secret'] ;
//                // write JS to pup up to refresh parent and close popup
//                echo '<script type="text/javascript">
//            window.opener.location.href = window.opener.location.href;
//            window.close();
//          </script>';
//            }
//
//        } catch(OAuthException $e) {
//            echo "Got auth exception";
//            echo '<pre>';
//            print_r($e);
//        }
//



            $response = new RedirectResponse($this->router->generate('fos_user_profile_show'));
//            $response = new RedirectResponse($this->router->generate('user_home'));
//        }

        return $response;
    }

}