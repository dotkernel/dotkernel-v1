<?php
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @package    DotLibrary
 * @copyright  Copyright (c) 2009-2015 DotBoost Technologies Inc. (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version    $Id$
 */

/**
 * DotKernel API Header
 *
 * This class contains all the header messages and a useful method 
 *
 * @category   DotKernel
 * @package    DotApi
 * @author     DotKernel Team <team@dotkernel.com>
 */
class Api_Model_Header
{
	
	
	
	// HTTP STATUS CODES
	
	// 1xx Informational
	const HTTP_100 = 'HTTP/1.0 100 Continue';
	const HTTP_101 = 'HTTP/1.0 101 Switching Protocols';
	const HTTP_102 = 'HTTP/1.0 102 Processing (WebDAV; RFC 2518)';

	// 2xx Success
	const HTTP_200 = 'HTTP/1.0 200 OK';
	const HTTP_201 = 'HTTP/1.0 201 Created';
	const HTTP_202 = 'HTTP/1.0 202 Accepted';
	const HTTP_203 = 'HTTP/1.1 203 Non-Authoritative Information';
	const HTTP_204 = 'HTTP/1.0 204 No Content';
	const HTTP_205 = 'HTTP/1.0 205 Reset Content';
	const HTTP_206 = 'HTTP/1.0 206 Partial Content';
	const HTTP_207 = 'HTTP/1.0 207 Multi-Status (WebDAV; RFC 4918)';
	const HTTP_208 = 'HTTP/1.0 208 Already Reported (WebDAV; RFC 5842)';
	const HTTP_226 = 'HTTP/1.0 226 IM Used (RFC 3229)';

	// 3xx Redirection
	const HTTP_300 = 'HTTP/1.0 300 Multiple Choices';
	const HTTP_301 = 'HTTP/1.0 301 Moved Permanently';
	const HTTP_302 = 'HTTP/1.0 302 Found';
	const HTTP_303 = 'HTTP/1.1 303 See Other';
	const HTTP_304 = 'HTTP/1.0 304 Not Modified';
	const HTTP_305 = 'HTTP/1.1 305 Use Proxy';
	const HTTP_306 = 'HTTP/1.0 306 Switch Proxy';
	const HTTP_307 = 'HTTP/1.1 307 Temporary Redirect';
	const HTTP_308 = 'HTTP/1.0 308 Permanent Redirect';

	// 4xx Client Error
	const HTTP_400 = 'HTTP/1.0 400 Bad Request';
	const HTTP_401 = 'HTTP/1.0 401 Unauthorized';
	const HTTP_402 = 'HTTP/1.0 402 Payment Required';
	const HTTP_403 = 'HTTP/1.0 403 Forbidden';
	const HTTP_404 = 'HTTP/1.0 404 Not Found';
	const HTTP_405 = 'HTTP/1.0 405 Method Not Allowed';
	const HTTP_406 = 'HTTP/1.0 406 Not Acceptable';
	const HTTP_407 = 'HTTP/1.0 407 Proxy Authentication Required';
	const HTTP_408 = 'HTTP/1.0 408 Request Timeout';
	const HTTP_409 = 'HTTP/1.0 409 Conflict';
	const HTTP_410 = 'HTTP/1.0 410 Gone';
	const HTTP_411 = 'HTTP/1.0 411 Length Required';
	const HTTP_412 = 'HTTP/1.0 412 Precondition Failed';
	const HTTP_413 = 'HTTP/1.0 413 Request Entity Too Large';
	const HTTP_414 = 'HTTP/1.0 414 Request-URI Too Long';
	const HTTP_415 = 'HTTP/1.0 415 Unsupported Media Type';
	const HTTP_416 = 'HTTP/1.0 416 Requested Range Not Satisfiable';
	const HTTP_417 = 'HTTP/1.0 417 Expectation Failed';
	const HTTP_418 = 'HTTP/1.0 418 I\'m a teapot (RFC 2324)';
	const HTTP_422 = 'HTTP/1.0 422 Unprocessable Entity (WebDAV; RFC 4918)';
	const HTTP_423 = 'HTTP/1.0 423 Locked (WebDAV; RFC 4918)';
	const HTTP_424 = 'HTTP/1.0 424 Failed Dependency (WebDAV; RFC 4918)';
	const HTTP_426 = 'HTTP/1.0 426 Upgrade Required';
	const HTTP_428 = 'HTTP/1.0 428 Precondition Required (RFC 6585)';
	const HTTP_429 = 'HTTP/1.0 429 Too Many Requests (RFC 6585)';
	const HTTP_431 = 'HTTP/1.0 431 Request Header Fields Too Large (RFC 6585)';
	const HTTP_444 = 'HTTP/1.0 444 No Response (Nginx)';
	const HTTP_452 = 'HTTP/1.0 452 Conference Not Found';
	const HTTP_453 = 'HTTP/1.0 453 Not Enough Bandwidth';
	const HTTP_454 = 'HTTP/1.0 454 Session Not Found';
	const HTTP_455 = 'HTTP/1.0 455 Method Not Valid in This State';
	const HTTP_456 = 'HTTP/1.0 456 Header Field Not Valid for Resource';
	const HTTP_457 = 'HTTP/1.0 457 Invalid Range';
	const HTTP_458 = 'HTTP/1.0 458 Parameter Is Read-Only';
	const HTTP_459 = 'HTTP/1.0 459 Aggregate operation not allowed';
	const HTTP_460 = 'HTTP/1.0 460 Only aggregate operation allowed';
	const HTTP_461 = 'HTTP/1.0 461 Unsupported transport';
	const HTTP_462 = 'HTTP/1.0 462 Destination unreachable';
	const HTTP_463 = 'HTTP/1.0 463 Key management Failure';
	const HTTP_494 = 'HTTP/1.0 494 Request Header Too Large (Nginx)';
	const HTTP_495 = 'HTTP/1.0 495 Cert Error (Nginx)';
	const HTTP_496 = 'HTTP/1.0 496 No Cert (Nginx)';
	const HTTP_497 = 'HTTP/1.0 497 HTTP to HTTPS (Nginx)';
	const HTTP_498 = 'HTTP/1.0 498 Token expired/invalid (Esri)';
	const HTTP_499 = 'HTTP/1.0 499 Client Closed Request (Nginx)';

	// 5xx Server Error
	const HTTP_500 = 'HTTP/1.0 500 Internal Server Error';
	const HTTP_501 = 'HTTP/1.0 501 Not Implemented';
	const HTTP_502 = 'HTTP/1.0 502 Bad Gateway';
	const HTTP_503 = 'HTTP/1.0 503 Service Unavailable';
	const HTTP_504 = 'HTTP/1.0 504 Gateway Timeout';
	const HTTP_505 = 'HTTP/1.0 505 HTTP Version Not Supported';
	const HTTP_506 = 'HTTP/1.0 506 Variant Also Negotiates (RFC 2295)';
	const HTTP_507 = 'HTTP/1.0 507 Insufficient Storage (WebDAV; RFC 4918)';
	const HTTP_508 = 'HTTP/1.0 508 Loop Detected (WebDAV; RFC 5842)';
	const HTTP_509 = 'HTTP/1.0 509 Bandwidth Limit Exceeded (Apache bw/limited extension)';
	const HTTP_510 = 'HTTP/1.0 510 Not Extended (RFC 2774)';
	const HTTP_511 = 'HTTP/1.0 511 Network Authentication Required (RFC 6585)';
	const HTTP_551 = 'HTTP/1.0 551 Option not supported';
	const HTTP_598 = 'HTTP/1.0 598 Network read timeout error (Unknown)';
	const HTTP_599 = 'HTTP/1.0 599 Network connect timeout error (Unknown)';

	public static function setHeaderByCode($code, $replace = true)
	{
		if(defined('HTTP_'.$code))
		{
			header ( constant('HTTP_'.$code), $replace, $code);
			return true;
		}
		return false;
	}
	
}