<?php
	
	class UniteDBRev{
		
		private $wpdb;
		private $lastRowID;
		
		/**
		 * 
		 * constructor - set database object
		 */
		public function __construct(){
			global $wpdb;
			$this->wpdb = $wpdb;
		}
		
		/**
		 * 
		 * throw error
		 */
		private function throwError($message,$code=-1){
			UniteFunctionsRev::throwError($message,$code);
		}
		
		//------------------------------------------------------------
		// validate for errors
		private function checkForErrors($prefix = ""){
			
			if(mysql_error()){
				$query = $this->wpdb->last_query;
				$message = $this->wpdb->last_error;
				
				if($prefix) $message = $prefix.' - <b>'.$message.'</b>';
				if($query) $message .=  '<br>---<br> Query: ' . $query;
				
				$this->throwError($message);
			}
		}
		
		
		/**
		 * 
		 * insert variables to some table
		 */
		public function insert($table,$arrItems){
			
			$this->wpdb->insert($table, $arrItems);
			$this->checkForErrors("Insert query error");
			
			$this->lastRowID = mysql_insert_id();
						
			return($this->lastRowID);
		}
		
		/**
		 * 
		 * get last insert id
		 */
		public function getLastInsertID(){
			$this->lastRowID = mysql_insert_id();
			return($this->lastRowID);			
		}
		
		
		/**
		 * 
		 * delete rows
		 */
		public function delete($table,$where){
			
			UniteFunctionsRev::validateNotEmpty($table,"table name");
			UniteFunctionsRev::validateNotEmpty($where,"where");
			
			$query = "delete from $table where $where";
			
			$this->wpdb->query($query);
			
			$this->checkForErrors("Delete query error");
		}
		
		
		/**
		 * 
		 * run some sql query
		 */
		public function runSql($query){
			
			$this->wpdb->query($query);			
			$this->checkForErrors("Regular query error");
		}
		
		
		/**
		 * 
		 * insert variables to some table
		 */
		public function update($table,$arrItems,$where){
			
			$response = $this->wpdb->update($table, $arrItems, $where);
			if($response === false)
				UniteFunctionsRev::throwError("no update action taken!");
				
			$this->checkForErrors("Update query error");
			
			return($this->wpdb->num_rows);
		}
		
		
		/**
		 * 
		 * get data array from the database
		 * 
		 */
		public function fetch($tableName,$where="",$orderField="",$groupByField="",$sqlAddon=""){
		
			$query = "select * from $tableName";
			if($where) $query .= " where $where";
			if($orderField) $query .= " order by $orderField";
			if($groupByField) $query .= " group by $groupByField";
			if($sqlAddon) $query .= " ".$sqlAddon;
			
			$response = $this->wpdb->get_results($query,ARRAY_A);
			
			$this->checkForErrors("fetch");
			
			return($response);
		}
		
		/**
		 * 
		 * fetch only one item. if not found - throw error
		 */
		public function fetchSingle($tableName,$where="",$orderField="",$groupByField="",$sqlAddon=""){
			$response = $this->fetch($tableName, $where, $orderField, $groupByField, $sqlAddon);
			if(empty($response))
				$this->throwError("Record not found");
			$record = $response[0];
			return($record);
		}
		
		/**
		 * 
		 * escape data to avoid sql errors and injections.
		 */
		public function escape($string){
			$string = esc_sql($string);
			return($string);
		}
		
	}
	
?>