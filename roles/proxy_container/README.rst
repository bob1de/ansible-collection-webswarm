bob1de.webswarm.proxy_container
===============================

This role spins up a Docker container which proxies TCP or UDP traffic
from the host machine to a container attached to some Docker network not directly
accessible from the host.

That way, services without published ports can temporarily be contacted from the
host machine for executing maintenance tasks.

A handler is notified which removes all containers created by this role again after
the play has ended or when the ``flush_handlers`` meta action is executed, whichever
happens first.


Dependencies
------------

This role does not depend on other roles being included before.


Role Variables
--------------

* ``proxy_network``:
  Docker network to which the service to proxy connections to is attached.
  (REQUIRED)
* ``proxy_host``:
  Host in ``proxy_network`` to connect to.
  (REQUIRED)
* ``proxy_port``:
  Port on ``proxy_host`` to connect to.
  (REQUIRED)
* ``proxy_protocol``:
  One of ``"tcp"`` and ``"udp"``.
  (default: ``"tcp"``)
* ``listen_address``:
  Address to bind to on the host.
  (default: ``"127.0.0.1"``)
* ``listen_port``:
  Port to bind to on the host.
  (default: ``proxy_port``)


Usage
-----

Things to keep in mind when using this role:

* Set ``force_handlers: true`` on your plays (or ``force-handlers=True`` in
  ``ansible.cfg``) to ensure handlers performing cleanup   tasks are executed even
  if a task of the play fails.
