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
    public function __construct(array $config = null)
    {
        if(is_null($config))
        {
            $config = array(
                'connect'=>array(
                    'old'=>array(
                        'token'=>'',
                        'base_uri'=>''
                    ),
                    'new'=>array(
                        'token'=>'',
                        'base_uri'=>''
                    )
                ),
                'methods'=>array(
                    array(
                        'endpoint'=>null,
                        'method'=>'',
                        'params'=>array(
                            'key'=>'value'
                        ),
                        'content_types'=>array(
                            'types'
                        )
                    )
                ),
                'display_all_results'=>''
            );
        }
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
     * @param string $old_token
     * @param string $old_uri
     * @param string $new_token
     * @param string $new_uri
     */
    public function set_connect($old_token, $old_uri, $new_token = null, $new_uri)
    {
        if(is_null($new_token))
        {
            $new_token = $old_token;
        }

        $this->config['connect'] = array(
            'old'=>array(
                'token'=>$old_token,
                'base_uri'=>$old_uri
            ),
            'new'=>array(
                'token'=>$new_token,
                'base_uri'=>$new_uri
            )
        );
    }

    /**
     * Set option to display all results or not
     * @param boolean $display_opt
     */
    public function set_display_opt($display_opt = true)
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
    public function set_methods(array $methods)
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

        if(is_null($this->config['methods'][0]['endpoint']))
        {
            // Overwrite the sample method set in constructor
            $this->config['methods'] = array($new_method);
        }
        else
        {
            // Add to existing methods
            $this->config['methods'][] = $new_method;
        }
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
        if(!is_readable(__DIR__.'/../schema/Config.json'))
        {
            throw new \Exception('File not readable');
        }
        $schema = file_get_contents(__DIR__.'/../schema/Config.json');

        // Parse json schema
        $json = new \Raml\Schema\Parser\JsonSchemaParser;
        $json_schema = $json->createSchemaDefinition($schema);

        $config = json_encode($this->get_config());

        // Validate config against schema
        if(!$json_schema->validate($config))
        {
            throw new \Exception();
        }
        $return = true;

        return $return;
    }

    /**
     * Sets the config from a json file
     * @param  string $filepath
     * @return array
     *
     * @throws \Exception
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
    public static function create_from_file($filepath)
    {
        if(!is_readable($filepath))
        {
            throw new \Exception("File not readable");
        }

        $config = file_get_contents($filepath);

        return json_decode($config, true);
    }
}
