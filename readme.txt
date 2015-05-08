Welcome to the DotKernel @VERSION@ release

DotKernel is a PHP Application Framework, built on top of Zend Framework (ZF)
	
	http://www.dotkernel.com/

RELEASE INFORMATION
-------------------
DotKernel @VERSION@ - @DATERELEASE@ (r@HEADREVISION@)

For detailed changes, please see 
	http://www.dotkernel.com/changelog/@CHANGELOGVER@


SYSTEM REQUIREMENTS
-------------------

DotKernel requires:
- APACHE HTTP server
- PHP 5.4.0 or later ( PHP 5.6.x recommended)
- MySQL 5.5 or later (MySQL 5.6.x recommended )
- Zend Framework 1.11.0 or later (Zend Framework 1.12.6 recommended)
For more details, please see: 
	http://www.dotkernel.com/docs/system-requirements/


INSTALLATION
------------

To install DotKernel, follow the steps below.
Note: It is important to follow the steps in the exact order below (especially steps 9, 10, 11).

   1. Download dk.php file to test that your environment meets DotKernel system requirements
   2. Download a copy of DotKernel
   3. Unzip the downloaded package. You should have a DotKernel-@VERSION@ directory
   4. Upload the folder content to your web server
   5. Create a MySQL database and import dot_kernel.sql file into it
   6. Set your environment (production, staging or development) in .htaccess by using the APPLICATION_ENV variable. In DotKernel, the development 
      environment is set by default.
      NOTE: You might want to disable the caching till you edit all the xml's you need, otherwise the changes will be ignored as the old xml data is still cached
   7. Depending on your environment, edit the configuration file configs/application.ini to reflect the website url and the connection to the database 
      you created previously
      NOTE: Make sure the database and the application.ini have the same collation set
   8. Edit the seo.xml file (found in /configs/dots/) to reflect the website title, description and other seo information
   9. Test the installation by opening the URL you set as your website. If you see the Home page, your DotKernel installation is completed.
  10. Log In into admin module: http://www.yourdotkernelinstance.com/admin/ with these credentials
      username: admin
      password: dot
  11. Change your admin password: http://www.yourdotkernelinstance.com/admin/admin/account/ 
   
   
DOCUMENTATION
---------

Online documentation can be found at 
	http://www.dotkernel.com/phpdocs/
     
QUESTIONS
---------

If you have any questions about DotKernel, visit our website:
	http://www.dotkernel.com/
	
FEEDBACK
--------

If you encounter any unexpected behavior of the application, please report the 
issue in the DotKernel bug tracker:
	http://www.dotkernel.net/
	

SUBVERSION REPOSITORY
----------------------
You can grab latest DotKernel copy from:
	 http://v1.dotkernel.net/svn/trunk/

Browse SVN repository:
	http://websvn.dotkernel.net/listing.php?repname=DotKernel


LICENSE
-------

DotKernel is Open Source, and released under Open Software License (OSL 3.0)
See http://opensource.org/licenses/osl-3.0

This product includes GeoLite data created by MaxMind, available from
  <a href="http://www.maxmind.com">http://www.maxmind.com</a>.
