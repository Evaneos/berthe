<?php

namespace Berthe;

use Berthe\Fetcher;
use Berthe\Fetcher\Fetchable;
use Berthe\ErrorHandler\FunctionalErrorException;

abstract class AbstractService implements Service
{

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var Fetchable
     */
    protected $fetchable;

    /**
     * Constructor
     *
     * @param Manager $manager
     * @param Builder $builder
     */
    public function __construct(Manager $manager = null, Builder $builder = null)
    {
        $this->manager = $manager;
        $this->builder = $builder;
    }

    /**
     * Manager setter
     *
     * @param Manager $manager
     */
    public function setManager(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Builder setter
     *
     * @param Builder $builder
     */
    public function setBuilder(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Fetchable setter
     *
     * @param Fetchable $fetchable
     */
    public function setFetchable(Fetchable $fetchable)
    {
        $this->fetchable = $fetchable;
    }

    /**
     * @inheritdoc
     */
    public function getAll()
    {
        return $this->manager->getAll();
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        return $this->manager->getById($id);
    }

    /**
     * @inheritdoc
     */
    public function getByIds(array $ids = array())
    {
        return $this->manager->getByIds($ids);
    }

    /**
     * @return Fetchable
     */
    protected function getFetchable()
    {
        return isset($this->fetchable) ? $this->fetchable : $this->manager;
    }

    /**
     * @inheritdoc
     */
    public function getIdsByFetcher(Fetcher $fetcher)
    {
        return $this->getFetchable()->getIdsByFetcher($fetcher);
    }

    /**
     * @inheritdoc
     */
    public function getCountByFetcher(Fetcher $fetcher)
    {
        return $this->getFetchable()->getCountByFetcher($fetcher);
    }

    /**
     * @inheritdoc
     */
    public function getByFetcher(Fetcher $fetcher)
    {
        return $this->getFetchable()->getByFetcher($fetcher);
    }

    /**
     * @inheritdoc
     */
    public function getFirstByFetcher(Fetcher $fetcher)
    {
        return $this->getFetchable()->getFirstByFetcher($fetcher);
    }

    /**
     * @inheritdoc
     */
    public function getUniqueByFetcher(Fetcher $fetcher)
    {
        return $this->getFetchable()->getUniqueByFetcher($fetcher);
    }

    /**
     * @inheritdoc
     */
    public function createNew(array $data = array())
    {
        $object = $this->manager->getVoForCreation();
        return $this->save($object, $data);
    }

    /**
     * @inheritdoc
     */
    public function save($object, $data = array())
    {
        $object = $this->builder->updateFromArray($object, $data);

        if (!$this->manager->save($object)) {
            throw new FunctionalErrorException('Creation failed!', 500);
        }

        return $object;
    }

    /**
     * @inheritdoc
     */
    public function delete($object)
    {
        return $this->manager->delete($object);
    }

    /**
     * @inheritdoc
     */
    public function deleteById($id)
    {
        return $this->manager->deleteById($id);
    }
}
