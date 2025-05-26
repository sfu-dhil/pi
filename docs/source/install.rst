.. _install:

Installation
============

.. note::

    PI doesn't use labeled or numbered releases. The code in the
    master branch of the repository should be runnable.

Make sure the requirements are satisfied.

The PI application is based on Symfony 4.4. Installation follows the normal
process for installing a Symfony application.

1. Get the code from GitHub. 

.. code-block:: bash

  git clone https://github.com/sfu-dhil/pi.git

2. Get the submodules from Git. There is quite a bit of reusable code in the
   application, and it's organized with git submodules.

.. code-block:: bash

  git submodule init
  git submodule update --recursive --remote

3. Create a database and database user.
  
.. code-block:: sql

  create database pi;
  create user if not exists pi@localhost;
  grant all on pi.* to pi@localhost;
  set password for pi@localhost = password('abc123');

4. `Install composer`_ if it isn't already installed somewhere.
  
5. Install the composer dependencies. Composer will ask for some 
   configuration variables during installation.
  
.. code-block:: bash

  ./vendor/bin/composer install --no-dev -o
   
Sometimes composer runs out of memory. If that happens, try this alternate.
  
.. code-block:: bash

  php -d memory_limit=-1 ./vendor/bin/composer install --no-dev -o

6. Update file permissions. The user running the web server must be
   able to write to `var/cache/*` and `var/logs/*` and
   `var/sessions/*`. The symfony docs provide `recommended commands`_
   depending on your OS.
  
7. If you have affiliation with the DHIL lab and have the required data for this project, you could run below command in the terminal to directly insert the data into the database and skip steps 8 and 9. Make sure that you have the recent data available and it would be different from the below mentioned one in the future. The example format would be like "pi-2020-03-25-dhil-r35.sql" with date mentioned in it. Please use the recent data file instead of the below given one.
  
.. code-block:: bash

  mysql -u pi -p pi < pi-2020-03-25-dhil-r35.sql
   
If you don't have the data, then proceed to step 8 and 9
    
8. Load the schema into the database. This is done with the 
   symfony console.
  
.. code-block:: bash

  ./bin/console doctrine:schema:update --force
  
9. Create an application user with full admin privileges. This is also done 
   with the symfony console.
  
.. code-block:: bash

  ./bin/console nines:create:user  
  
10. If you haven't installed npm and yarn globally, you will have to install them. You could do this by running the below commands in the terminal.
  
.. code-block:: bash

  sudo apt install npm
  sudo npm install --global yarn

11. If you have installed npm and yarn globally, then set up yarn for this project by running the below command inside project directory.
  
.. code-block:: bash

  yarn install

12. Please follow the instructions in the config.rst file to set up the configuration settings for this project.

13. Configure the web server. The application's `public/` directory must
    be accessible to the world. Symfony provides `example
    configurations`_ for most server setups.

14. Start the Symfony server by using the below command and navigate to the link displayed.
  
.. code-block:: bash

  symfony server:start

At this point, the web interface should be up and running, and you should
be able to login by following the Login link in the top right menu bar.

15. Once everything is done, you should stop the Symfomny server. Before you close the terminal, make sure to stop the server using this command.
  
.. code-block:: bash

  symfony server:stop

That should be it.

.. _`Install composer`: https://getcomposer.org/download/

.. _`recommended commands`:
   http://symfony.com/doc/current/setup/file_permissions.html

.. _`example configurations`:
   http://symfony.com/doc/current/setup/web_server_configuration.html
