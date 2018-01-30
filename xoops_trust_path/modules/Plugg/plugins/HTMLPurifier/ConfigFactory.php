<?php
require_once 'HTMLPurifier.auto.php';
require_once 'HTMLPurifier.php';

class Plugg_HTMLPurifier_ConfigFactory
{
    public static function create(array $options)
    {
        // verify cache directory
        if (empty($options['Cache_SerializerPath']) || !is_dir($options['Cache_SerializerPath'])) {
            trigger_error('Invalid HTMLPurifier cache directory.', E_USER_WARNING);
        }

        if (!is_writable($options['Cache_SerializerPath'])) {
            trigger_error(sprintf('The cache directory %s must be configured writeable by the server.', $options['Cache_SerializerPath']), E_USER_WARNING);
        }

        // remove the port part
        $host = !empty($options['URI_Host']) ? $options['URI_Host'] : $_SERVER['HTTP_HOST'];
        if ($pos = strpos($host, ':')) {
            $options['URI_Host'] = substr($host, 0, $pos);
        } else {
            $options['URI_Host'] = $host;
        }

        $options['HTML_DefinitionRev'] = !isset($options['HTML_DefinitionRev']) ? 1 : $options['HTML_DefinitionRev'];
        $options['Core_Encoding'] = SABAI_CHARSET;

        $config = HTMLPurifier_Config::create($options);
        $config->getHTMLDefinition(true)->addAttribute('a', 'rel', 'CDATA');

        return $config;
    }
}