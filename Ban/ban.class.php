<?php
/**
 * 关键词过滤
 * @author toryzen(toryzen.com)
 * @create 2014-03-12
 */
 
class Ban{
    
    protected $key_num = 0;
    protected $ban_list = array();
    protected $replace_num = 0;
    protected $source;
    
    /**
     * 构造
     * @param string $source 源文件
     * @param string $dir    目录名
     */
    public function __construct($source,$dir="./list"){
        $this->source = $source;
        $contents = "";
        if(is_dir($dir)){
            //读取整个目录
            $fso = opendir($dir);
            while($fname = readdir($fso)){
                if(!in_array($fname,array('.','..'))){
                    //非目录文件
                    if(!is_dir($dir."/".$fname)){
                        $contents.= file_get_contents($dir."/".$fname);
                    }
                }
                $contents.= "\r\n";
            }
            if($contents){
                //切分、过滤、去重
                $ban_list = array_unique(array_filter(explode("\r\n",$contents)));
                //重新排列,按长度倒序
                usort($ban_list,array('self','sortByLen'));
                $key_num = count($ban_list);
                $this->ban_list = $ban_list;
                $this->key_num = $key_num;
            }
            closedir($fso);
            if($this->key_num==0){exit("Keyword list not found !");}
        }else{
            exit("Directory not exists !");
        }
    }
    
    /**
        自定义排序
    */
    private function sortByLen($a, $b){ 
        if (strlen($a) == strlen($b)){ 
            return 0; 
        }else{ 
        return (strlen($a) > strlen($b)) ? -1 : 1; 
        }
    }
    
    /**
        比较是否存在过滤关键词
    */
    public function has_ban(){
        
        foreach($this->ban_list as $key){
            if(strpos($this->source,$key)!==FALSE){
                return TRUE;
            }
        }
        return FALSE;
    }
    
    /**
     * 过滤
     * @param bool $strip 是否过滤html标签
     */
    public function filter($strip = FALSE){
        $old_num = substr_count($this->source,'*');
        //不区分大小写
        $ban_list = array_change_key_case(array_combine($this->ban_list,array_fill(0,$this->key_num,'*')),CASE_LOWER);
        $source = strtolower($strip?strip_tags($this->source):$this->source);
        $return = strtr($source,$ban_list);
        $new_num = substr_count($return,'*');
        $this->replace_num = $new_num-$old_num;
        return $return;
    }
    /**
        获取类信息
        @param string $field 数据名称
    */
    public function info($field=""){
        $return['key_num'] = $this->key_num;
        $return['replace_num'] = $this->replace_num;
        $return['ban_list'] = $this->ban_list;
        if($field!=""&&isset($return[$field])){
            return $return[$field];
        }
        return $return;
    }
    
}