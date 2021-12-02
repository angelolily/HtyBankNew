<?php


class Jko_Model extends CI_Model
{

	function __construct()
	{
		parent::__construct();
		$this->db =$this->load->database('jkos',true);
	}

	//插入记录
	public function table_addRow($taname,$values,$type=1){

		if($type==1)
		{
			$this->db->insert($taname,$values);
		}
		else
		{
			$this->db->insert_batch($taname,$values);
		}



		$result = $this->db->affected_rows();
		$this->db->cache_delete_all();
		return $result;

	}


	public function table_seleRow_limit($field,$taname,$wheredata=array(),$likedata=array(),$limit=1,$offset=1,$order=null,$order_type=null){


		$this->db->select($field);
		if(count($wheredata)>0){
			$this->db->where($wheredata);//判断需不需where要查询
		}
		if(count($likedata)>0){
			$this->db->like($likedata);//判断需不需要like查询
		}
		$this->db->limit($offset,$limit);
		if(!(is_null($order))){

			$this->db->order_by($order,$order_type);

		}

		$query = $this->db->get($taname);
		$ss=$this->db->last_query();

		$rows_arr=$query->result_array();

		return $rows_arr;


	}


	//查询记录
	public function table_seleRow($field,$taname,$wheredata=array(),$likedata=array(),$wherein=array(),$whereinfield="",$orderby="",$order_type=""){

		$this->db->select($field);
		if(count($wheredata)>0){
			$this->db->where($wheredata);//判断需不需where要查询
		}
		if(count($likedata)>0){
			$this->db->like($likedata);//判断需不需要like查询
		}

		if(count($wherein)>0){
			$this->db->where_in($whereinfield,$wherein);//判断需不需要ow where in
		}
		if($orderby!="")
		{
			$this->db->order_by($orderby,$order_type);
		}
		$query = $this->db->get($taname);

		$ss=$this->db->last_query();

		$rows_arr=$query->result_array();

		return $rows_arr;

	}

	//修改记录
	public function table_updateRow($taname,$values,$wheredata){

		$this->db->where($wheredata);
		$this->db->update($taname,$values);
		$ss = $this->db->last_query();
		$result = $this->db->affected_rows();
		$this->db->cache_delete_all();
		return $result;

	}

	//删除记录
	public function table_del($taname,$wheredata){

		$this->db->where($wheredata);
		$this->db->delete($taname);
		$result = $this->db->affected_rows();
		$this->db->cache_delete_all();

		return $result;
	}

	//事物处理
	public function table_trans($sql_array)
	{

		if(count($sql_array)>0)
		{

			try {
				$this->db->trans_begin();
				foreach ($sql_array as $sql)
				{
					$this->db->query($sql);
				}
				if (($this->db->trans_status() === FALSE))
				{
					$this->db->trans_rollback();
					return false;
				}
				else {
					$this->db->trans_commit();
					$this->db->cache_delete_all();
					return true;
				}
			}
			catch (Exception $ex)
			{
				$this->db->trans_rollback();
				return false;
			}

		}





	}

	//查询银行信息
	public function searcheBank($where)
	{
		$data = array();
		$this->db->select('c_bankid,c_bankname');
		$this->db->where($where);
		$query = $this->db->get('jko_bank');
		$ss = $this->db->last_query();
		foreach ($query->result_array() as $row) {
			array_push($data, $row);
		}

		return $data;
	}


	//执行纯SQL语句，返回数组
	public function execute_sql($sql)
	{

		$query = $this->db->query($sql);
		if($query){
			return $query->result_array();
		}
		$ss=$this->db->last_query();
		return array();

	}

	//批量修改记录
	public function table_updateBatchRow($taname, $values, $wherekey)
	{

		$result = $this->db->update_batch($taname, $values, $wherekey);
		$ss = $this->db->last_query();
		$this->db->cache_delete_all();
		return $result;

	}

}
