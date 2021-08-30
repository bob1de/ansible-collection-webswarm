bob1de.webswarm.traefik
=======================

This role sets up Traefik as TLS-enabled front-end web server and load-balancing
reverse proxy to web services on the Swarm.


Dependencies
------------

These roles *must be included/imported before* this role:

* ``bob1de.webswarm.swarm``


Groups
------

This role expects hosts to be mapped to the following groups in your inventory:

* ``traefik``:
  Hosts in this group will run an instance of the Traefik reverse proxy and hence
  publish the configured entrypoint ports to the public.
  At least one host needs to be in this group.


Role Variables
--------------

All configuration variables are documented in ``defaults/main.yml``.
Mandatory settings are marked with the ``REQUIRED`` keyword.


Custom Files
------------

These directories (relative to your playbook directory) are searched for user-provided
files:

* ``traefik-certs``:
  In this directory, TLS certificates and keys for Traefik can be placed in PEM format.
  A pair of key and certificate files must have the same base name, ending in ``.key``
  and ``.crt``, respectively.
  Traefik will analyze the ``commonName`` and ``subjectAltName`` fields of all
  certificates to find the appropriate one for serving an incoming request.
  If present, ``default.key``/``default.crt`` will be used as fallback for requests
  with a hostname for which no matching certificate was found.
  In case you don't provide a default certificate, Traefik automatically generates
  a self-signed one for serving such requests, which you probably should avoid.

* ``traefik-dynconfig``:
  In this directory, ``.yml``, ``.yaml`` and ``.toml`` files may be placed which
  augment Traefik's dynamic configuration.
  These files are rendered by Ansible's templating engine, so you can build
  configuration files with dynamic contents.
  Since YAML is a superset of JSON, Ansible's ``to_json`` templating filter can be
  used to safely insert values in such YAML configuration files without having to
  care about escaping.


Usage
-----

The Docker stack created by this role is named ``traefik``.

The role creates an encrypted Docker overlay network named ``traefik-edge``.
Services that should be accessible via Traefik need to be attached to this
``traefik-edge`` Docker network and annotated with a set of Docker labels.
When using Docker Compose, make sure to specify the ``labels`` key under ``deploy``
instead of under the service name key directly, because otherwise they are set on
the individual containers and not on the service as a whole.

A sample labels set might look like this:

.. code-block:: yaml

   # Enable this service for Traefik ingress routing
   - traefik.enable=true
   # Requests need to come in at the websecure entrypoint (defined by
   # traefik_entrypoints variable)
   - traefik.http.routers.myapp.entrypoints=websecure
   # App will be available under my.domain.org/my-app
   - traefik.http.routers.myapp.rule=Host(`my.domain.org`) && Path(`/my-app`)
   # Route requests to port 80 on the service containers
   - traefik.http.services.myapp.loadbalancer.server.port=80

A lot more than this can be configured, particularly Traefik middlewares are a
common need for some applications. See the respective Traefik documentation pages
for all options.
