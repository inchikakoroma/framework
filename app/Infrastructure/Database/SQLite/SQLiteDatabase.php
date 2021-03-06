<?php

namespace App\Infrastructure\Database\SQLite;

use App\Infrastructure\Database\DatabaseInterface;
use PicoDb\Database;
use PDO;

class SQLiteDatabase implements DatabaseInterface
{
    /**
    * @var SQLite
    */
    protected $db;

    /**
     * Constructor
     * @param Database $db
     */
    function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Return last executed query
     *
     * @return string
     */
    public function getLastQuery()
    {
        return $this->db->getStatementHandler()->withLogging();
    }

    /**
    * Insert a record into the specified table
    *
    * @param string $table Table name
    * @param array $record Record as an array mapping column to value
    * @return bool Whether or not the operation was successful
    */
    public function insert($table, $record)
    {
        return $this->db->table($table)->insert($record);
    }

    /**
    * Insert multiple records into the specified table
    *
    * @param string $table Table name
    * @param array $record Collection of records
    * @return array|bool Whether or not the operation was successful for each record
    */
    public function insertAll($table, $records)
    {
        $results = [];
        foreach ($records as $record) {
            $results[] = $this->db->table($table)->save($record);
        }

        return $results;
    }

    /**
    * Update a record in the specified table
    *
    * @param string $table Table name
    * @param array|string $conds Record as an array mapping column to value
    *                               or ID of record to be updated
    * @param array|string $record Array mapping updated columns to new values
    * @return bool Whether or not the operation was successful
    */
    public function update($table, $conds, $record)
    {
        $key = key($conds);
        return $this->db->table($table)->eq(key($conds), $conds[$key])->save($record);
    }

    /**
    * Execute a query and return the generated statement/result set
    *
    * @param string $query Query string
    * @return \PDOStatement
    */
    public function query($query)
    {
        $results = $this->db->execute($query)->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }

    /**
    * Execute a query and return the first result
    *
    * @param string $query Query string
    * @param array $conds Named query parameters
    * @return array()
    */
    public function queryFirst($query, $data = array())
    {
        $columns = array_keys($data);
        $query_string = str_replace($columns, '?', $query);

        $results = $this->db
                        ->execute($query_string, $data)
                        ->fetch(PDO::FETCH_ASSOC);

        return $results;
    }

    /**
    * Execute a query and return all results
    *
    * @param string $query Query string
    * @param array $conds Named query parameters
    * @return array()
    */
    public function queryAll($query, $data = array())
    {

        $columns = array_keys($data);
        $query_string = str_replace($columns, '?', $query);

        $results = $this->db
                        ->execute($query_string, $data)
                        ->fetchAll(PDO::FETCH_ASSOC);

        return $results;
    }

    /**
    * Delete a record from the specified table
    *
    * @param string $table Table name
    * @param array|string $conds array mapping column to value
    *                             or ID of record to be deleted
    * @return bool Whether or not the operation was successful
    */
    public function delete($table, $conds)
    {
        $key = key($conds);
        return $this->db->table($table)->eq(key($conds), $conds[$key])->remove();
    }
}
