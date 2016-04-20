<?php
class Category_model extends MY_Model{
  
	public function category_list($id=0){
		if($id>0){
			$id=intval($id);
			$sql = "select cate_name,id from pro_category where is_delete=0 and id=$id order by id asc ";
			$res=$this->data_getRow($sql);
			return $res['cate_name'];
		}
		$res = $this->data_getAll("select cate_name,id from pro_category where is_delete=0 order by id asc ");
		$list[0] = '选择分类';
		foreach($res as $v){
			$list[$v['id']] = $v['cate_name'];
		}
		return $list;
	}

	//根据父级关联更新子级记录
	public function update_child($id=0,$is_wine=1){
		if($id>0){
			$sql = "UPDATE pro_category a,pro_category b SET a.is_wine=$is_wine,b.is_wine=$is_wine WHERE b.parent_id=a.id AND a.id=$id";
			return  data()->query($sql);
		}
	}
}