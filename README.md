This is the new place for my MVC framework assignment

## Rudimentary installation instructions
* Change RewriteBase in .htaccess if needed (for example if you're not running okapi on the "root of your address" i.e. not on http://www.example.com/ but for example http://example.com/sub/okapi/
1. Copy config files: (the following commands assume you are in the root of the okapi folder structure.
<pre>
$ cp application/config/config.sample.php application/config/config.php 
$ cp application/config/site_config.sample.php application/config/site_config.php 
</pre>
2. Change configs to your liking, the important stuff here is valid connection settings to your database if you want the CMS part to work
3. Navigate to http://yourdomain/path/to/okapi/install_db/ (Of course you need to change yourdomain and path/to/okapi to whatever matches your prerequisites.
4. If you don't encounter any problems and if the above navigation leads to the pages telling you it was successful you're all set to go! (the url to the CMS part is http://yourdomain/path/to/okapi/cms/. If you want the CMS part to be your default you could just change the default_controller setting in your config.php file to be cms instead of welcome (which is the default controller after install).

