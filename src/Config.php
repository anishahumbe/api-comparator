<?php

namespace Giift\Compare;

class Config
{
    protected $config = array();

    /**
     * Instantiates the config
     * @param array $config
     *
     * <pre>
     * $config = array(
     *     'connect'=>array(
     *         'old'=>array(
     *             // string Access token
     *             'token'=>'',
     *             // string Base uri
     *             'base_uri'=>''
     *         ),
     *         'new'=>array(
     *             // string Access token
     *             'token'=>'',
     *             // string Base uri
     *             'base_uri'=>''
     *         )
     *     ),
     *     'methods'=>array(
     *         array(
     *             // string Method uri
     *             'endpoint'=>'',
     *             // string Type of method
     *             'method'=>'',
     *             // array  Parameters required for method
     *             'params'=>array(
     *                 // string
     *                 'key'=>'value'
     *             ),
     *             // array Types of response
     *             'content_types'=>array(
     *                 'types'
     *             )
     *         )
     *     ),
     *     // boolean Display all results (true) or only differences (false)
     *     'display_all_results'=>''
     * );
     * </pre>
     *
     */
    public function __construct($config = array())
    {
        $this->config = $config;
    }

    /**
     * Returns the config
     * @return array
     *
     * <pre>
     * $array = array(
     *     'connect'=>array(
     *         'old'=>array(
     *             // string Access token
     *             'token'=>'',
     *             // string Base uri
     *             'base_uri'=>''
     *         ),
     *         'new'=>array(
     *             // string Access token
     *             'token'=>'',
     *             // string Base uri
     *             'base_uri'=>''
     *         )
     *     ),
     *     'methods'=>array(
     *         array(
     *             // string Method uri
     *             'endpoint'=>'',
     *             // string Type of method
     *             'method'=>'',
     *             // array  Parameters required for method
     *             'params'=>array(
     *                 // string
     *                 'key'=>'value'
     *             ),
     *             // array Types of response
     *             'content_types'=>array(
     *                 'types'
     *             )
     *         )
     *     ),
     *     // boolean Display all results (true) or only differences (false)
     *     'display_all_results'=>''
     * );
     * </pre>
     *
     */
    public function get_config()
    {
        return $this->config;
    }

    /**
     * Set the token and base uris for both APIs
     * @param array $connect
     *
     * <pre>
     * $connect = array(
     *     'old'=>array(
     *         // string Access token
     *         'token'=>'',
     *         // string Base uri
     *         'base_uri'=>''
     *     ),
     *     'new'=>array(
     *         // string Access token
     *         'token'=>'',
     *         // string Base uri
     *         'base_uri'=>''
     *     )
     * );
     * </pre>
     *
     */
    public function set_connect($connect)
    {
        $this->config['connect'] = $connect;
    }

    /**
     * Set option to display all results or not
     * @param boolean $display_opt
     */
    public function set_display_opt($display_opt)
    {
        $this->config['display_all_results'] = $display_opt;
    }

    /**
     * Set the methods
     * @param array $methods
     *
     * <pre>
     * $methods = array(
     *     array(
     *         // string Method uri
     *         'endpoint'=>'',
     *         // string Type of method
     *         'method'=>'',
     *         // array  Parameters required for method
     *         'params'=>array(
     *             // string
     *             'key'=>'value'
     *         ),
     *         // array Types of response
     *         'content_types'=>array(
     *             'types'
     *         )
     *     )
     * );
     * </pre>
     *
     */
    public function set_methods($methods)
    {
        $this->config['methods'] = $methods;
    }

    /**
     * Add a method
     * @param string $endpoint
     * @param string $method
     * @param array $content_types
     * @param array $params
     *
     * <pre>
     * $content_types = array(
     *     'types'
     * );
     * $params = array(
     *     'key'=>'value'
     * );
     * </pre>
     *
     */
    public function add_method($endpoint, $method, array $content_types, array $params = null)
    {
        $new_method = array();
        $new_method['endpoint'] = $endpoint;
        $new_method['method'] = $method;
        $new_method['content_types'] = $content_types;

        if(!is_null($params))
        {
            $new_method['params'] = $params;
        }

        $this->config['methods'][] = $new_method;
    }

    /**
     * Validates config against json schema
     * @throws \Exception
     *
     * @return boolean
     */
    public function validate()
    {
        $return = false;

        // Get json schema
        try
        {
            if(!is_readable(__DIR__.'/../schema/Config.json'))
            {
                throw new \Exception('File not readable');
            }
            $schema = file_get_contents(__DIR__.'/../schema/Config.json');
        }
        catch(\Exception $e)
        {
            echo $e->getMessage();
        }

        // Parse json schema
        $json = new \Raml\Schema\Parser\JsonSchemaParser;
        $json_schema = $json->createSchemaDefinition($schema);

        $config = json_encode($this->get_config());

        // Validate config against schema
        try
        {
            if(!$json_schema->validate($config))
            {
                throw new \Exception();
            }
            $return = true;
        }
        catch(\Exception $e)
        {}

        return $return;
    }
}
