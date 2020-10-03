<?php


namespace UniBot\Interfaces;


interface BotInterface
{
    /**
     * @param EventInterface $event
     * @return void
     */
    public function update(EventInterface $event);

    /**
     * @return void
     */
    public function execute();

    /**
     * @param string $code
     * @param ProviderInterface $provider
     * @return mixed
     */
    public function addProvider(string $code, ProviderInterface $provider);

    /**
     * @param string $code
     * @return ProviderInterface|null
     */
    public function getProviderByCode(string $code);
}