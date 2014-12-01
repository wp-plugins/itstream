<?php
class Itstream_Ws_Params {
    /**
      * 
     * @var integer
     */
    public $limit = "10";
    
    /**
      * 
     * @var integer
     */
    public $page = "1";
    
    /**
      * 
     * @var integer
     */
    public $category = null;
    
    /**
      * 
     * @var string
     */
    public $order = 'video_id';
    
    /**
      * 
     * @var string
     */
    public $order_type = 'desc';
    
    /**
      * 
     * @var string
     */
    public $search = "";
    
    
    /**
      * 
     * @var integer
     */
    public $id = null;

    public function __construct($params = array()) {
        if(!empty($params['category'])) {
            $this->category = (int)$params['category'];
        }

        if(!empty($params['search'])) {
            $this->search = $params['search'];
        }

        if(!empty($params['page'])) {
            $this->page = (int)$params['page'];
        }
    }
}
?>
