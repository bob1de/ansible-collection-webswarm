bob1de.webswarm
===============

This collection provides roles for setting up a Docker Swarm suitable for running
arbitrary web (and other) applications.

In addition to the plain Swarm setup, a bunch of roles providing services that can
ease the deployment of custom web applications is included:


Roles
-----

The following roles are contained:

* Swarm setup:

  * ``bob1de.webswarm.swarm``:
    Assemble a Docker Swarm of multiple hosts.

* Services that ease the deployment of custom web applications:

  * ``bob1de.webswarm.traefik``:
    Traefik as frontend web server and flexible load-balancing reverse proxy.

  * ``bob1de.webswarm.registry``:
    A private Docker registry for providing custom images.

  * ``bob1de.webswarm.elastic``:
    Elastic stack (Elasticsearch, Kibana and Filebeat) for collection and inspection
    of log messages.

  * ``bob1de.webswarm.mariadb``:
    MariaDB as database for custom applications.

* Miscellaneous roles for maintenance tasks:

  * ``bob1de.webswarm.generate_secrets``:
    Generate random secrets for services provided by the different roles.

  * ``bob1de.webswarm.proxy_container``:
    Temporarily proxy network traffic from the host to a Docker container otherwise
    isolated in some Docker network for running maintenance tasks.

  * ``bob1de.webswarm.self_signed``:
    Generate self-signed X509 certificates.

See the README files of the individual roles to learn about their requirements,
mutual dependencies, configuration and usage.


Requirements
------------

The collection is designed for and was tested with Debian 10 and 11 systems on both
controller and Swarm hosts.

The requirements listed here are common to all roles and serve as a starting point
for your customized selection of services to deploy.
The individual roles you'll pick list additional requirements in their README files.

The following Debian packages need to be installed on the Ansible controller and
all managed hosts:

* iproute2


Installation
------------

It's strongly recommended to install and use this collection in a Python virtual
environment in order to avoid dependencies conflicting with packages installed
system-wide.
The steps described here will help you setting it up.
Feel free to install the collection in a different way if you know what you're doing.

Install the following Debian packages on the Ansible controller host:

* python3-pip
* python3-setuptools
* python3-venv
* python3-wheel

First, you should create and enter the directory you want to base your Ansible
project in:

.. code-block:: bash

   mkdir myproject
   cd myproject

Then, a Python virtual environment should be set up as follows (change the ``myvenv``
directory according to your liking):

.. code-block:: bash

   python3 -m venv myvenv
   source myvenv/bin/activate
   pip install -U pip setuptools wheel
   pip install -U ansible

Each time you want to work with the collection after starting a new shell, activate
the virtual environment first:

.. code-block:: bash

   source myvenv/bin/activate

In order to store installed roles and collections together with your project instead
of in ``~/.ansible``, create an ``ansible.cfg`` file:

.. code-block:: yaml

   [defaults]
   # Install collections into ./ansible_collections/<namespace>/<collection_name>
   collections_paths = ./
   # Install roles into ./roles/<namespace>.<role_name>
   roles_path = ./roles

Now a ``requirements.yml`` file should be set up, declaring your project's dependence
on this collection:

.. code-block:: yaml

   collections:
     - name: bob1de.webswarm
       source: https://github.com/bob1de/ansible-collection-webswarm
       # Pick a branch or tag
       version: master

Finally install the requirements just defined:

.. code-block:: bash

   ansible-galaxy install -f -r requirements.yml

If you instead want to install directly from a local clone of the repository, for
instance to test custom changes, install like so:

.. code-block:: bash

   ansible-galaxy collection install -f /path/to/cloned/repo


Usage
-----

Use of this role typically involves multiple playbooks.

Generate Random Secrets
~~~~~~~~~~~~~~~~~~~~~~~

This step is optional.
You may also specify all the password and secret variables for the different services
yourself, but the included secrets file generator makes this work a breeze.

Just run the included ``bob1de.webswarm.generate_secrets`` playbook, which does
nothing more than executing the role of same name on localhost.

.. code-block:: bash

   ansible-playbook bob1de.webswarm.generate_secrets

Now you should have a file ``vars/webswarm-secrets.yml`` with all kinds of secrets
you need for running the roles from this collection.
Inspect the file's contents and adapt it to your needs.
You are free to remove any secrets from that file related to services you are not
planning to deploy, but keeping the unused ones is no problem either.

The path of the generated file can be changed with ``-e secrets_file=some-path.yml``.

Add ``-e replace_secrets_file=true`` if you want an existing file to be replaced
with a new set of random secrets.
Don't panic, the previous file will be backed up before getting replaced.

With ``-e '{"admin_usernames":["alice","bob","mallory"]}'``, you can pass a
JSON-encoded list of usernames for conveniently populating the ``admin_users`` list.


Set up Swarm and System Services
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

How the Swarm and some of the roles deploying system services can be set up is shown in the included demo scenario.

Just have a look at the playbook in ``demo/molecule/default/deploy-webswarm.yml``,
which includes documentary comments.


Deploy Custom (Web) Applications
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The demo scenario deploys a simple todo web application written in PHP, which is
replicated, load-balanced by Traefik and uses MariaDB for storing data.

See the documented playbook ``demo/molecule/default/sample-todo-app/deploy.yml``.


License
-------

GPL-3.0-or-later
