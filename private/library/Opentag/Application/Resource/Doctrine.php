<?php
/*
 * //namespace Opentag\Application\Resource;
 *
 * @since   beta 1.0
 * @version $Revision$
 * @author James A Helly <mrhelly@gmail.com>,  Gediminas Morkevicius <gediminas.morkevicius@gmail.com>
 * @subpackage Doctrine Adapter
 * @package Opentag
 * @category Opentag
 *
 * @see Zend_Application_Resource_ResourceAbstract
 * require_once 'Zend/Application/Resource/ResourceAbstract.php';
 *
 */
use Doctrine\DBAL\DriverManager,
    Doctrine\DBAL\Logging\DebugStack,
    Doctrine\Common\Annotations\AnnotationReader,
    Doctrine\Common\Annotations\AnnotationRegistry,
    Doctrine\ORM\Mapping\Driver\AnnotationDriver,
    Doctrine\ORM\Mapping\Driver\DriverChain,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Configuration,
    Wednesday\Auth\Adapter\Doctrine,
    Wednesday\Restable\RestListener,
    Gedmo\Tree\TreeListener,
    Gedmo\Loggable\LoggableListener,
    Gedmo\Sluggable\SluggableListener,
    Gedmo\Translatable\TranslationListener,
    Gedmo\Timestampable\TimestampableListener;

class Opentag_Application_Resource_Doctrine extends \Zend_Application_Resource_ResourceAbstract {

    /**
     * @var \Doctrine\Common\EventManager
     */
    protected $evtm;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var Doctrine\DBAL\Connection
     */
    protected $conn;

    /**
     * @var Zend_Log
     */
    protected $log;

    /**
     * @var \Doctrine\ORM\Configuration
     */
    protected $cfg;

    /**
     * Constructor.
     *
     * @param array $config Doctrine Container configuration
     */
    public function __construct(array $config = array())
    {
        #Initialise default state for query.
         #Get logger
        $this->log = $this->getBootstrap()->getContainer()->get('logger');
        $this->log->info(get_class($this) . '::init');
        #Use apc for queryCache & configCache {session,default,autheduser,authpipe}
        $this->cfg = new \Doctrine\ORM\Configuration;
        $this->evtm = new \Doctrine\Common\EventManager();
        $this->em = $this->getEntityManager();
   }

    /**
     * Init Doctrine
     *
     * @param N/A
     * @return \Doctrine\ORM\EntityManager Instance.
     */
    public function init() {

        return $this;
    }

    /**
     * Init Doctrine
     *
     * @param N/A
     * @return \Doctrine\ORM\EntityManager Instance.
     */
    public function getEntityManager($options = false) {
        #Get logger
        //$logger = Zend_Registry::get('logger');
        #use apc object cache.
        if (null === $this->log) {
            $this->log = $this->getBootstrap()->getContainer()->get('logger');
        }
        $this->log->info(get_class($this) . '::getEntityManager');
        if ((null === $this->em)) {
            $this->em = $this->_buildEntityManager($this->getOptions());
        }
        $this->getBootstrap()->getContainer()->set('entity.manager', $this->em);
        return $this->em;
    }

    public function getConnection($options = false) {
        #Get logger
        if (null === $this->log) {
            $this->log = $this->getBootstrap()->getContainer()->get('logger');
        }
        $this->log->info(get_class($this) . '::getConnection');
        if ((null === $this->conn)) {
            $this->conn = $this->_buildConnection();
        }
        $this->getBootstrap()->getContainer()->set('doctrine.connection', $this->conn);
        return $this->conn;
    }

    protected function _buildConnection() {
        //die(print_r($options));
        $options = $this->getOptions();

        $connectionOptions = $this->_buildConnectionOptions($options);

        #Setup configuration as seen from the sandbox application
        $config = $this->cfg;
        $eventManager = $this->evtm;

<<<<<<< HEAD
//	$sluggableListener = new SluggableListener();
//        $eventManager->addEventSubscriber($sluggableListener);
//
//        $translatableListener = new TranslationListener();
//        $translatableListener->setTranslatableLocale('en_gb');
//        $eventManager->addEventSubscriber($translatableListener);
//
//        $treeListener = new TreeListener();
//        $eventManager->addEventSubscriber($treeListener);
=======
        $sluggableListener = new SluggableListener();
        $eventManager->addEventSubscriber($sluggableListener);

        $translatableListener = new TranslationListener();
        $translatableListener->setTranslatableLocale('en_gb');
        $eventManager->addEventSubscriber($translatableListener);

        $treeListener = new TreeListener();
        $eventManager->addEventSubscriber($treeListener);
>>>>>>> jah.development

        return \Doctrine\DBAL\DriverManager::getConnection($connectionOptions, $config, $eventManager);
    }

    /**
     * A method to build the connection options, for a Doctrine
     * EntityManager/Connection. Sure, we can find a more elegant solution to build
     * the connection options. A builder class could be applied. Sure you can with
     * some refactor :)
     * TODO: refactor to build some other, more elegant, solution to build the conn
     * ection object.
     * @param Array $params The options array defined in getOptions
     * @return \Doctrine\ORM\EntityManager Instance.
     */
    protected function _buildEntityManager() {
        $this->log->info(get_class($this) . '::buildEntityManager');
        #Options
        $options = $this->getOptions();
        $connection = $this->_buildConnection();
        $config = $this->cfg;
        $eventManager = $this->evtm;

        #Now configure doctrine cache
        if ('development' == APPLICATION_ENV) {
            $cacheClass = isset($options['cacheClass']) ? $options['cacheClass'] : 'Doctrine\Common\Cache\ArrayCache';
        } else {
            $cacheClass = isset($options['cacheClass']) ? $options['cacheClass'] : 'Doctrine\Common\Cache\XcacheCache';
        }

        #Cache Options.
        $cache = new $cacheClass();
        $config->setMetadataCacheImpl($cache);
        #Caches for Development..
        if ('development' == APPLICATION_ENV) {
            $cachedClass = 'Doctrine\Common\Cache\ArrayCache';
            $cached = new $cachedClass();
            $config->setQueryCacheImpl($cached);
            $config->setResultCacheImpl($cached);
        } else {
            $config->setQueryCacheImpl($cache);
            $config->setResultCacheImpl($cache);
        }
        $autoPaths = $this->getBootstrap()->getContainer()->get('autoload.paths');

        #Proxy Configuration
        $config->setProxyDir($options['orm']['manager']['proxy']['dir']);
        $config->setProxyNamespace($options['orm']['manager']['proxy']['namespace']);
        $config->setAutoGenerateProxyClasses($options['orm']['manager']['proxy']['autoGenerateClasses']);

        #Set Logging
        $logger = new \Doctrine\DBAL\Logging\DebugStack;
        $config->setSqlLogger($logger);

        #Driver Configuration
        AnnotationRegistry::registerFile("Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php");
        AnnotationRegistry::registerAutoloadNamespace("Gedmo", CORE_PATH . "/private/library");
        AnnotationRegistry::registerAutoloadNamespace("Doctrine\ORM\Mapping\\", CORE_PATH . "/private/library");
//        AnnotationRegistry::registerAutoloadNamespaces(array("Gedmo\Mapping\Annotation\\","Doctrine\ORM\Mapping\\"));
        $reader = new AnnotationReader();
        $reader->setAnnotationNamespaceAlias('Gedmo\Mapping\Annotation\\', 'gedmo');
//        $reader->setIgnoreNotImportedAnnotations(true);
//        $reader->setEnableParsePhpImports(false);
        $entityPathes = $autoPaths->entities;
        array_push($entityPathes, CORE_PATH . '/private/library/Gedmo/Tree/Entity');
        array_push($entityPathes, CORE_PATH . '/private/library/Gedmo/Sortable/Entity');
        array_push($entityPathes, CORE_PATH . '/private/library/Gedmo/Loggable/Entity');
        array_push($entityPathes, CORE_PATH . '/private/library/Gedmo/Translatable/Entity');

        $chainDriverImpl = new \Doctrine\ORM\Mapping\Driver\DriverChain();
        $defaultDriverImpl = \Doctrine\ORM\Mapping\Driver\AnnotationDriver::create($autoPaths->entities, $reader);
        $defaultDriverImpl->getAllClassNames();
        $translatableDriverImpl = $config->newDefaultAnnotationDriver($entityPathes);
        $chainDriverImpl->addDriver($defaultDriverImpl, 'Application\Entities\\');
//        $chainDriverImpl->addDriver($translatableDriverImpl, 'Gedmo\Tree');
//        $chainDriverImpl->addDriver($translatableDriverImpl, 'Gedmo\Sortable');
//        $chainDriverImpl->addDriver($translatableDriverImpl, 'Gedmo\Loggable');
//        $chainDriverImpl->addDriver($translatableDriverImpl, 'Gedmo\Translatable');
        $config->setMetadataDriverImpl($chainDriverImpl);

        #setup entity manager
        $em = \Doctrine\ORM\EntityManager::create(
                        $connection, $config, $eventManager
        );

        return $em;
    }

    /**
     * A method to build the connection options, for a Doctrine
     * EntityManager/Connection. Sure, we can find a more elegant solution to build
     * the connection options. A builder class could be applied. Sure you can with
     * some refactor :)
     * TODO: refactor to build some other, more elegant, solution to build the conn
     * ection object.
     * @param Array $options The options array defined on the application.ini file
     * @return Array
     */
    protected function _buildConnectionOptions(array $options) {
        $connectionSpec = array(
            'pdo_sqlite' => array('path', 'memory', 'user', 'password'),
            'pdo_mysql' => array('user', 'password', 'host', 'port', 'dbname', 'unix_socket', 'charset'),
            'pdo_pgsql' => array('user', 'password', 'host', 'port', 'dbname'),
            'pdo_oci' => array('user', 'password', 'host', 'port', 'dbname', 'charset')
        );
        $dbalopts = $options['dbal'][$options['orm']['manager']['connection']];
        $connection = array('driver' => $dbalopts['driver']);

        #Simple array map.
        foreach ($connectionSpec[$dbalopts['driver']] as $driverOption) {
            if (isset($dbalopts[$driverOption]) && !is_null($driverOption)) {
                $connection[$driverOption] = $dbalopts[$driverOption];
            }
        }

        return $connection;
    }

}