bob1de.webswarm.self_signed
===========================

Generate a self-signed X509 certificate (incl. corresponding private key and
certificate signing request).

This role only provides a very limited subset of options for creating the certificate
(see variables below). For more complex requirements, use the ``community.crypto.*``
modules directly.


Requirements
------------

This role uses the ``community.crypto.x509_*`` and ``community.crypto.openssl_*``
modules, hence the ``community.crypto`` collection is pulled as a dependency of
``bob1de.webswarm``.

You'll also need the ``cryptography`` Python package (``python3-cryptography``
on Debian).


Dependencies
------------

This role does not depend on other roles being included before.


Role Variables
--------------

A single variable ``ss_config`` of type dict needs to be passed with the following
keys:

* ``cert_path``:
  Path of generated PEM certificate.
  (REQUIRED)
* ``csr_path``:
  Path of generated PEM certificate signing request.
  (REQUIRED)
* ``key_path``:
  Path of generated PEM private key.
  (REQUIRED)
* ``subject``:
  Mapping of certificate subject fields such as ``commonName``, ``organizationName``
  or ``emailAddress``; see ``community.crypto.openssl_csr`` documentation for more.
  (default: ``{}``)
* ``subject_alt_name``:
  List of SAN entries such as ``["DNS:some.fqdn"]``.
  (default: ``[]``)
* ``min_validity``:
  An existing certificate will be renewed if it expires earlier than this.
  The format is compatible to the ``community.crypto.x509_certificate`` module.
  (default: ``"+90d"``)
* ``validity``:
  Expiration time of the newly issued certificate.
  The format is compatible to the ``community.crypto.x509_certificate`` module.
  (default: ``"+365d"``)
* ``key_type``:
  Private key type.
  (default: ``type`` default of ``community.crypto.openssl_privatekey``)
* ``key_size``:
  Private key size.
  (default: ``size`` default of ``community.crypto.openssl_privatekey``)
* ``digest``:
  Digest algorithm for signing the issued certificate.
  (default: ``selfsigned_digest`` default of ``community.crypto.x509_certificate``)
* ``backup``:
  Whether to back up any existing file going to be overwritten.
  (default: ``true``)


Usage
-----

NOTE:
If you want to generate just a single certificate on localhost (instead of separate
certificates on the inventory hosts), include this role in your play like so:

.. code-block:: yaml

   - name: Generate self-signed certificate on localhost
     include_role:
       name: bob1de.webswarm.self_signed
       apply:
         run_once: true
         delegate_to: localhost
     vars:
       ss_config:
         ...
