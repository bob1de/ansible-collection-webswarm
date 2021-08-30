bob1de.webswarm.swarm
=====================

This role sets up a Docker Swarm suitable for running arbitrary web (and other)
applications.


Requirements
------------

The role is designed for and was tested with Debian 10 and 11 systems on both
controller and Swarm hosts.
The hosts to be included in the Swarm need a working Docker installation (incl. Docker
CLI), version 20.10 or later.
Docker 18.09, which is provided by Debian 10's ``docker.io`` package was tested and
generally works too, but was found to be quite unreliable in that Swarm services
eventually crash and don't recover properly.

Between the Swarm hosts, a number of ports has to
be open for Swarm-related communication (see `Docker docs
<https://docs.docker.com/engine/swarm/swarm-tutorial/#open-protocols-and-ports-between-the-hosts>`_
for an up-to-date list and more details):

* 2377 (TCP) for cluster management
* 7946 (TCP and UDP) for communication among nodes
* 4789 (UDP) for overlay network traffic
* IP protocol 50 (ESP) for encrypted overlay network traffic

The following Debian packages need to be installed on all Swarm hosts:

* docker.io (or an alternative way of installing Docker)
* docker-compose
* python3-docker
* python3-jsondiff
* python3-six
* python3-yaml


Dependencies
------------

This role does not depend on other roles being included before.

In addition, this role itself includes the following roles, so make sure their
requirements are met as well:

* ``bob1de.webswarm.swarm_sanity``


Groups
------

This role expects hosts to be mapped to the following groups in your inventory:

* ``swarm_manager``:
  Hosts in this group will be manager nodes in the created Docker Swarm.
  At least one host needs to be in this group.
  If a host is removed from the group after the Swarm was deployed, you have to
  remove it manually from the Swarm using ``docker node rm`` on a manager.

* ``swarm_worker``:
  Hosts in this group will be manager nodes in the created Docker Swarm.
  If a host is removed from the group after the Swarm was deployed, you have to
  remove it manually from the Swarm using ``docker node rm`` on a manager.


Role Variables
--------------

All configuration variables are documented in ``defaults/main.yml``.
Mandatory settings are marked with the ``REQUIRED`` keyword.

This role defines some helper variables for use in your own play:

* ``swarm_managers``, ``swarm_workers`` and ``swarm_members``:
  Lists of inventory hostnames of Swarm managers, Swarm workers and the concatenation
  of both.
  Note that these variables don't necessarily reflect the real physical state of
  your Swarm setup, especially not before the role ran and set it up for you, they
  just reflect the group mappings configured in your inventory.

* ``is_swarm_manager``, ``is_swarm_worker`` and ``is_swarm_member``:
  Boolean variants of the previous, primarily for use in ``when`` clauses.

* ``swarm_leader`` and ``is_swarm_leader``:
  Inventory hostname of the first host in ``swarm_manager`` group which also is in
  ``ansible_play_hosts_all`` (or ``none`` in case the play doesn't target any manager).
  The boolean variant can be used in ``when`` clauses to easily target the first
  available manager for running Swarm management tasks, such as stack deployments
  using the ``community.docker.docker_stack`` module.


Usage
-----

Things to keep in mind when using this role:

* Set ``ansible_become: true`` for hosts in your inventory which will be part of
  the Swarm so that all tasks are executed as root on these hosts.
  Alternatively, you can set ``become: true`` on the play level (or task level
  via ``apply: {become: true}`` when using ``include_role``), but then even tasks
  delegated to localhost which write local files will be running as root, leading
  to potentially unwanted file ownerships in your playbook directory.
