bob1de.webswarm.registry
========================

This role sets up a private Docker registry on the Swarm


Dependencies
------------

These roles *must be included/imported before* this role:

* ``bob1de.webswarm.swarm``
* ``bob1de.webswarm.traefik``

In addition, this role itself includes the following roles, so make sure their
requirements are met as well:

* ``bob1de.webswarm.self_signed`` (if ``registry_self_signed`` is set to ``true``)


Groups
------

This role expects hosts to be mapped to the following groups in your inventory:

* ``registry``:
  Each host in this group will run a load-balanced instance of the private Docker
  registry.
  At least one host needs to be in this group.
  Note that with the default configuration of local data storage, only a single
  instance can be run. See ``registry_data_*`` settings if you want to run multiple
  instances backed by shared network storage such as NFS.

* ``registry_login``:
  Hosts in this group will be logged in to the private Docker registry.
  If using a self-signed TLS certificate for the registry (i.e. ``registry_self_signed:
  true``), the certificate will also be deployed as CA to these hosts.
  All Swarm members are automatically logged in to the registry anyway and *may not*
  be included in this group.


Role Variables
--------------

All configuration variables are documented in ``defaults/main.yml``.
Mandatory settings are marked with the ``REQUIRED`` keyword.

This role defines some helper variables for use in your own play:

* ``registry_spec``:
  A string of the form ``"{{ registry_fqdn }}:{{ registry_port }}"`` indicating
  where the private Docker registry is reachable via HTTPS.
  This can be used to construct names for pushing and referencing of self-built
  Docker images.


Usage
-----

The Docker stack created by this role is named ``registry``.

Things to keep in mind when using this role:

* If using a self-signed TLS certificate for Docker registry
  (i.e. ``registry_self_signed: true``), make sure hosts in the ``registry_login``
  group, if any, have a working ``become`` setup (normally passwordless sudo),
  so that the generated CA certificate can be copied to its proper location under
  ``/etc/docker`` on these machines as well.
  You don't need to set ``ansible_become: true`` globally for these hosts, the
  respective tasks are annotated with ``become: true`` already.
