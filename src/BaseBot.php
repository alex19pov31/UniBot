<?php


namespace UniBot;


use UniBot\Interfaces\BotInterface;
use UniBot\Interfaces\EventInterface;
use UniBot\Interfaces\ProviderInterface;

abstract class BaseBot implements BotInterface
{
    /**
     * @var array
     */
    protected $providers = [];

    /**
     * @param EventInterface $event
     * @return void
     */
    abstract public function update(EventInterface $event);

    /**
     * @return void
     */
    abstract public function execute();

    /**
     * @param string $code
     * @param ProviderInterface $provider
     * @return mixed|void
     */
    public function addProvider(string $code, ProviderInterface $provider)
    {
        $provider->setBot($this);
        $this->providers[$code] = $provider;
    }

    /**
     * @param string $code
     * @return ProviderInterface|null
     */
    public function getProviderByCode(string $code)
    {
        if ($this->providers[$code] instanceof ProviderInterface) {
            return $this->providers[$code];
        }

        return null;
    }
}