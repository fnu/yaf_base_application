<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 * Sets an authenticity token to session and validates it against POST 
 * submissions.
 *
 * To enable it set it On at config/application.ini file
 * <code>
 * application.protect_from_csrf=1
 * </code>
 *
 * Then you must define an input hidden field in each html form you submit.
 * <code>
 * <input type="hidden" name="auth_token" value="<?php echo Yaf\Session::getInstance()->auth_token ?>">
 * </code>
 *
 * After submission of the form, the plugin will attempt to validate the 
 * auth_token an will throw an \Exception if tokens are not equal.
 */
class AuthTokenPlugin extends Yaf\Plugin_Abstract
{

    public function routerStartup(Yaf\Request_Abstract $request , 
        Yaf\Response_Abstract $response
    ) {

    }

    public function dispatchLoopStartup(Yaf\Request_Abstract $request, 
        Yaf\Response_Abstract $response 
    ){

        $this->verify_auth_token($request);
    }

    protected function verify_auth_token($request)
    {
        $config = Yaf\Registry::get("config");
        
        if (   $config['application']['protect_from_csrf']
            && $request->isPost())
        {
            $post = $request->getPost();
            if (   !isset($post['auth_token']) 
                || $post['auth_token'] !== $this->auth_token()
            ){
                throw new \Exception('Invalid authenticity token!');
            }
        }
    }

    protected function auth_token()
    {
        $session = Yaf\Session::getInstance();
        $session->auth_token = $session->auth_token
            ?: base64_encode(sha1(uniqid(rand(), true)));
        return $session->auth_token;
    }

}
