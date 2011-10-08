<?php

/*
 * This file is part of the Congow\Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Repository class
 *
 * @package    Congow\Orient
 * @subpackage ODM
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 */

namespace Congow\Orient\ODM;

use Congow\Orient\ODM\Manager;
use Congow\Orient\ODM\Mapper;
use Congow\Orient\Query;
use Congow\Orient\Exception;
use Doctrine\Common\Persistence\ObjectRepository;

class Repository implements ObjectRepository
{
    protected $manager;
    protected $mapper;
    protected $className;
    
    /**
     * Instantiates a new repository.
     *
     * @param type $className
     * @param Manager $manager
     * @param Mapper $mapper 
     */
    public function __construct($className, Manager $manager, Mapper $mapper)
    {
        $this->manager   = $manager;
        $this->className = $className;
        $this->mapper    = $mapper;
    }
    
    /**
     * Finds an object by its primary key / identifier.
     *
     * @param   $rid The identifier.
     * @return  object The object.
     */
    public function find($rid)
    {
        $document   = $this->getManager()->find($rid);
        
        if ($document) {
            if ($this->contains($document)) {
                return $document;
            }
            
            $message = "You are asking to find record $rid through the repository ";
            $message .= "{$this->getClassName()} but the document belongs to another repository (" . get_class($document) . ")";
            
            throw new Exception($message);
        }
        
        return null;
    }

    /**
     * Finds all objects in the repository.
     *
     * @return mixed The objects.
     * @todo duplication in the find*()
     */
    public function findAll()
    {
        $results = array();
        
        foreach ($this->getOrientClasses() as $mappedClass) {
            $query      = new Query(array($mappedClass));
            $collection = $this->getManager()->execute($query);

            if (!is_array($collection)) {
                $message = <<<EOT
Problems executing the query "{$query->getRaw()}".
The server returned $collection, while it should be an Array.
EOT;
                
                throw new Exception($message);
            }
            
            $results = array_merge($results, $collection);
        }

        return $results;
    }

    /**
     * Finds objects by a set of criteria.
     *
     * Optionally sorting and limiting details can be passed. An implementation may throw
     * an UnexpectedValueException if certain values of the sorting or limiting details are
     * not supported.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return mixed The objects.
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $results = array();
        
        foreach ($this->getOrientClasses() as $mappedClass) {
            $query      = new Query(array($mappedClass));
            
            foreach ($criteria as $key => $value) {
                $query->where("$key = ?", $value);
            }
            
            foreach ($orderBy as $key => $order) {
                $query->orderBy("$key $order");
            }

            $collection = $this->getManager()->execute($query);

            if (!is_array($collection)) {
                $message = <<<EOT
Problems executing the query "{$query->getRaw()}".
The server returned $collection, while it should be an Array.
EOT;
              
                throw new Exception($message);
            }
            
            $results = array_merge($results, $collection);
        }

        return $results;
    }

    /**
     * Finds a single object by a set of criteria.
     *
     * @param array $criteria
     * @return object The object.
     */
    public function findOneBy(array $criteria)
    {
        
    }
    
    /**
     * Verifies if the $document should belog to this repository.
     *
     * @param   object  $document
     * @return  boolean
     */
    protected function contains($document)
    {
        return in_array($this->getClassName(), class_parents(get_class($document)));
    }
    
    /**
     * Returns the POPO class associated with this repository.
     *
     * @return string
     */
    protected function getClassName()
    {
        return $this->className;
    }
    
    /**
     * Returns the manager associated with this repository.
     *
     * @return Manager
     */
    protected function getManager()
    {
        return $this->manager;
    }
    
    /**
     * Returns the mapper associated with this repository.
     *
     * @return Mapper
     */
    protected function getMapper()
    {
        return $this->mapper;
    }
    
    /**
     * Returns the OrientDB classes which are mapper by the
     * Repository's $className.
     *
     * @return Array 
     */
    protected function getOrientClasses()
    {
        $classAnnotation = $this->getMapper()->getClassAnnotation($this->getClassName());
        
        return explode(',', $classAnnotation->class);
    }
}