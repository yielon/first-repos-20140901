<?php 
/*
 * 构造无限级树
 * 毗邻目录模式
* @author along 
* @since  2014-06-04
*/
class Tree {

    /**
     * 主键ID键名
     */
    private $id_key;
    
    /**
     * 父ID键名
     */
    private $pid_key;
    
    /**
     * 源数据组
     *     $source_data=array(
     *         //id=>data
     *         1=>array('id'=>'1', 'pid'=>'0', 'title'=>'标题1'),
     *         2=>array('id'=>'2', 'pid'=>'0', 'title'=>'标题2'),
     *     )
     */
    static private $source_data;
    
    /**
     * 源数据组
     *     $relation_data=array(
     *         //id=>pid
     *         1=>0,
     *         2=>0,
     *         11=>1,
     *     )
     */
    static private $relation_data;
    
    /**
     * 构造函数，初始化静态数据
     */
    public function __construct($data, $id_key, $pid_key){
        $this->id_key     = $id_key;
        $this->pid_key   = $pid_key;
        $this->__makeData($data);
    }
    
    
    /**
     * 构造关系数组和源数据数组
     */
    private function __makeData($data){
        $relation_data = array();
        $source_data  = array();
        foreach ($data as $v) {
            $relation_data[ $v[$this->id_key] ] = $v[ $this->pid_key ];
            $source_data[ $v[$this->id_key] ] = $v;
        }
        self::$source_data = $source_data;
        self::$relation_data = $relation_data;
    }
    
    
    /**
     * 递归树
     */
    static private  function __recursionTree($pid=0) {
        $not_root = false;
        if(array_key_exists($pid,self::$source_data )){
            $tree = self::$source_data[$pid];
            $not_root = true;
        }
        $children_keys = array_keys( self::$relation_data , $pid);
        if(!empty($children_keys)){
            foreach ($children_keys as $id) {
                if($not_root){
                    $tree['children'][$id] = self::__recursionTree($id);
                }else{
                    $tree[$id] = self::__recursionTree($id);
                }
            }
        }
        return $tree;
    }
    
    /**
     * 直接获取树形结构
     */
    public function getTree($pid=0){
        $layer = self::__recursionTree($pid);
        return $layer;
    }
    
    
    /**
     * 递归计算层级数和层级路径
     */
    static private  function __recursionLayer( $parent_id=0, $layer=1, $layer_data=array(), $path=array() ){
        foreach (self::$relation_data as $id => $pid) {
            if($pid == $parent_id){
                //原有数据
                $layer_data[$id] = self::$source_data[$id];
                //获取层级数
                $layer_data[$id]['layer'] = $layer;
                //获取层级路径
                $path[$id]['path'] = !empty($path[$pid]['path']) ? $path[$pid]['path'] : array();
                ////$path[$id]['path'][] = $pid;    //显示上层(包含节点0)
                $path[$id]['path'][] = $id;    //显示下层(包含自身)
                $layer_data[$id]['path'] =  $path[$id]['path'];
                //
                $layer_data = self::__recursionLayer( $id, $layer+1, $layer_data, $path);
            }
        }
        return $layer_data;
    } 
    
    /**
     * 获取层级结构
     * @return 返回层级数和层级路径，层级顺序已经正确
     */
    public function getLayer(){
        $layer = array();
        $layer = self::__recursionLayer();
        return $layer;
    }
    
    

    /**
     * 递归获取子孙ID数组
     * error
     */
    static private  function __recursionLeaf( $pid, $leaf=array()){
        $limb_keys = array_keys( self::$relation_data , $pid);
        if(!empty($limb_keys)){
            foreach ($limb_keys as $id) {
                //原有数据
                $leaf[$id] =  self::$source_data[$id];
                $leaf = self::__recursionLeaf($id, $leaf);
            }
        }
        return $leaf;
    }
    
    
    /**
     * 获取所有子节点
     * @return 返回层级数和层级路径
     */
    public function getLeaf($pid=0){
        $leaf = self::__recursionLeaf($pid);
        return $leaf;
    }
    

}






/*
 * demo
*/

$data = array(
        array('id'=>'1', 'pid'=>'0', 'title'=>'标题1'),
        array('id'=>'2', 'pid'=>'0', 'title'=>'标题2'),
        array('id'=>'11', 'pid'=>'1', 'title'=>'标题11'),
        array('id'=>'12', 'pid'=>'1', 'title'=>'标题12'),
        array('id'=>'13', 'pid'=>'1', 'title'=>'标题13'),
        array('id'=>'121', 'pid'=>'12', 'title'=>'标题121'),
        array('id'=>'122', 'pid'=>'12', 'title'=>'标题122'),
        array('id'=>'123', 'pid'=>'12', 'title'=>'标题123'),   
        array('id'=>'1211', 'pid'=>'121', 'title'=>'标题1211'),   
        array('id'=>'1212', 'pid'=>'121', 'title'=>'标题1212'),   
        array('id'=>'21', 'pid'=>'2', 'title'=>'标题21'),   
        array('id'=>'22', 'pid'=>'2', 'title'=>'标题22'),   
);

//实例
$treeObj = new Tree($data, 'id', 'pid');
//获取树形结构
$tree = $treeObj->getTree(12);
var_dump($tree);
//获取层级结构
$layer = $treeObj->getLayer();
var_dump($layer);
//获取子孙数据
$leaf = $treeObj->getLeaf(0);
var_dump($leaf);


/*
 * 删除枝干
 *     保证其所有子节点都要删除
 */

/*
 * 移动枝干
*     原则：不可向其子孙节点移动
*/


/*
 * 复制枝干
*     重点：如何构造这个枝干
*/

















