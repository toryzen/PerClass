<?php
/**
 * 源码抓取
 * @author toryzen(toryzen.com)
 * @create 2014-03-12
 */
class tCatch{
	public $url;
	public $results;
	public $headers;
    public $msg;
	public $read_timeout = 5;
    public $Charset = 'utf-8';
    
	/**
     * 构造
     * @param string $url URL
     */
    public function __construct($url=NULL){
        $this->url = $url==NULL?"":$url;
    }
    
    /**
     * 抓取
     * @param string $url URL
     */
	function fetch($url=NULL){
        if($url!=NULL){
            $this->url = $url;
        }
        if(!$this->url){exit("Url muct set !");}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, TRUE);    //表示需要response header
		curl_setopt($ch, CURLOPT_NOBODY, FALSE);   //表示需要response body
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->read_timeout);
		$result = curl_exec($ch);
		$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($result, 0, $headerSize);
		$header = explode("\n",$header);
        $this->msg = curl_getinfo($ch);
		$this->headers = array_filter($header);
		$this->results = substr($result, $headerSize);
        $pageChar  = $this->getCharset($this->results,$this->msg);
        if($pageChar&&$pageChar!=$this->Charset){
            $this->results = $this->uni_decode($this->results,$pageChar);
        }
		curl_close($ch);
		return TRUE;
	}
    
    /**
     * 获取编码 
     * @param string $html 源码
     * @param string $msg msg
     */
    private function getCharset($html,$msg) 
    {
        preg_match("/<meta[^>]*charset=['\"]?([-\w]+)/i",$html,$temp);
        if(isset($temp[1]))
        {
            return strtolower($temp[1]);
        }
        if(isset($msg['content_type']))
        {
            preg_match("/charset=(.*)/i",$msg['content_type'],$match);
            if(isset($match[1]))
            {
                return strtolower($match[1]);
            }
        }			
        return false;			
    }
    
    /**
     * 编码转换
     * @param string $str 
     * @param string $char
     */
    private function uni_decode($str,$char)
    {
        if(strtolower($this->Charset) != strtolower($char))
        {
            if(function_exists("iconv"))
            {
                $str = @iconv($char,$this->Charset,$str);
                return $str;
            }
            elseif(function_exists("mb_convert_encoding"))
            {
                $str = mb_convert_encoding($str,$this->Charset,$char);
                return $str;
            }
        }
        return $str;
    }      
}