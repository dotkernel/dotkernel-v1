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
- PHP 5.3.7 or later ( PHP 5.5.x recommended)
- MySQL 5.0 or later (MySQL 5.5.x recommended )
- Zend Framework 1.8.0 or later (Zend Framework 1.12.6 recommended)
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
   7. Depending on your environment, edit the configuration file configs/application.ini to reflect the website url and the connection to the database 
      you created previously
   8. Test the installation by opening the URL you set as your website. If you see the Home page, your DotKernel installation is completed.
   9. Log In into admin module: http://www.yourdotkernelinstance.com/admin/ with username: admin and password: dot
  10. Change your admin password: http://www.yourdotkernelinstance.com/admin/admin/account/ 
   
   
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
See http://v1.dotkernel.net/license.txt

This product includes GeoLite data created by MaxMind, available from
  <a href="http://www.maxmind.com">http://www.maxmind.com</a>.
