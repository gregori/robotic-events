<?php
/**
 * @package    RoboticEvent
 * @author     davisonpro.coder@gmail.com
 * @copyright  2018 RoboticEvent
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */

namespace RoboticEvent\Database;

/**
 * SQL query builder
 *
 * @since 1.0.0
 */
class DbQuery
{
    /**
     * List of data to build the query
     *
     * @var array
     */
    protected $query = array(
        'type'   => 'SELECT',
        'select' => array(),
        'from'   => array(),
        'join'   => array(),
        'where'  => array(),
        'group'  => array(),
        'having' => array(),
        'order'  => array(),
        'limit'  => array('offset' => 0, 'limit' => 0),
    );

    /**
     * Sets type of the query
     *
     * @param string $type SELECT|DELETE
     *
     * @return DbQuery
     */
    public function type($type)
    {
        $types = array('SELECT', 'DELETE');

        if (!empty($type) && in_array($type, $types)) {
            $this->query['type'] = $type;
        }

        return $this;
    }

    /**
     * Adds fields to SELECT clause
     *
     * @param string $fields List of fields to concat to other fields
     *
     * @return DbQuery
     */
    public function select($fields)
    {
        if (!empty($fields)) {
            $this->query['select'][] = $fields;
        }

        return $this;
    }

    /**
     * Sets table for FROM clause
     *
     * @param string      $table Table name
     * @param string|null $alias Table alias
     *
     * @return DbQuery
     */
    public function from($table, $alias = null)
    {
        if (!empty($table)) {
            $this->query['from'][] = '`'._DB_PREFIX_.$table.'`'.($alias ? ' '.$alias : '');
        }

        return $this;
    }

    /**
     * Adds JOIN clause
     * E.g. $this->join('RIGHT JOIN '._DB_PREFIX_.'product p ON ...');
     *
     * @param string $join Complete string
     *
     * @return DbQuery
     */
    public function join($join)
    {
        if (!empty($join)) {
            $this->query['join'][] = $join;
        }

        return $this;
    }

    /**
     * Adds a LEFT JOIN clause
     *
     * @param string      $table Table name (without prefix)
     * @param string|null $alias Table alias
     * @param string|null $on    ON clause
     *
     * @return DbQuery
     */
    public function leftJoin($table, $alias = null, $on = null)
    {
        return $this->join('LEFT JOIN `'._DB_PREFIX_.bqSQL($table).'`'.($alias ? ' `'.pSQL($alias).'`' : '').($on ? ' ON '.$on : ''));
    }

    /**
     * Adds an INNER JOIN clause
     * E.g. $this->innerJoin('product p ON ...')
     *
     * @param string      $table Table name (without prefix)
     * @param string|null $alias Table alias
     * @param string|null $on    ON clause
     *
     * @return DbQuery
     */
    public function innerJoin($table, $alias = null, $on = null)
    {
        return $this->join('INNER JOIN `'._DB_PREFIX_.bqSQL($table).'`'.($alias ? ' `'.pSQL($alias).'`' : '').($on ? ' ON '.$on : ''));
    }

    /**
     * Adds a LEFT OUTER JOIN clause
     *
     * @param string      $table Table name (without prefix)
     * @param string|null $alias Table alias
     * @param string|null $on    ON clause
     *
     * @return DbQuery
     */
    public function leftOuterJoin($table, $alias = null, $on = null)
    {
        return $this->join('LEFT OUTER JOIN `'._DB_PREFIX_.bqSQL($table).'`'.($alias ? ' `'.pSQL($alias).'`' : '').($on ? ' ON '.$on : ''));
    }

    /**
     * Adds a NATURAL JOIN clause
     *
     * @param string      $table Table name (without prefix)
     * @param string|null $alias Table alias
     *
     * @return DbQuery
     */
    public function naturalJoin($table, $alias = null)
    {
        return $this->join('NATURAL JOIN `'._DB_PREFIX_.bqSQL($table).'`'.($alias ? ' `'.pSQL($alias).'`' : ''));
    }

    /**
     * Adds a RIGHT JOIN clause
     *
     * @param string      $table Table name (without prefix)
     * @param string|null $alias Table alias
     * @param string|null $on    ON clause
     *
     * @return DbQuery
     */
    public function rightJoin($table, $alias = null, $on = null)
    {
        return $this->join('RIGHT JOIN `'._DB_PREFIX_.bqSQL($table).'`'.($alias ? ' `'.pSQL($alias).'`' : '').($on ? ' ON '.$on : ''));
    }

    /**
     * Adds a restriction in WHERE clause (each restriction will be separated by AND statement)
     *
     * @param string $restriction
     *
     * @return DbQuery
     */
    public function where($restriction)
    {
        if (!empty($restriction)) {
            $this->query['where'][] = $restriction;
        }

        return $this;
    }

    /**
     * Adds a restriction in HAVING clause (each restriction will be separated by AND statement)
     *
     * @param string $restriction
     *
     * @return DbQuery
     */
    public function having($restriction)
    {
        if (!empty($restriction)) {
            $this->query['having'][] = $restriction;
        }

        return $this;
    }

    /**
     * Adds an ORDER BY restriction
     *
     * @param string $fields List of fields to sort. E.g. $this->order('myField, b.mySecondField DESC')
     *
     * @return DbQuery
     */
    public function orderBy($fields)
    {
        if (!empty($fields)) {
            $this->query['order'][] = $fields;
        }

        return $this;
    }

    /**
     * Adds a GROUP BY restriction
     *
     * @param string $fields List of fields to group. E.g. $this->group('myField1, myField2')
     *
     * @return DbQuery
     */
    public function groupBy($fields)
    {
        if (!empty($fields)) {
            $this->query['group'][] = $fields;
        }

        return $this;
    }

    /**
     * Sets query offset and limit
     *
     * @param int $limit
     * @param int $offset
     *
     * @return DbQuery
     */
    public function limit($limit, $offset = 0)
    {
        $offset = (int)$offset;
        if ($offset < 0) {
            $offset = 0;
        }

        $this->query['limit'] = array(
            'offset' => $offset,
            'limit'  => (int)$limit,
        );

        return $this;
    }

    /**
     * Generates query and return SQL string
     *
     * @return string
     * @throws \Exception
     */
    public function build()
    {
        if ($this->query['type'] == 'SELECT') {
            $sql = 'SELECT '.((($this->query['select'])) ? implode(",\n", $this->query['select']) : '*')."\n";
        } else {
            $sql = $this->query['type'].' ';
        }

        if (!$this->query['from']) {
            throw new \Exception('Table name not set in DbQuery object. Cannot build a valid SQL query.');
        }

        $sql .= 'FROM '.implode(', ', $this->query['from'])."\n";

        if ($this->query['join']) {
            $sql .= implode("\n", $this->query['join'])."\n";
        }

        if ($this->query['where']) {
            $sql .= 'WHERE ('.implode(') AND (', $this->query['where']).")\n";
        }

        if ($this->query['group']) {
            $sql .= 'GROUP BY '.implode(', ', $this->query['group'])."\n";
        }

        if ($this->query['having']) {
            $sql .= 'HAVING ('.implode(') AND (', $this->query['having']).")\n";
        }

        if ($this->query['order']) {
            $sql .= 'ORDER BY '.implode(', ', $this->query['order'])."\n";
        }

        if ($this->query['limit']['limit']) {
            $limit = $this->query['limit'];
            $sql .= 'LIMIT '.($limit['offset'] ? $limit['offset'].', ' : '').$limit['limit'];
        }

        return $sql;
    }

    /**
     * Converts object to string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->build();
    }
}
