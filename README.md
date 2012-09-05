EEClickTale
===========

An EE extension to implement ClickTale

INSTALLATION
================================================
- Install the extension in the system/expressionengine/third_party folder
	* Edit the libraries/ClickTaleScripts.xml file with the necessary code. (TODO: Move this to CP settings)
	* Make sure the Cache and Logs folder have read and write permissions (775).
- Enable the extension in the CP.
- Add the clicktale folder to the webroot.
	* Change the permissions of this folder to 775, recursively.
	* Make sure the system path is set correctly in the ClickTaleCache.php file (should point to the EE system folder).
- Add the cron.clicktale_cache.php script to the crontab to run every day.
- Make sure to ignore the Cache and Logs directories (in the extension directory) in Git.