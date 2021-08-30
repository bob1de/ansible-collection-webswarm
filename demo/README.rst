bob1de.webswarm Demonstration
=============================

This demo uses molecule with Vagrant and the libvirt provider to create KVM virtual
machines it then deploys roles of the ``bob1de.webswarm`` collection on.

After the Swarm and system services were set up
(``molecule/default/deploy-webswarm.yml``) a sample todo web app is deployed to the
Swarm (``molecule/default/sample-todo-app/deploy.yml``).


Requirements
------------

The following Debian packages need to be installed (Debian 10 or later):

* libvirt-daemon-system
* qemu-system-x86
* vagrant
* vagrant-libvirt

This molecule scenario is somewhat special in that it builds the Docker image of the
sample web app on your local system instead of a virtual machine before it's pushed
to the private Docker registry and finally deployed to the virtualized Swarm.
Therefore, you also need a local Docker installation and Python Docker bindings,
Debian's packages are just fine for that purpose:

* docker.io
* python3-docker

Also note that the user running molecule must have permissions to connect to the
Docker socket, so you probably want to add it to the ``docker`` group:

.. code-block:: bash

   sudo adduser "$USER" docker

Log in again or reboot for the group changes to take effect.

The necessity of contacting the private Docker registry from localhost requires the
hostnames of the virtual machines to be resolvable. For that purpose, molecule will
later add entries for the VMs to ``/etc/hosts`` and therefore requires sudo access.
Either configure passwordless sudo access for the user running molecule or unlock
it shortly before running molecule, which should work if your sudo configuration
has a reasonable grace period for repeated executions.


Install
-------

After a Python virtual environment incl. Ansible has been set up and activated as
described in the Install section of ``../README.rst``, the following commands need
to be executed to install additional Python packages required for running this demo:

.. code-block:: bash

   pip install -U molecule molecule-vagrant


Converge
--------

After molecule is set up, you're ready to run the ``converge`` command, which will
perform the following steps:

* Set up a Vagrant environment with 2 virtual machines (one as Swarm manager and
  one as worker), incl. the corresponding Ansible inventory
* Prepare VMs for running the roles by installing additional Debian packages on them
  (``molecule/default/prepare.yml``)
* Deploy the roles and sample todo web app
  (``molecule/default/converge.yml``)

To start the process, run molecule from the ``demo`` directory (the one this file
is in):

.. code-block:: bash

   mol converge

After this has finished, the Swarm should be set up and the URL under which the
sample app is accessible be displayed by a task of the executed Ansible playbook.

You can also list and log in to the Swarm VMs to inspect their state:

.. code-block:: bash

   mol list
   mol login --host <hostname>


Cleanup
-------

The ``destroy`` sequence will revert any changes made to your local machine:

* Log out from the private Docker registry
* Remove the self-signed CA certificate for private Docker registry
* Remove the added host mappings from ``/etc/hosts``
* Destroy the virtual machines

Start the sequence by issuing:

.. code-block:: bash

   mol destroy

Apart from the downloaded Vagrant boxes and respective libvirt volumes, your system
should now be as virgin as it was before. You can remove these as well using the
``vagrant box list`` and ``vagrant box remove`` commands.
