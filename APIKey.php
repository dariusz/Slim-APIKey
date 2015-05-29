<?php
/**
* API Key Authorization
*
* Use this middleware with your Slim application to require an API key for all routes.
*
* @author Dariusz Grabka <dariusz@grabka.org>
* @version 0.1
*
* BASIC USAGE
*
* $app = new \Slim\Slim();
* $app->add(new \Slim\Middleware\APIKey(array_of_valid_keys));
* 
* USAGE
* 
* $app = new \Slim\Slim();
* $app->add(new \Slim\Middleware\APIKey(
*     array_of_valid_keys, parameter_name, response_type
* ));
* 
* array_of_valid_keys:
*     An array() of api_keys considered valid.  The values (not the
*     array keys) are compared and type-matched.
* 
* parameter_name:
*     name of the GET/POST parameter to use as the key.
*     Default is 'api_key'.
* 
* content_type:
*     Content-Type of the response. Can be one of ...
* 
*         application/json
*         application/problem+json
*         none
*         text/plain 
* 
*     ... with text/plain being the default.  Suppress any content from
*     being returned by using 'none' as the Content-Type.
*
* MIT LICENSE
*
* Permission is hereby granted, free of charge, to any person obtaining
* a copy of this software and associated documentation files (the
* "Software"), to deal in the Software without restriction, including
* without limitation the rights to use, copy, modify, merge, publish,
* distribute, sublicense, and/or sell copies of the Software, and to
* permit persons to whom the Software is furnished to do so, subject to
* the following conditions:
*
* The above copyright notice and this permission notice shall be
* included in all copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
* EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
* MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
* NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
* LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
* OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
* WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

namespace Slim\Middleware;

class APIKey extends \Slim\Middleware
{
	/**
	* Constructor
	*
	* @param string $valid_keys An array of value API key values.
	* @param string $parameter_name Name of the API key parameter
	* @param string $content_type Content-Type of the response. One of:
	*     text/plain
	*     application/json
	*     application/problem+json
	*     none
	*/

	public function __construct($valid_keys = array(), $parameter_name = 'api_key', $content_type = 'text/plain')
	{
		$this->parameter_name = $parameter_name;
		$this->valid_keys = $valid_keys;
		$this->content_type = $content_type;
	}
	
	/*
	 * Call
	 * 
	 * This method will check the API key parameter against the array
	 * of valid values. If there's a match, the next middleware is
	 * called. Otherwise a 403 Forbidden response and a brief
	 * description is returned to the client.
	 */
	public function call() {
		
		$incoming_key = $this->app->request->params($this->parameter_name);
		
		if (strlen($this->parameter_name) < 1) {
			$this->set_response(array(
				'status' => 500,
				'title' => "Internal Server Error",
				'detail' => "No valid name provided for API key parameter."
			));
		} else if ($incoming_key == null) {
			$this->set_response(array(
				'status' => 403,
				'title' => "Forbidden",
				'detail' => "API key is not provided."
			));
		} else if (!in_array($incoming_key, $this->valid_keys, true)) {
			$this->set_response(array(
				'status' => 403,
				'title' => "Forbidden",
				'detail' => "API key is not authorized."
			));
		} else {
			$this->next->call();
		}
	}	
	
	/*
	 * Set_Response
	 * 
	 * Sets the response based on the preferred Content-Type.
	 * If the preferred Content-Type is 'none', the default Content-Type
	 * is used and no message is returned.
	 */
	private function set_response($response) {

		$this->app->response->status($response['status']);

		switch ($this->content_type) {
			case "application/problem+json":
				$this->app->response->header("Content-Type", "application/problem+json");
				$this->app->response->body(json_encode($response));
				break;
			case "application/json":
				$this->app->response->header("Content-Type", "application/json");
				$this->app->response->body(json_encode($response['detail']));
				break;
			case "none":
				break;
			case "text/plain":
			default:
				$this->app->response->header("Content-Type", "text/plain");
				$this->app->response->body($response['detail']);
				break;
		}
	} 
}
