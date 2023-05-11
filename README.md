# mod_child_export

A very simple (administrator) module that allows to export child templates as installable zip files.
The module should be placed in the position `cpanel` and by default adds only a button, a javascript and an inner modal (ie, won't degrade your performace compared to other things that have many database queries, etc).

## Mini docs

After installing the module and checking the Administrator Modules you wiil find a new module on the top of the list.

![List of modules](https://github.com/dgrammatiko/mod_child_export/blob/main/images/step1.png?raw=true)

Edit the `mod_exportchild` module and change the position to `cpanel` and Status to `Published`

![Edit a module](https://github.com/dgrammatiko/mod_child_export/blob/main/images/step2.png?raw=true)

No the Dashboard view has a new section

![Edit a module](https://github.com/dgrammatiko/mod_child_export/blob/main/images/cpanel.png?raw=true)

Clicking on the button you get the list of all the Child Templates with an export button. The export button does what you expect it will...

![The export list](https://github.com/dgrammatiko/mod_child_export/blob/main/images/export-list.png?raw=true)
