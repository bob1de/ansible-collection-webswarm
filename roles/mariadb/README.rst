bob1de.webswarm.mariadb
=======================

This role sets up MariaDB on the Swarm.


Requirements
------------

The following Debian packages need to be installed on the Swarm leader host, install
them on all managers to be safe:

* mariadb-client
* python3-pymysql


Dependencies
------------

These roles *must be included/imported before* this role:

* ``bob1de.webswarm.swarm``

In addition, this role itself includes the following roles, so make sure their
requirements are met as well:

* ``bob1de.webswarm.proxy_container``


Groups
------

This role expects hosts to be mapped to the following groups in your inventory:

* ``mariadb``:
  The host in this group will run MariaDB.
  Since Galera or similar technologies are not supported at the moment, exactly one
  host must be in this group.


Role Variables
--------------

All configuration variables are documented in ``defaults/main.yml``.
Mandatory settings are marked with the ``REQUIRED`` keyword.


Usage
-----

The Docker stack created by this role is named ``mariadb``.

The role creates an encrypted Docker overlay network named ``mariadb`` to which you
can attach your own applications needing to access the database, which is available
under the hostname ``db.mariadb`` on the MariaDB default port ``3306``..
