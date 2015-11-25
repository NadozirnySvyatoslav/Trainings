<?php
define ( DB_ERR, 'Database error: ' );

/**
 * Database class to connect Postgresql database
 */
class DB implements Iterator {
	private $conn;
	private $res;
	private $numrows;
	/**
	 * Connect to database with global constants
	 *
	 * \param[in] DB_HOST host
	 * \param[in] DB_NAME database name
	 * \param[in] DB_USER username
	 * \param[in] DB_PASSWORD password
	 */
	function __construct() {
		$this->conn = pg_connect ( 'host=' . DB_HOST . ' port=5432' . ' dbname=' . DB_NAME . ' user=' . DB_USER . ' password=' . DB_PASSWORD );
		if (! $this->conn)
			throw new Exception ( DB_ERR . 'Could not connect' );
	}
	
	/**
	 * Close connection
	 */
	function __destruct() {
		unset ( $this->res );
		@pg_close ( $this->conn );
	}
	/**
	 * Get table metadata (fields description)
	 * \return description
	 */
	function meta($table) {
		$res = pg_meta_data ( $this->conn, $table );
		if (! $res)
			throw new Exception ( DB_ERR . 'Meta: ' . pg_last_error ( $this->conn ) );
		return $res;
	}
	/**
	 * Run SQL-query
	 */
	function query($sql) {
		$res = pg_query ( $this->conn, $sql );
		if (! $res)
			throw new Exception ( DB_ERR . 'Query: ' . pg_last_error ( $this->conn ) );
	}
	/**
	 * Insert data with param
	 * \param[in] table name of table
	 * \param[in] data associative array of field=>value
	 * \param[in] options =PGSQL_DML_EXEC if not escaped strings present
	 */
	function insert($table, $data, $options = PGSQL_DML_EXEC) {
		if (! is_array ( $data ))
			throw new Exception ( DB_ERR . "Illegal data for insert" );
		$res = pg_insert ( $this->conn, $table, $data, $options );
		if (! $res)
			throw new Exception ( DB_ERR . 'Insert: ' . pg_last_error ( $this->conn ) );
	}
	/**
	 * Update data with param
	 * \param[in] table name of table
	 * \param[in] data associative - array of field=>value
	 * \param[in] cond conditions - array of field=>value
	 */
	function update($table, $data, $cond, $options = PGSQL_DML_EXEC) {
		$res = pg_update ( $this->conn, $table, $data, $cond, $options );
		
		if (! $res)
			throw new Exception ( DB_ERR . 'Update: ' . pg_last_error ( $this->conn ) );
		return $res;
	}
	/**
	 * Delete data with param
	 * \param[in] table name of table
	 * \param[in] condition associative array of field=>value
	 */
	function delete($table, $cond, $options = PGSQL_DML_EXEC) {
		$res = pg_delete ( $this->conn, $table, $cond, $options );
		if (! $res)
			throw new Exception ( DB_ERR . 'Delete: ' . pg_last_error ( $this->conn ) );
	}
	/**
	 * Select data with param
	 * \param[in] query SQL-query with keys.
	 * Example: "SELECT * FROM table WHERE id=$1 OR id=$2"
	 * \param[in] param array of params. Example: array('1','2')
	 */
	function select($query, $param = null) {
		$this->position = 0;
		$this->numrows = 0;
		if (isset ( $param )) {
			$this->res = pg_query_params ( $this->conn, $query, $param );
		} else {
			$this->res = pg_query ( $this->conn, $query );
		}
		if (! $this->res)
			throw new Exception ( DB_ERR . 'Select: ' . pg_last_error ( $this->conn ) );
		$this->numrows = pg_num_rows ( $this->res );
	}
	/**
	 * Iterator - get current item
	 * \return associative array of field=>value
	 */
	function current() {
		if ($this->position >= $this->numrows)
			throw new Exception ( DB_ERR . 'Fetch: no data' );
		$res = pg_fetch_assoc ( $this->res, $this->position );
		if (! $this->res)
			throw new Exception ( DB_ERR . 'Fetch: ' . pg_last_error ( $this->conn ) );
		return $res;
	}
	/**
	 * Iterator - get item and increase counter
	 */
	function next() {
		$res = $this->current ();
		++ $this->position;
		return $res;
	}
	/**
	 * Iterator - set begin position
	 */
	function rewind() {
		$this->position = 0;
	}
	/**
	 * Iterator - return current position
	 */
	function key() {
		return $this->position;
	}
	/**
	 * Iterator - check for next valid element
	 */
	function valid() {
		return ($this->position < $this->numrows);
	}
}