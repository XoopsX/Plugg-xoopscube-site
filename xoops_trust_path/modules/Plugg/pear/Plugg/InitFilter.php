<?php
require_once 'Sabai/Application/ControllerFilter.php';

class Plugg_InitFilter extends Sabai_Application_ControllerFilter
{
    private $_isAdmin;

    public function __construct($isAdmin = false)
    {
        $this->_isAdmin = $isAdmin;
    }

    public function before(Sabai_Application_Context $context, Sabai_Application $application)
    {
        // Show debug messages?
        if ($application->getConfig('showDebugMessages')) {
            Sabai_Log::level(Sabai_Log::ALL);
            require_once 'Sabai/Log/Writer/HTML.php';
            Sabai_Log::writer(new Sabai_Log_Writer_HTML());
        }

        // Is it an AJAX request?
        if ($context->request->isAjax()) {
            $context->response->setRedirect(false) // no redirection
                ->setLayoutFile('ajax.html')
                ->setContentStackLevel(1); // Set default content stack level to 1
        }

        // Set identity data loader for lazy loading extra user data
        if ($context->user->isAuthenticated()) {
            $identity_fetcher = $application->getService('UserIdentityFetcher');
            $context->user->getIdentity()->setDataLoader(array($identity_fetcher, 'loadIdentityWithData'));
        }

        // Add link to homepage
        $context->response->setPageInfo($application->getGettext()->_('Home'), array('base' => '', 'path' => ''));

        // Define token name
        define('SABAI_TOKEN_NAME', Plugg::TOKEN);

        // Set some useful template helpers
        $context->response->getTemplate()->setPluggObjects($application)
            ->setObject('User', $context->user)
            ->setObject('Request', $context->request);
    }

    public function after(Sabai_Application_Context $context, Sabai_Application $application)
    {
        // Set some view related variables for the requested plugin
        if (isset($context->plugin)) {
            // Template
            $context->response->getTemplate()->setObject('Plugin', $context->plugin)
                ->addTemplateDir($context->plugin->getTemplatePath());

            // Add CSS file if any
            $plugin_library = $context->plugin->getLibrary();
            if ($this->_isAdmin) {
                if ($context->plugin->hasAdminCSS()) {
                    $context->response->addCSSFile(
                        $application->getUrl()->getCssUrl($plugin_library, 'Admin.css'),
                        'screen',
                        $plugin_library
                    );
                }
            } else {
                if ($context->plugin->hasMainCSS()) {
                    $context->response->addCSSFile(
                        $application->getUrl()->getCssUrl($plugin_library),
                        'screen',
                        $plugin_library
                    );
                }

                // Check if the plugin is clone and has own template directory and css file
                if ($context->plugin->isClone()) {
                    $plugin_name = $context->plugin->getName();
                    $plugin_dir = $application->getPluginManager()->getPluginDir();
                    if (is_dir($template_dir = $plugin_dir . '/' . $plugin_name . '/templates')) {
                        $context->response->getTemplate()->addTemplateDir($template_dir);
                    }
                    if (is_file(sprintf('%1$s/%2$s/css/Main.css', $plugin_dir, $plugin_name))) {
                        $context->response->addCSSFile($application->getUrl()->getCssUrl($plugin_name));
                    }
                }
            }
        }

        if ($context->request->isAjax()) $context->response->clearPageInfo();

        // Any specific content region or stack level requested?
        if ($content_region = $context->request->getContentRegion()) {
            $context->response->setContentRegion($content_region)
                ->setContentStackLevel(0); // Remove stack level limit if content region is set
        } elseif ($content_stacklevel = $context->request->getContentStackLavel()) {
            $context->response->setContentStackLevel($content_stacklevel);
        }
    }
}