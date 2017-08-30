StudioSiteMonitoringBundle
=====================

Sometimes it becomes necessary to monitor some parameters using external monitoring systems (for example, zabbix). The bundle makes it very easy

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require studiosite/monitoring-bundle
```

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new StudioSite\MonitoringBundle\StudioSiteMonitoringBundle(),
        );
    }
}
```

Step 3: Configuration
-------------------------------------
```yml
studiosite_monitoring:
    # path to console file for zabbix config generator
    # usually the bundle finds the desired path
    console: "%kernel.root_dir%/../bin/console"
    zabbix:
        # template for generating zabbix config
        template: "StudioSiteMonitoringBundle:Zabbix:userParameters.conf.twig"
```

Example of use
--------------

Consider an example service that provides values of parameters
```php
<?php
// AppBundle/Service/SomeService.php

class SomeService
{
    // Your magic...

    public function getSomeValue()
    {
        return 'teststring';
    }

    public function getCount()
    {
        return 100;
    }

    public function getSum($a, $b)
    {
        return $a + $b;
    }
}
```
The service description in DI

```yml
services:
    app_bundle.some_service:
        class: AppBundle\Service\SomeService
        tags:
            - { name: studiosite_monitoring.parameter, method: getSomeValue, key: app.some_value }
            - { name: studiosite_monitoring.parameter, method: getCount, key: app.count }
            - { name: studiosite_monitoring.parameter, method: getSum, key: app.sum }
```

Now you can get parameter values

```console
# To see a list of available parameters:
$ php bin/console studiosite:monitoring:get

The list of available parameters:
    app.some_value
    app.count
    app.sum <a> <b>

# To get the value of the parameter:

$ php bin/console studiosite:monitoring:get app.some_value
teststring

$ php bin/console studiosite:monitoring:get app.sum 100 100
200
```

Generate zabbix user parameter config
-------------------------------------

The bundle can generate a configuration for all the collected parameters in zabbix userparameter format

```console
$ php bin/console studiosite:monitoring:zabbix symfony.conf --destination=/etc/zabbix/conf.d/
Write config to /etc/zabbix/conf.d/symfony.conf? y

$ cat /etc/zabbix/conf.d/symfony.conf
UserParameter=app.some_value[*], /var/www/dev/bin/console studiosite:monitoring:get app.some_value --no-debug -e prod
UserParameter=app.count[*], /var/www/dev/bin/console studiosite:monitoring:get app.count --no-debug -e prod
UserParameter=app.sum[*], /var/www/dev/bin/console studiosite:monitoring:get app.sum $1 $2 --no-debug -e prod
```
