<?php
namespace Berthe;

abstract class AbstractService implements Service {
    
    /**
     * 
     * @var Manager
     */
    protected $manager;
    
    /**
     * 
     * @var Builder
     */
    protected $builder;
    
    /**
     * Constructor
     * 
     * @param Manager $manager
     */
    public function __construct(Manager $manager = null, Builder $builder = null) {
        $this->manager = $manager;
        $this->builder = $builder;
    }
    
    /**
     * Manager setter
     * 
     * @param Manager $manager
     */
    public function setManager(Manager $manager) {
        $this->manager = $manager;
    }
    
    /**
     * Builder setter
     * 
     * @param Builder $builder
     */
    public function setBuilder(Builder $builder) {
        $this->builder = $builder;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Berthe\Service::getAll()
     */
    public function getAll() {
        return $this->manager->getAll();
    }
    
    /**
     * (non-PHPdoc)
     * @see \Berthe\Service::getById()
     */
    public function getById($id) {
        return $this->manager->getById($id);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Berthe\Service::getByIds()
     */
    public function getByIds(array $ids = array()) {
        return $this->manager->getByIds($ids);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Berthe\Service::getByFetcher()
     */
    public function getByFetcher(Fetcher $fetcher) {
        return $this->manager->getByFetcher($fetcher);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Berthe\Service::createNew()
     */
    public function createNew(array $data = array()) {
        $object = $this->manager->getVoForCreation();
        return $this->save($object, $data);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Berthe\Service::save()
     */
    public function save($object, array $data = array()) {
        $object = $this->builder->updateFromArray($object, $data);
        
        if (!$this->manager->save($object)) {
            throw new \FunctionalErrorException('Creation failed!', 500);
        }
        
        return $object;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Berthe\Service::delete()
     */
    public function delete($object) {
        return $this->manager->delete($object);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Berthe\Service::deleteById()
     */
    public function deleteById($id) {
        return $this->manager->deleteById($id);
    }
    
}