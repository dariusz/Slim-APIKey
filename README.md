# Slim-APIKey

Slim-APIKey requires that clients provide an API key in order to make requests from your Slim PHP application.

## Basic Usage

To get going, you just need to provide an array of valid API keys.
  
    $valid_keys = array(
		"validkey001",
		"validkey002"
    );

    $app = new \Slim\Slim();
    $app->add(new \Slim\Middleware\APIKey($valid_keys));
    
Slim-APIKey will look for a parameter called `api_key` in your GET and POST parameters, for every client request. Your client now has to provide a key, or they will get a *403 Forbidden* response.  Ex. if your app is located at https://example.com/app, your client should request:

    https://example.com/app?api_key=validkey001
    
## Advanced Usage

You can control a couple of things:

    $app->add(new APIKey(valid_keys, parameter_name, content_type));

* `valid_keys` - The array() of valid API keys.  The array *values* are used, the array keys are ignored.  The type has to match.
* `parameter_name` - The name of the parameter (the default is "api_key").  If this value is empty, users will get a *500 Internal Server* error.
* `content_type` - The Content-Type of the response, which includes a humane error message.  Valid options are:  application/json, application/problem+json, text/plain, none  (the default is "text/plain")

Setting `content_type` to "none" prevents Slim-APIKey from responding with an error message.

In the example below, the api_key parameter is called APPKEY, and the Content-Type is application/json.

    $app = new \Slim\Slim();
    $app->add(new \Slim\Middleware\APIKey(
		$valid_keys,
		"APPKEY",
		"application/json"
	));

### application/problem+json

What is this `application/problem+json` magic?  It's a proposed IETF format that provides "machine-readable details of errors in a HTTP response, to avoid the need to invent new error response formats for HTTP APIs."  Sauce: https://tools.ietf.org/html/draft-nottingham-http-problem-06

Slim-APIKey returns the following JSON block when using this Content-Type:

    {
        "status": 403,
        "title": "Forbidden",
        "detail": "Helpful human readable error."
    }
