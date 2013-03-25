N/X Release Notes for 4.1
------------------------------------
Version 4.1
------------------------------------


CONTENT

  1. General & License
  2. Installation
  3. Requirements
  4. Questions
  5. Contributions
  6. Bugs
  

1. General
----------

This is beta software. You are using this software at your own risk! This software is
released under GNU General Public License. Read license.txt for more information. If you
do not agree to the license, you must not use this program.

2. Installation
---------------

There is a install-script shipped with N/X 4.1. Running the install.php the only thing you
should need to change is the database-configuration. If you are lucky, the rest is determined
correctly by the installer.

For setting up N/X read the installation guide in the docs folder. 
There is one important change! The config.inc.php and the settings.inc.php have
merged to the new config.inc.php. So there is only one file which needs to be
configured.

The config.inc.php has a simple config and a expert config part. You can use either the 
expert or the simple-config but you never(!) should use both configs at a time. You need
to set the config-variable (e.g. $simpleconfig=true). You need to comment the expert config
out in this case (which is default).

We will provide a installer with the final version.


3. Requirements
----------------

N/X is designed to run with a minimum of requirements and should run on popular providers 
like 1und1 and so on. We have at the moment no data on which providers N/X runs without or
with limitation. Any information about this is appreciated!

At the moment the richtext-editor which is based on fckeditor is running on Internet Explorer
only. This will change as soon as a new version of the editor is available. Firefox will then
be also supported.

Try to give the server the following environment:

mySQL 4.0.x
Apache >1.3.30
php >= 4.3.10

The system will definitely run also with older versions, however we made the experience
that the older the software (especially php) is, the more problems may occur.

Some extensions of N/X require the following OPTIONAL modules:

- gd2-extension
- zip-extension (office plugin, bulk-image importer)
- ImageMagick (installation on linux or windows, no php module required)


4. Questions
-------------

If you have any questions, please look at our website first (www.nxsystems.org). Take a look
at the faq. If your question is not answered there, klick in the menu on "Ask a question" and 
tell us, what you want to know. 



5. Contributions
----------------

We are thankful for any contributions like new ideas, sourcecode, plugins, themes, templates or money. 
If you are using N/X on a commercial project, a small donation to the developers of the system would be nice
and would help to ensure, we can continue the project for the next years.


6. Bugs
--------
We have included a bug reporting form into the N/X system. If you think, anything is wrong and if you checked
all the points of our installation and getting started documentation, then please report the bug at sourceforge.
It would be nice, if you check for duplicate bug reports.


Sven Weih
Karlsruhe, Germany February 2006
