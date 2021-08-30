bob1de.webswarm.generate_secrets
================================

Generate random secrets for the different services deployed by this collection's roles.

Secrets are stored as variables in a YAML file.
Then, you can include that file in your play via ``vars_files`` or by using the
``include_vars`` action before including the role or copy its contents to one of
your own variable files.

The tasks will run only once and are delegated to localhost automatically.


Dependencies
------------

This role does not depend on other roles being included before.


Role Variables
==============

* ``secrets_file``:
  Path of YAML variables file to store secrets in (either absolute or relative to
  working directory).
  (default: ``"{{ playbook_dir }}/vars/webswarm-secrets.yml"``)
* ``replace_secrets_file``:
  In case the file exists already, whether to replace it with a new set of random
  secrets.
  The existing file will be backed up before getting replaced.
  (default: ``false``)
* ``admin_usernames``:
  List of usernames for which an entry should be created in the ``admin_users``
  list with random password.
  (default: ``["admin"]``)
