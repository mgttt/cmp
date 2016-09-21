<?php
//NOTES:
//201608
//Refer from the Redbean\Facade and cleanup for NonStatic need.
namespace RedBeanPHP {

	use RedBeanPHP\QueryWriter as QueryWriter;
	use RedBeanPHP\Adapter\DBAdapter as DBAdapter;
	use RedBeanPHP\RedException\SQL as SQLException;
	#use RedBeanPHP\Adapter as Adapter;
	use RedBeanPHP\QueryWriter\AQueryWriter as AQueryWriter;
	use RedBeanPHP\RedException as RedException;
	use RedBeanPHP\BeanHelper\SimpleFacadeBeanHelper as SimpleFacadeBeanHelper;//
	use RedBeanPHP\Driver\RPDO as RPDO;
	use RedBeanPHP\Util\DispenseHelper as DispenseHelper;

	class CmpRbFacadeNonStatic
	{
		/**
		 * @var ToolBox
		 */
		public $toolbox;

		/**
		 * @var OODB
		 */
		private $redbean;

		/**
		 * @var QueryWriter
		 */
		private $writer;

		/**
		 * @var DBAdapter
		 */
		private $adapter;

		/**
		 * @var AssociationManager
		 */
		private $associationManager;

		/**
		 * @var TagManager
		 */
		private $tagManager;

		/**
		 * @var DuplicationManager
		 */
		private $duplicationManager;

		/**
		 * @var LabelMaker
		 */
		private $labelMaker;

		/**
		 * @var Finder
		 */
		private $finder;

		/**
		 * @var array
		 */
		private $plugins = array();

		/**
		 * @var string
		 */
		private $exportCaseStyle = 'default';

		/**
		 * Not in use (backward compatibility SQLHelper)
		 */
		public $f;

		/**
		 * @var string
		 */
		public $currentDB = '';

		/**
		 * Internal Query function, executes the desired query. Used by
		 * all facade query functions. This keeps things DRY.
		 *
		 * @param string $method   desired query method (i.e. 'cell', 'col', 'exec' etc..)
		 * @param string $sql      the sql you want to execute
		 * @param array  $bindings array of values to be bound to query statement
		 *
		 * @return array
		 */
		//CMP
		private function query( $method, $sql, $bindings )
		{
			if ( !$this->redbean->isFrozen() ) {
				try {
					$rs = $this->adapter->$method( $sql, $bindings );
				} catch ( SQLException $exception ) {
					if ( $this->writer->sqlStateIn( $exception->getSQLState(),
						array(
							QueryWriter::C_SQLSTATE_NO_SUCH_COLUMN,
							QueryWriter::C_SQLSTATE_NO_SUCH_TABLE )
						)
					) {
						//cmp.hack:
						if ($method=='exec') return NULL;
						return ( $method === 'getCell' ) ? NULL : array();
					} else {
						throw $exception;
					}
				}

				return $rs;
			} else {
				return $this->adapter->$method( $sql, $bindings );
			}
		}

		/**
		 * Tests the connection.
		 * Returns TRUE if connection has been established and
		 * FALSE otherwise.
		 *
		 * @return boolean
		 */
		//CMP
		public function testConnection()
		{
			if ( !isset( $this->adapter ) ) return FALSE;

			$database = $this->adapter->getDatabase();
			try {
				@$database->connect();
			} catch ( \Exception $e ) {}
				return $database->isConnected();
		}

		/**
		 * Kickstarts redbean for you. This method should be called before you start using
		 * RedBean. The Setup() method can be called without any arguments, in this case it will
		 * try to create a SQLite database in /tmp called red.db (this only works on UNIX-like systems).
		 *
		 * @param string  $dsn      Database connection string
		 * @param string  $username Username for database
		 * @param string  $password Password for database
		 * @param boolean $frozen   TRUE if you want to setup in frozen mode
		 *
		 * @return ToolBox
		 */
		//CMP
		public function setup( $dsn = NULL, $username = NULL, $password = NULL, $frozen = FALSE )
		{
			if ( is_null( $dsn ) ) {
				$dsn = 'sqlite:/' . sys_get_temp_dir() . '/red.db';
			}

			$this->attachDatabase( 'default', $dsn, $username, $password, $frozen );

			$this->configureFacadeWithToolbox( $this->toolbox );

			return $this->toolbox;
		}

		public function attachDatabase( $key, $dsn, $user = NULL, $pass = NULL, $frozen = FALSE )
		{
			if ( is_object($dsn) ) {
				$db  = new RPDO( $dsn );
				$dbType = $db->getDatabaseType();
			} else {
				$db = new RPDO( $dsn, $user, $pass, TRUE );
				$dbType = substr( $dsn, 0, strpos( $dsn, ':' ) );
			}

			$adapter = new DBAdapter( $db );

			$writers = array(
				'pgsql'  => 'PostgreSQL',
				'sqlite' => 'SQLiteT',
				'cubrid' => 'CUBRID',
				'mysql'  => 'MySQL',
				'sqlsrv' => 'SQLServer',
			);

			$wkey = trim( strtolower( $dbType ) );
			if(!$wkey) throw new RedException( 'Database Not Config?' );//2016-2-16.wanjo
			if ( !isset( $writers[$wkey] ) ) {
				$wkey = preg_replace( '/\W/', '' , $wkey );
				throw new RedException( 'Unsupported database ('.$wkey.').' );
			}
			$writerClass = '\\RedBeanPHP\\QueryWriter\\'.$writers[$wkey];
			$writer      = new $writerClass( $adapter );
			$redbean     = new OODB( $writer, $frozen );

			$this->toolbox = new ToolBox( $redbean, $adapter, $writer );
		}

		/**
		 * Stores a bean in the database. This method takes a
		 * OODBBean Bean Object $bean and stores it
		 * in the database. If the database schema is not compatible
		 * with this bean and RedBean runs in fluid mode the schema
		 * will be altered to store the bean correctly.
		 * If the database schema is not compatible with this bean and
		 * RedBean runs in frozen mode it will throw an exception.
		 * This function returns the primary key ID of the inserted
		 * bean.
		 *
		 * The return value is an integer if possible. If it is not possible to
		 * represent the value as an integer a string will be returned.
		 *
		 * @param OODBBean|SimpleModel $bean bean to store
		 *
		 * @return integer|string
		 */
		//CMP
		public function store( $bean )
		{
			return $this->redbean->store( $bean );
		}

		/**
		 * Toggles fluid or frozen mode. In fluid mode the database
		 * structure is adjusted to accomodate your objects. In frozen mode
		 * this is not the case.
		 *
		 * You can also pass an array containing a selection of frozen types.
		 * Let's call this chilly mode, it's just like fluid mode except that
		 * certain types (i.e. tables) aren't touched.
		 *
		 * @param boolean|array $trueFalse
		 */
		//CMP
		public function freeze( $tf = TRUE )
		{
			$this->redbean->freeze( $tf );
		}

		/**
		 * Loads a bean from the object database.
		 * It searches for a OODBBean Bean Object in the
		 * database. It does not matter how this bean has been stored.
		 * RedBean uses the primary key ID $id and the string $type
		 * to find the bean. The $type specifies what kind of bean you
		 * are looking for; this is the same type as used with the
		 * dispense() function. If RedBean finds the bean it will return
		 * the OODB Bean object; if it cannot find the bean
		 * RedBean will return a new bean of type $type and with
		 * primary key ID 0. In the latter case it acts basically the
		 * same as dispense().
		 *
		 * Important note:
		 * If the bean cannot be found in the database a new bean of
		 * the specified type will be generated and returned.
		 *
		 * @param string  $type type of bean you want to load
		 * @param integer $id   ID of the bean you want to load
		 *
		 * @return OODBBean
		 */
		//CMP
		public function load( $type, $id )
		{
			return $this->redbean->load( $type, $id );
		}

		/**
		 * Removes a bean from the database.
		 * This function will remove the specified OODBBean
		 * Bean Object from the database.
		 *
		 * This facade method also accepts a type-id combination,
		 * in the latter case this method will attempt to load the specified bean
		 * and THEN trash it.
		 *
		 * @param string|OODBBean|SimpleModel $bean bean you want to remove from database
		 * @param integer                     $id   ID if the bean to trash (optional, type-id variant only)
		 *
		 * @return void
		 */
		//CMP
		public function trash( $beanOrType, $id = NULL )
		{
			if ( is_string( $beanOrType ) ) return $this->trash( $this->load( $beanOrType, $id ) );
			return $this->redbean->trash( $beanOrType );
		}

		/**
		 * Dispenses a new RedBean OODB Bean for use with
		 * the rest of the methods.
		 *
		 * @param string|array $typeOrBeanArray   type or bean array to import
		 * @param integer      $number            number of beans to dispense
		 * @param boolean	   $alwaysReturnArray if TRUE always returns the result as an array
		 *
		 * @return array|OODBBean
		 */
		//CMP
		public function dispense( $typeOrBeanArray, $num = 1, $alwaysReturnArray = FALSE )
		{
			return DispenseHelper::dispense( $this->redbean, $typeOrBeanArray, $num, $alwaysReturnArray );
		}

		/**
		 * Takes a comma separated list of bean types
		 * and dispenses these beans. For each type in the list
		 * you can specify the number of beans to be dispensed.
		 *
		 * Usage:
		 *
		 * <code>
		 * list( $book, $page, $text ) = $this->dispenseAll( 'book,page,text' );
		 * </code>
		 *
		 * This will dispense a book, a page and a text. This way you can
		 * quickly dispense beans of various types in just one line of code.
		 *
		 * Usage:
		 *
		 * <code>
		 * list($book, $pages) = $this->dispenseAll('book,page*100');
		 * </code>
		 *
		 * This returns an array with a book bean and then another array
		 * containing 100 page beans.
		 *
		 * @param string  $order      a description of the desired dispense order using the syntax above
		 * @param boolean $onlyArrays return only arrays even if amount < 2
		 *
		 * @return array
		 */
		public function dispenseAll( $order, $onlyArrays = FALSE )
		{
			return DispenseHelper::dispenseAll( $this->redbean, $order, $onlyArrays );
		}

		/**
		 * Convience method. Tries to find beans of a certain type,
		 * if no beans are found, it dispenses a bean of that type.
		 *
		 * @param  string $type     type of bean you are looking for
		 * @param  string $sql      SQL code for finding the bean
		 * @param  array  $bindings parameters to bind to SQL
		 *
		 * @return array
		 */
		//CMP
		public function findOrDispense( $type, $sql = NULL, $bindings = array() )
		{
			return $this->finder->findOrDispense( $type, $sql, $bindings );
		}

		/**
		 * Finds a bean using a type and a where clause (SQL).
		 * As with most Query tools in RedBean you can provide values to
		 * be inserted in the SQL statement by populating the value
		 * array parameter; you can either use the question mark notation
		 * or the slot-notation (:keyname).
		 *
		 * @param string $type     the type of bean you are looking for
		 * @param string $sql      SQL query to find the desired bean, starting right after WHERE clause
		 * @param array  $bindings array of values to be bound to parameters in query
		 *
		 * @return array
		 */
		//CMP
		public function find( $type, $sql = NULL, $bindings = array() )
		{
			return $this->finder->find( $type, $sql, $bindings );
		}

		/**
		 * @see $this->find
		 *      The findAll() method differs from the find() method in that it does
		 *      not assume a WHERE-clause, so this is valid:
		 *
		 * $this->findAll('person',' ORDER BY name DESC ');
		 *
		 * Your SQL does not have to start with a valid WHERE-clause condition.
		 *
		 * @param string $type     the type of bean you are looking for
		 * @param string $sql      SQL query to find the desired bean, starting right after WHERE clause
		 * @param array  $bindings array of values to be bound to parameters in query
		 *
		 * @return array
		 */
		//CMP
		public function findAll( $type, $sql = NULL, $bindings = array() )
		{
			return $this->finder->find( $type, $sql, $bindings );
		}

		/**
		 * @see $this->find
		 * The variation also exports the beans (i.e. it returns arrays).
		 *
		 * @param string $type     the type of bean you are looking for
		 * @param string $sql      SQL query to find the desired bean, starting right after WHERE clause
		 * @param array  $bindings array of values to be bound to parameters in query
		 *
		 * @return array
		 */
		//CMP
		public function findAndExport( $type, $sql = NULL, $bindings = array() )
		{
			return $this->finder->findAndExport( $type, $sql, $bindings );
		}

		/**
		 * @see $this->find
		 * This variation returns the first bean only.
		 *
		 * @param string $type     the type of bean you are looking for
		 * @param string $sql      SQL query to find the desired bean, starting right after WHERE clause
		 * @param array  $bindings array of values to be bound to parameters in query
		 *
		 * @return OODBBean
		 */
		//CMP
		public function findOne( $type, $sql = NULL, $bindings = array() )
		{
			return $this->finder->findOne( $type, $sql, $bindings );
		}

		/**
		 * @see $this->find
		 * This variation returns the last bean only.
		 *
		 * @param string $type     the type of bean you are looking for
		 * @param string $sql      SQL query to find the desired bean, starting right after WHERE clause
		 * @param array  $bindings array of values to be bound to parameters in query
		 *
		 * @return OODBBean
		 */
		public function findLast( $type, $sql = NULL, $bindings = array() )
		{
			return $this->finder->findLast( $type, $sql, $bindings );
		}

		/**
		 * Finds multiple types of beans at once and offers additional
		 * remapping functionality. This is a very powerful yet complex function.
		 * For details see Finder::findMulti().
		 *
		 * @see Finder::findMulti()
		 *
		 * @param array|string $types      a list of bean types to find
		 * @param string|array $sqlOrArr   SQL query string or result set array
		 * @param array        $bindings   SQL bindings
		 * @param array        $remappings an array of remapping arrays containing closures
		 *
		 * @return array
		 */
		public function findMulti( $types, $sql, $bindings = array(), $remappings = array() )
		{
			return $this->finder->findMulti( $types, $sql, $bindings, $remappings );
		}

		/**
		 * Returns an array of beans. Pass a type and a series of ids and
		 * this method will bring you the corresponding beans.
		 *
		 * important note: Because this method loads beans using the load()
		 * function (but faster) it will return empty beans with ID 0 for
		 * every bean that could not be located. The resulting beans will have the
		 * passed IDs as their keys.
		 *
		 * @param string $type type of beans
		 * @param array  $ids  ids to load
		 *
		 * @return array
		 */
		public function batch( $type, $ids )
		{
			return $this->redbean->batch( $type, $ids );
		}

		/**
		 * @see $this->batch
		 *
		 * Alias for batch(). Batch method is older but since we added so-called *All
		 * methods like storeAll, trashAll, dispenseAll and findAll it seemed logical to
		 * improve the consistency of the Facade API and also add an alias for batch() called
		 * loadAll.
		 *
		 * @param string $type type of beans
		 * @param array  $ids  ids to load
		 *
		 * @return array
		 */
		//CMP
		public function loadAll( $type, $ids )
		{
			return $this->redbean->batch( $type, $ids );
		}

		/**
		 * Convenience function to execute Queries directly.
		 * Executes SQL.
		 *
		 * @param string $sql       SQL query to execute
		 * @param array  $bindings  a list of values to be bound to query parameters
		 *
		 * @return integer
		 */
		//CMP
		public function exec( $sql, $bindings = array() )
		{
			return $this->query( 'exec', $sql, $bindings );
		}

		/**
		 * Convenience function to execute Queries directly.
		 * Executes SQL.
		 *
		 * @param string $sql      SQL query to execute
		 * @param array  $bindings a list of values to be bound to query parameters
		 *
		 * @return array
		 */
		//CMP
		public function getAll( $sql, $bindings = array() )
		{
			return $this->query( 'get', $sql, $bindings );
		}

		/**
		 * Convenience function to execute Queries directly.
		 * Executes SQL.
		 *
		 * @param string $sql      SQL query to execute
		 * @param array  $bindings a list of values to be bound to query parameters
		 *
		 * @return string
		 */
		//CMP
		public function getCell( $sql, $bindings = array() )
		{
			return $this->query( 'getCell', $sql, $bindings );
		}

		/**
		 * Convenience function to execute Queries directly.
		 * Executes SQL.
		 *
		 * @param string $sql      SQL query to execute
		 * @param array  $bindings a list of values to be bound to query parameters
		 *
		 * @return array
		 */
		//CMP
		public function getRow( $sql, $bindings = array() )
		{
			return $this->query( 'getRow', $sql, $bindings );
		}

		/**
		 * Convenience function to execute Queries directly.
		 * Executes SQL.
		 *
		 * @param string $sql      SQL query to execute
		 * @param array  $bindings a list of values to be bound to query parameters
		 *
		 * @return array
		 */
		//CMP
		public function getCol( $sql, $bindings = array() )
		{
			return $this->query( 'getCol', $sql, $bindings );
		}

		/**
		 * Convenience function to execute Queries directly.
		 * Executes SQL.
		 * Results will be returned as an associative array. The first
		 * column in the select clause will be used for the keys in this array and
		 * the second column will be used for the values. If only one column is
		 * selected in the query, both key and value of the array will have the
		 * value of this field for each row.
		 *
		 * @param string $sql      SQL query to execute
		 * @param array  $bindings a list of values to be bound to query parameters
		 *
		 * @return array
		 */
		public function getAssoc( $sql, $bindings = array() )
		{
			return $this->query( 'getAssoc', $sql, $bindings );
		}

		/**
		 * Convenience function to execute Queries directly.
		 * Executes SQL.
		 * Results will be returned as an associative array indexed by the first
		 * column in the select.
		 *
		 * @param string $sql      SQL query to execute
		 * @param array  $bindings a list of values to be bound to query parameters
		 *
		 * @return array
		 */
		public function getAssocRow( $sql, $bindings = array() )
		{
			return $this->query( 'getAssocRow', $sql, $bindings );
		}

		/**
		 * Returns the insert ID for databases that support/require this
		 * functionality. Alias for $this->getAdapter()->getInsertID().
		 *
		 * @return mixed
		 */
		public function getInsertID()
		{
			return $this->adapter->getInsertID();
		}

		/**
		 * Exports a collection of beans. Handy for XML/JSON exports with a
		 * Javascript framework like Dojo or ExtJS.
		 * What will be exported:
		 *
		 * * contents of the bean
		 * * all own bean lists (recursively)
		 * * all shared beans (not THEIR own lists)
		 *
		 * @param    array|OODBBean $beans   beans to be exported
		 * @param    boolean        $parents whether you want parent beans to be exported
		 * @param    array          $filters whitelist of types
		 *
		 * @return array
		 */
		//CMP
		public function exportAll( $beans, $parents = FALSE, $filters = array())
		{
			return $this->duplicationManager->exportAll( $beans, $parents, $filters, $this->exportCaseStyle );
		}

		/**
		 * Wipes all beans of type $beanType.
		 *
		 * @param string $beanType type of bean you want to destroy entirely
		 *
		 * @return boolean
		 */
		//CMP
		public function wipe( $beanType )
		{
			return $this->redbean->wipe( $beanType );
		}

		/**
		 * Counts the number of beans of type $type.
		 * This method accepts a second argument to modify the count-query.
		 * A third argument can be used to provide bindings for the SQL snippet.
		 *
		 * @param string $type     type of bean we are looking for
		 * @param string $addSQL   additional SQL snippet
		 * @param array  $bindings parameters to bind to SQL
		 *
		 * @return integer
		 */
		public function count( $type, $addSQL = '', $bindings = array() )
		{
			return $this->redbean->count( $type, $addSQL, $bindings );
		}

		/**
		 * Configures the facade, want to have a new Writer? A new Object Database or a new
		 * Adapter and you want it on-the-fly? Use this method to hot-swap your facade with a new
		 * toolbox.
		 *
		 * @param ToolBox $tb toolbox to configure facade with
		 *
		 * @return ToolBox
		 */
		public function configureFacadeWithToolbox( ToolBox $tb )
		{
			$oldTools                 = $this->toolbox;
			$this->toolbox            = $tb;
			$this->writer             = $this->toolbox->getWriter();
			$this->adapter            = $this->toolbox->getDatabaseAdapter();
			$this->redbean            = $this->toolbox->getRedBean();
			$this->finder             = new Finder( $this->toolbox );
			$this->associationManager = new AssociationManager( $this->toolbox );
			$this->redbean->setAssociationManager( $this->associationManager );
			$this->labelMaker         = new LabelMaker( $this->toolbox );
			$helper                   = new SimpleModelHelper();
			$helper->attachEventListeners( $this->redbean );
			$this->redbean->setBeanHelper( new SimpleFacadeBeanHelper );
			$this->duplicationManager = new DuplicationManager( $this->toolbox );
			$this->tagManager         = new TagManager( $this->toolbox );
			return $oldTools;
		}

		/**
		 * Facade Convience method for adapter transaction system.
		 * Begins a transaction.
		 *
		 * @return bool
		 */
		public function begin()
		{
			if ( !$this->redbean->isFrozen() ) return FALSE;
			$this->adapter->startTransaction();
			return TRUE;
		}

		/**
		 * Facade Convience method for adapter transaction system.
		 * Commits a transaction.
		 *
		 * @return bool
		 */
		public function commit()
		{
			if ( !$this->redbean->isFrozen() ) return FALSE;
			$this->adapter->commit();
			return TRUE;
		}

		/**
		 * Facade Convience method for adapter transaction system.
		 * Rolls back a transaction.
		 *
		 * @return bool
		 */
		public function rollback()
		{
			if ( !$this->redbean->isFrozen() ) return FALSE;
			$this->adapter->rollback();
			return TRUE;
		}

		/**
		 * Returns a list of columns. Format of this array:
		 * array( fieldname => type )
		 * Note that this method only works in fluid mode because it might be
		 * quite heavy on production servers!
		 *
		 * @param  string $table name of the table (not type) you want to get columns of
		 *
		 * @return array
		 */
		//CMP
		public function getColumns( $table )
		{
			return $this->writer->getColumns( $table );
		}

		/**
		 * Short hand function to store a set of beans at once, IDs will be
		 * returned as an array. For information please consult the $this->store()
		 * function.
		 * A loop saver.
		 *
		 * @param array $beans list of beans to be stored
		 *
		 * @return array
		 */
		public function storeAll( $beans )
		{
			$ids = array();
			foreach ( $beans as $bean ) {
				$ids[] = $this->store( $bean );
			}
			return $ids;
		}

		/**
		 * Short hand function to trash a set of beans at once.
		 * For information please consult the $this->trash() function.
		 * A loop saver.
		 *
		 * @param array $beans list of beans to be trashed
		 *
		 * @return void
		 */
		public function trashAll( $beans )
		{
			foreach ( $beans as $bean ) {
				$this->trash( $bean );
			}
		}

		/**
		 * Toggles Writer Cache.
		 * Turns the Writer Cache on or off. The Writer Cache is a simple
		 * query based caching system that may improve performance without the need
		 * for cache management. This caching system will cache non-modifying queries
		 * that are marked with special SQL comments. As soon as a non-marked query
		 * gets executed the cache will be flushed. Only non-modifying select queries
		 * have been marked therefore this mechanism is a rather safe way of caching, requiring
		 * no explicit flushes or reloads. Of course this does not apply if you intend to test
		 * or simulate concurrent querying.
		 *
		 * @param boolean $yesNo TRUE to enable cache, FALSE to disable cache
		 *
		 * @return void
		 */
		public function useWriterCache( $yesNo )
		{
			$this->getWriter()->setUseCache( $yesNo );
		}

		/**
		 * Closes the database connection.
		 *
		 * @return void
		 */
		//CMP
		public function close()
		{
			if ( isset( $this->adapter ) ) {
				$this->adapter->close();
			}
		}

		/**
		 * Optional accessor for neat code.
		 * Sets the database adapter you want to use.
		 *
		 * @return DBAdapter
		 */
		public function getDatabaseAdapter()
		{
			return $this->adapter;
		}

		/**
		 * Returns the current duplication manager instance.
		 *
		 * @return DuplicationManager
		 */
		public function getDuplicationManager()
		{
			return $this->duplicationManager;
		}

		/**
		 * Optional accessor for neat code.
		 * Sets the database adapter you want to use.
		 *
		 * @return QueryWriter
		 */
		//CMP
		public function getWriter()
		{
			return $this->writer;
		}

		/**
		 * Optional accessor for neat code.
		 * Sets the database adapter you want to use.
		 *
		 * @return OODB
		 */
		//CMP
		public function getRedBean()
		{
			return $this->redbean;
		}

		/**
		 * Returns the toolbox currently used by the facade.
		 * To set the toolbox use $this->setup() or $this->configureFacadeWithToolbox().
		 * To create a toolbox use Setup::kickstart(). Or create a manual
		 * toolbox using the ToolBox class.
		 *
		 * @return ToolBox
		 */
		//CMP
		public function getToolBox()
		{
			return $this->toolbox;
		}

		/**
		 * Mostly for internal use, but might be handy
		 * for some users.
		 * This returns all the components of the currently
		 * selected toolbox.
		 *
		 * Returns the components in the following order:
		 *
		 * # OODB instance (getRedBean())
		 * # Database Adapter
		 * # Query Writer
		 * # Toolbox itself
		 *
		 * @return array
		 */
		public function getExtractedToolbox()
		{
			return array(
				$this->redbean,
				$this->adapter,
				$this->writer,
				$this->toolbox
			);
		}

		/**
		 * Tries to find a bean matching a certain type and
		 * criteria set. If no beans are found a new bean
		 * will be created, the criteria will be imported into this
		 * bean and the bean will be stored and returned.
		 * If multiple beans match the criteria only the first one
		 * will be returned.
		 *
		 * @param string $type type of bean to search for
		 * @param array  $like criteria set describing the bean to search for
		 *
		 * @return OODBBean
		 */
		//CMP 尽量也不要用.
		public function findOrCreate( $type, $like = array() )
		{
			return $this->finder->findOrCreate( $type, $like );
		}

		/**
		 * Tries to find beans matching the specified type and
		 * criteria set.
		 *
		 * If the optional additional SQL snippet is a condition, it will
		 * be glued to the rest of the query using the AND operator.
		 *
		 * @param string $type type of bean to search for
		 * @param array  $like optional criteria set describing the bean to search for
		 * @param string $sql  optional additional SQL for sorting
		 *
		 * @return array
		 */
		public function findLike( $type, $like = array(), $sql = '' )
		{
			return $this->finder->findLike( $type, $like, $sql );
		}

		/**
		 * Resets the Query counter.
		 *
		 * @return integer
		 */
		public function resetQueryCount()
		{
			$this->adapter->getDatabase()->resetCounter();
		}

		/**
		 * Returns the number of SQL queries processed.
		 *
		 * @return integer
		 */
		public function getQueryCount()
		{
			return $this->adapter->getDatabase()->getQueryCount();
		}

	}

}

