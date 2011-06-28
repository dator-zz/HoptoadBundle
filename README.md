Installation
============

  1. Add this bundle to your project as Git submodules:

          $ git submodule add git://github.com/realestateconz/HoptoadBundle.git src/Hoptoad/HoptoadBundle

  2. Add this bundle to your application's kernel:

          // application/ApplicationKernel.php
          public function registerBundles()
          {
              return array(
                  // ...
                  new Hoptoad\HoptoadBundle\HoptoadBundle(),
                  // ...
              );
          }

  3. Go to hoptoadapp.com and create and account. Next edit your project and copy the Current API key
  
  4. Configure the `hoptoad` service in your config:

          # app/config/config.yml
          hoptoad:
            key: 'YOURKEYGOESHERE'
            config: 'curl' # or Zend, Pear (default to curl)

          hoptoad.config: ~

Setting up the PHP Notifier
---------------------------

The PHP notifier is already enabled by the default installation :)

Setting up the JavaScript Notifier
----------------------------------

A templating helper is included for loading the Hoptoad JavaScript Exception Notifer and
initializing it with parameters from your service container. To setup the
Hoptoad JavaScript Exception Notifer environment, add the following to your layout just after
the opening `body` tag:

      <body>
        <?php echo $view['hoptoad']->initialize() ?>

To send an exception raised by your javascript code, you juste need to do so :

      <script type="text/javascript">
          try {
              // ... 
          } catch (e) {
              Hoptoad.notify(e);
          }

      </script>
      
