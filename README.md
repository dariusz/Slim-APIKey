# Slim-APIKey

Slim-APIKey requires that clients provide an API key in order to make requests from your application.

## Basic Usage

All you need to provide is an array of valid API keys.  Slim-APIKey will look for a parameter called 'api_key' in your GET and POST parameters, and make sure the value is valid.

    use \Slim\Slim;
    use \Slim\Middleware\APIKey;
    
    $valid_keys = array(
		'validkey001',
		'validkey002'
    );

    $app = new Slim();
    $app->add(new APIKey($valid_keys));
    
Your client now has to provide an api_key, or they will get a 403 Forbidden response.  For example, if your app is located at https://example.com/app, your client's request should include the api_key:

    https://example.com/app?api_key=validkey001
    
## Advanced Usage

You can control a couple of things:

    $app->add(new APIKey(valid_keys, parameter_name, content_type));

* valid_keys - An array() of valid API keys.  The values are used, the keys are ignored.  The type matters.
* parameter_name - The name of the parameter (the default is "api_key").  If this value is empty, users will get 500 Internal Server an error.
* content_type - The Content-Type of the response.  Valid options are:  application/json, application/problem+json, text/plain.  (the default is "text/plain")

In the example below, the api_key is called APPKEY, and the content type is application/json.

    $app = new Slim();
    $app->add(new APIKey(
		$valid_keys,
		"APPKEY",
		"application/json"
	));
