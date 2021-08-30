bob1de.webswarm.elastic
=======================

This role sets up Elasticsearch, Kibana and Filebeat on the Swarm for collection of
log messages.


Dependencies
------------

These roles *must be included/imported before* this role:

* ``bob1de.webswarm.swarm``
* ``bob1de.webswarm.traefik``

In addition, this role itself includes the following roles, so make sure their
requirements are met as well:

* ``bob1de.webswarm.proxy_container``
* ``bob1de.webswarm.self_signed``


Groups
------

This role expects hosts to be mapped to the following groups in your inventory:

* ``elasticsearch``:
  Hosts in this group will run Elasticsearch, together forming a cluster.
  At least one host needs to be in this group.

* ``elasticsearch_master``:
  These nodes are listed as ``cluster.initial_master_nodes`` in ``elasticsearch.yml``.
  All hosts in this group must also be members of group ``elasticsearch``.
  At least one host needs to be in this group.

* ``kibana``:
  Hosts in this group will form a load-balanced Kibana cluster.
  At least one host needs to be in this group.


Role Variables
--------------

All configuration variables are documented in ``defaults/main.yml``.
Mandatory settings are marked with the ``REQUIRED`` keyword.


Usage
-----

The Docker stack created by this role is named ``elastic``.

The role creates an encrypted Docker overlay network named ``elastic`` to which you
can attach your own applications needing to access the Elastic services.

These services are available on the network:

* ``http://elasticsearch.elastic:9200``
* ``http://kibana.elastic:5601``
