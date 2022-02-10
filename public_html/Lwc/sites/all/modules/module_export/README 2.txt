Module Export
================

Export all or enabled modules on Drupal as a module or CSV file. The exported
module can be used as a check tool to see if the required modules are all
enabled on the same install or could be used to sync the list of enabled modules
across Drupal installations.

The module allows you to export all of the installed/enabled modules into
another empty module as dependencies. You can verify and enable the exported
module on another Drupal installation after satisfying all the missing
dependency modules.

Drush: To sync and enable modules on a running Drupal installation with the 
exported module, use Drush to download and enable all the modules in one go.
Command:-'drush en -y exported_module_name'

Use Case : This module was built as a need by the developer to fix the enabled
modules list which gets corrupted as a result of missing files or folders when
moving Drupal websites between dev and live environments. This makes sense when
you have more than 100 modules like an Open Atrium Installation on a multisite.
It can also be used in a git based development environment to keep track of 
and verify if the required modules are enabled after a push.

Additional Features
1. Sync across Drupal versions i.e. from D7 to D8 (Limited functionality).
2. Export the module list as a CSV with version numbers.
3. Use as a check tool to inversely verify enabled/disabled modules.
4. Import CSV files to enable and sync modules with version granularity(TODO).

Steps
1. Install this module and go to: admin/modules/export.
2. Select the appropriate settings and the Drupal version of the target system.
3. Click on Export and download the tar file.
4. Extract and copy the generated module into the modules folder of the
target Drupal installation.
5. Enable the module by manually fulfilling the dependencies or by using Drush 
to automatically fulfill dependencies.

Similar Modules
1. Installation Profiles
2. Features
