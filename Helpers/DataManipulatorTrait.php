<?php declare(strict_types=1);

namespace Wpci\Core\Helpers;

/**
 * Getting and incrementing the data in different ways
 *
 * Have to implemented:
 * @method static getDefaultChannel()
 */
trait DataManipulatorTrait
{
    /** @var array $baseData */
    protected static $baseData = [];

    /**
     * Adds some data with specified channel
     * to base data of all objects which will be created
     * witch same channel afterwards
     *
     * @param array $data
     * @param null|string $channel
     */
    public static function addBaseVariables(array $data = [], ?string $channel = null)
    {
        $channel = $channel ?? static::getDefaultChannel();
        static::$baseData[$channel] = static::$baseData[$channel] ?? [];

        static::$baseData[$channel] = array_merge(static::$baseData[$channel], $data);
    }

    /**
     * Receive all existing in given channel base data
     * @param null|string $channel
     * @return array
     */
    protected static function getBaseDataByChannel(?string $channel = null): array
    {
        $channel = $channel ?? static::getDefaultChannel();
        return static::$baseData[$channel] ?? [];
    }

    /** @var array */
    protected $data = [];

    /**
     * Add new variables to the data or redefine existing
     * @param array $data
     * @return $this
     */
    public function addVariables(array $data)
    {
        $this->mergeToTheData($data);
        return $this;
    }

    /**
     * Modify stored value with callable or scalar modifiers
     * @param $modifiers
     * @param null $subKey
     * @return $this
     */
    public function modifyValues($modifiers, $subKey = null)
    {
        $process = function (&$source) use ($modifiers) {
            foreach ($modifiers as $key => $value) {
                if (is_callable($value)) {
                    $source[$key] = $value((isset($source[$key]) ? $source[$key] : null));
                } else {
                    $source[$key] = $value;
                }
            }
        };

        if (is_array($modifiers)) {

            if (is_null($subKey) || !isset($this->data[$subKey])) {
                $process($this->data);
            } else {

                $wrongSubKey = new \InvalidArgumentException(
                    sprintf("Wrong subKey given (%s), have no array value by this sub key", $subKey));;

                if (!is_array($this->data[$subKey])) {
                    throw $wrongSubKey;
                }

                array_map(function ($el) use ($wrongSubKey) {
                    if(!is_array($el)) throw $wrongSubKey;
                }, $this->data[$subKey]);

                foreach ($this->data[$subKey] as $key => $data) {
                    $process($this->data[$subKey][$key]);
                }
            }
        }

        return $this;
    }

    /**
     * Fetch the data. Use it after adds chain
     * @return array
     */
    public function fetch()
    {
        return $this->data;
    }

    /**
     * Merge new values to the data
     * @param array $newData
     */
    protected function mergeToTheData(array $newData)
    {
        $this->data = array_merge($this->data, $newData);
    }
}