<?php

/**
 * $Date$
 * $Revision$
 * $HeadURL$
 * @package EDK
 */

/**
 * simple class for getting CREST references via URL
 */
class SimpleCrest
{
    
    /** timeout for fetching data from CREST
     * kills with many involved parties might take very long to fetch
     */
    public static $TIMEOUT = 45;
    
    /**
     * preferred method of getting data from CREST;
     * will fall back to file_get_contents if curl isn't available 
	 * accepted values: curl, file
     */
    public static $HTTP_METHOD = "curl";
    
    
    public static $USER_AGENT = "EDK HTTP Requester, http://www.evekb.org";
    
    
    protected static $curl;
    
    
    /**
     * takes a CREST URL and gets the referenced object
     * @param string $url
     * @return Object a decoded json object
     */
    public static function getReferenceByUrl($url)
    {
        if($url == null)
        {
            return null;
        }

        // determine whether cURL is available
        if(in_array  ('curl', get_loaded_extensions()) && self::$HTTP_METHOD == 'curl')
        {
            $data = self::getCrestByCurl($url);
        }
        
        else
        {
            $data = self::getCrestByFileGetContents($url);
        }
        
        if($data != NULL)
        {
            return json_decode($data);
        }
        
        return NULL;

    }
    
    /**
     * gets data from CREST using the given url and
     * cURL
     * @throws Exception
     */
    protected static function getCrestByCurl($url)
    {
        if(!self::$curl)
        {
            self::$curl = curl_init();
        }
        
        $headers = array(
			'Accept-language: en\r\n'
		);
        
        // ignore ssl peer verification
        if(substr($url,0,5) == "https")
        {
            curl_setopt(self::$curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        }
        
        // set timeout
        curl_setopt(self::$curl, CURLOPT_TIMEOUT, self::$TIMEOUT);
        
        // allow all encodings
        curl_setopt(self::$curl, CURLOPT_ENCODING, "");

        // curl defaults
        curl_setopt(self::$curl, CURLOPT_URL, $url);
        curl_setopt(self::$curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt(self::$curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(self::$curl, CURLOPT_USERAGENT, self::$USER_AGENT);
        
        // call
        $result	= curl_exec(self::$curl);
        $errorNumber = curl_errno(self::$curl);
        $error = curl_error(self::$curl);
        
        // response http headers
        $httpCode = curl_getinfo(self::$curl, CURLINFO_HTTP_CODE);

        // http errors
        if($httpCode >= 400) {
            switch($httpCode) {
                case 400:
                case 403:
                case 500:
                case 503: 
                    return $result;
                    break;
                default:
            }            
            throw new Exception('Error getting data: HTTP '.$httpCode.', URL: '.$url);
        }
        
        // curl errors
        if($errorNumber)
        {
            throw new Exception('Error getting data: '.$error.'('.$errorNumber.')');
        }
        
        return $result;
    }
    
    
     /**
     * gets data from CREST using the given url and
     * file_get_contents
     * @param String $url the CREST url to fetch data from
     */
    protected static function getCrestByFileGetContents($url)
    {
        // build header
        $header = 'Accept-language: en\r\n';
        $header .= "User-Agent: ".self::$USER_AGENT."\r\n";
        
        $opts = array(
            'http' => array(
                'method' => "GET",
                'timeout' => self::$TIMEOUT
            ),
            'socket' => array(
                'bindto' => "0.0.0.0:0"
            )
            
        );
        
        $opts['http']['header'] = $header;
        
        $context = stream_context_create($opts);
        $headers = @get_headers($url, 1);
        if (false === ($data = @file_get_contents($url, false, $context))) {

            if (false === $headers) {
                throw new \Exception("could not connect to api");
            }

            throw new \Exception("an error occurred with the http request: ".$headers[0]);
        } 
		
        else 
        {
            if(isset($headers['Transfer-Encoding']) && $headers['Transfer-Encoding'] == "chunked")
            {
                // fix: some PHP versions don't automatically decode chunked http streams
                $decoded = self::decode_chunked($data);
                if(trim($decoded) == "")
                {
                    return $data;
                }

                else
                {
                    return $decoded;
                }
            }

            else
            {
                    return $data;
            }
				
        }
        
        return null;
    }
	
    public static function decode_chunked($str) 
    {
      for ($res = ''; !empty($str); $str = trim($str)) {
            $pos = strpos($str, "\r\n");
            $len = hexdec(substr($str, 0, $pos));
            $res.= substr($str, $pos + 2, $len);
            $str = substr($str, $pos + 2 + $len);
      }
      return $res;
    }
}
