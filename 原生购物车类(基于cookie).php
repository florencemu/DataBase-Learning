 <?php 
/** 
 * 购物车类 cookies 保存，保存周期为1天 注意：浏览器必须支持cookie才能够使用 
 */ 
class cartApi { 
 private $cartarray = array(); // 存放购物车的二维数组 
 private $cartcount; // 统计购物车数量 
 public $expires = 86400; // cookies过期时间，如果为0则不保存到本地 单位为秒 
 /** 
  * 构造函数 初始化操作 如果$id不为空，则直接添加到购物车 
  * 
  */ 
 public function __construct($id = "",$name = "",$price1 = "",$price2 = "",$price3 = "",$count = "",$image = "",$expires = 86400) { 
  if ($id != "" && is_numeric($id)) { 
   $this->expires = $expires; 
   $this->addcart($id,$name,$price1,$price2,$price3,$count,$image); 
  } 
 } 
 /** 
  * 添加商品到购物车 
  * 
  * @param int $id 商品的编号 
  * @param string $name 商品名称 
  * @param decimal $price1 商品价格 
  * @param decimal $price2 商品价格 
  * @param decimal $price3 商品价格 
  * @param int $count 商品数量 
  * @param string $image 商品图片 
  * @return 如果商品存在，则在原来的数量上加1，并返回false 
  */ 
 public function addcart($id,$name,$price1,$price2,$price3,$count,$image) { 
  $this->cartarray = $this->cartview(); // 把数据读取并写入数组 
  if ($this->checkitem($id)) { // 检测商品是否存在 
   $this->modifycart($id,$count,0); // 商品数量加$count 
   return false; 
  } 
  $this->cartarray[0][$id] = $id; 
  $this->cartarray[1][$id] = $name; 
  $this->cartarray[2][$id] = $price1; 
  $this->cartarray[3][$id] = $price2; 
  $this->cartarray[4][$id] = $price3; 
  $this->cartarray[5][$id] = $count; 
  $this->cartarray[6][$id] = $image; 
  $this->save(); 
 } 
 /** 
  * 修改购物车里的商品 
  * 
  * @param int $id 商品编号 
  * @param int $count 商品数量 
  * @param int $flag 修改类型 0：加 1:减 2:修改 3:清空 
  * @return 如果修改失败，则返回false 
  */ 
 public function modifycart($id, $count, $flag = "") { 
  $tmpid = $id; 
  $this->cartarray = $this->cartview(); // 把数据读取并写入数组 
  $tmparray = &$this->cartarray;  // 引用 
  if (!is_array($tmparray[0])) return false; 
  if ($id < 1) { 
   return false; 
  } 
  foreach ($tmparray[0] as $item) { 
   if ($item === $tmpid) { 
    switch ($flag) { 
     case 0: // 添加数量 一般$count为1 
      $tmparray[5][$id] += $count; 
      break; 
     case 1: // 减少数量 
      $tmparray[5][$id] -= $count; 
      break; 
     case 2: // 修改数量 
      if ($count == 0) { 
       unset($tmparray[0][$id]); 
       unset($tmparray[1][$id]); 
       unset($tmparray[2][$id]); 
       unset($tmparray[3][$id]); 
       unset($tmparray[4][$id]); 
       unset($tmparray[5][$id]); 
       unset($tmparray[6][$id]); 
       break; 
      } else { 
       $tmparray[5][$id] = $count; 
       break; 
      } 
     case 3: // 清空商品 
      unset($tmparray[0][$id]); 
      unset($tmparray[1][$id]); 
      unset($tmparray[2][$id]); 
      unset($tmparray[3][$id]); 
      unset($tmparray[4][$id]); 
      unset($tmparray[5][$id]); 
      unset($tmparray[6][$id]); 
      break; 
     default: 
      break; 
    } 
   } 
  } 
  $this->save(); 
 } 
 /** 
  * 清空购物车 
  * 
  */ 
 public function removeall() { 
  $this->cartarray = array(); 
  $this->save(); 
 } 
 /** 
  * 查看购物车信息 
  * 
  * @return array 返回一个二维数组 
  */ 
 public function cartview() { 
  $cookie = strips教程lashes($_cookie['cartapi']); 
  if (!$cookie) return false; 
  $tmpunserialize = unserialize($cookie); 
  return $tmpunserialize; 
 } 
 /** 
  * 检查购物车是否有商品 
  * 
  * @return bool 如果有商品，返回true，否则false 
  */ 
 public function checkcart() { 
  $tmparray = $this->cartview(); 
  if (count($tmparray[0]) < 1) {    
   return false; 
  } 
  return true; 
 } 
 /** 
  * 商品统计 
  * 
  * @return array 返回一个一维数组 $arr[0]:产品1的总价格 $arr[1:产品2得总价格 $arr[2]:产品3的总价格 $arr[3]:产品的总数量 
  */ 
 public function countprice() { 
  $tmparray = $this->cartarray = $this->cartview(); 
  $outarray = array(); //一维数组 
  // 0 是产品1的总价格 
  // 1 是产品2的总价格 
  // 2 是产品3的总价格 
  // 3 是产品的总数量 
  $i = 0; 
  if (is_array($tmparray[0])) { 
   foreach ($tmparray[0] as $key=>$val) { 
    $outarray[0] += $tmparray[2][$key] * $tmparray[5][$key]; 
    $outarray[1] += $tmparray[3][$key] * $tmparray[5][$key]; 
    $outarray[2] += $tmparray[4][$key] * $tmparray[5][$key]; 
    $outarray[3] += $tmparray[5][$key]; 
    $i++; 
   } 
  } 
  return $outarray; 
 } 
 /** 
  * 统计商品数量 
  * 
  * @return int 
  */ 
 public function cartcount() { 
  $tmparray = $this->cartview(); 
  $tmpcount = count($tmparray[0]); 
  $this->cartcount = $tmpcount; 
  return $tmpcount; 
 } 
 /** 
  * 保存商品 如果不使用构造方法，此方法必须使用 
  * 
  */ 
 public function save() { 
  $tmparray = $this->cartarray; 
  $tmpserialize = serialize($tmparray); 
  setcookie("cartapi",$tmpserialize,time()+$this->expires); 
 } 
 /** 
  * 检查购物车商品是否存在 
  * 
  * @param int $id 
  * @return bool 如果存在 true 否则false 
  */ 
 private function checkitem($id) { 
  $tmparray = $this->cartarray; 
  if (!is_array($tmparray[0])) return; 
  foreach ($tmparray[0] as $item) { 
   if ($item === $id) return true; 
  } 
  return false; 
 } 
} 
?> 
