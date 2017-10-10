<?php

namespace Mapudo\Bundle\GuzzleBundle\Tests\DependencyInjection;

use Mapudo\Bundle\GuzzleBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

/**
 * Class ConfigurationTest
 *
 * @category DependencyInjection test
 * @package  Mapudo\Bundle\GuzzleBundle\Tests\DependencyInjection
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 * @link     http://www.mapudo.com
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that configuration works as intended
     *
     * @dataProvider dataProviderSingleClientConfigWithOptions
     * @param array $config The config that should be tested against.
     */
    public function testSingleClientConfigWithOptions(array $config)
    {
        $processor = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration(true), $config);

        // Since the dashes (-) in the configuration get translated to underscores
        // we need to transform the data
        $guzzleHeaders = $config['guzzle']['clients']['test_client']['headers'];
        $config['guzzle']['clients']['test_client']['headers']['Accept_Language'] = $guzzleHeaders['Accept-Language'];
        $config['guzzle']['clients']['test_client']['headers']['X_Auth'] = $guzzleHeaders['X-Auth'];

        unset(
            $config['guzzle']['clients']['test_client']['headers']['Accept-Language'],
            $config['guzzle']['clients']['test_client']['headers']['X-Auth']
        );

        if (!isset($config['guzzle']['clients']['test_client']['request_options']['auth'])) {
            // Since we defined how the content of the "auth" option looks like in the configuration
            // by default an empty array is added
            $config['guzzle']['clients']['test_client']['request_options']['auth'] = null;
        }

        $this->assertEquals($config['guzzle'], $processedConfig);
    }

    /**
     * Returns a loaded config.
     * @return array
     */
    public function dataProviderSingleClientConfigWithOptions(): array
    {
        return [
            [Yaml::parse(file_get_contents(__DIR__ . '/../Resources/config/sample_config.yml'))],
            [Yaml::parse(file_get_contents(__DIR__ . '/../Resources/config/sample_config_auth_string.yml'))],
            [Yaml::parse(file_get_contents(__DIR__ . '/../Resources/config/sample_config_auth_array.yml'))],
        ];
    }
}
