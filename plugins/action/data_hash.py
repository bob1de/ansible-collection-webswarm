# Copyright: (c) 2021, Robert Schindler <dev@bob1.de>
# GNU General Public License v3.0+ (see COPYING or https://www.gnu.org/licenses/gpl-3.0.txt)

import binascii
import hashlib
import json
import uuid

from ansible.plugins.action import ActionBase


_NONE_TYPE = type(None)


def _deterministic_serialize(data):
    """Deterministically serialize ``data`` into bytes."""
    return json.dumps(_make_deterministic(data), separators=(",", ":")).encode("ascii")


def _generate_salt():
    """Return 16 bytes long random salt of type bytes."""
    return uuid.uuid4().bytes


def _make_deterministic(obj):
    if isinstance(obj, (bool, float, int, str, _NONE_TYPE)):
        return obj
    if isinstance(obj, (list, tuple)):
        return [_make_deterministic(item) for item in obj]
    if isinstance(obj, dict):
        return sorted(
            [_make_deterministic(key), _make_deterministic(value)]
            for key, value in obj.items()
        )
    raise TypeError(
        "Can only deterministically serialize JSON-encodable types, not %s %r"
        % (type(obj).__name__, obj)
    )


def _salted_hash(data_bytes, salt_bytes):
    """Return 32 bytes long SHA256 hash of salt + data."""
    hasher = hashlib.sha256()
    hasher.update(salt_bytes)
    hasher.update(data_bytes)
    return hasher.digest()


class ActionModule(ActionBase):
    TRANSFERS_FILES = False
    _VALID_ARGS = frozenset(("data", "previous_hash", "force_new_salt"))

    def run(self, tmp=None, task_vars=None):
        result = {
            "changed": False,
            "failed": False,
            "skipped": False,
            **super(ActionModule, self).run(tmp, task_vars),
        }

        data = self._task.args["data"]
        try:
            serialized = _deterministic_serialize(data)
        except TypeError as err:
            return {"failed": True, "msg": str(err), **result}

        old_hash_hex = self._task.args.get("previous_hash")
        old_hash = None
        old_salt = None
        data_changed = True
        if isinstance(old_hash_hex, str) and len(old_hash_hex) == 96:
            try:
                old_hash_bytes = binascii.a2b_hex(old_hash_hex.encode("ascii"))
                old_salt, old_hash = old_hash_bytes[:16], old_hash_bytes[16:]
            except ValueError:
                pass
            else:
                if old_hash == _salted_hash(serialized, old_salt):
                    data_changed = False

        if data_changed or self._task.args.get("force_new_hash"):
            new_salt = _generate_salt()
            new_hash = _salted_hash(serialized, new_salt)
        else:
            new_salt = old_salt
            new_hash = old_hash

        return {
            "hash": binascii.b2a_hex(new_salt + new_hash).decode("ascii"),
            "data_changed": data_changed,
            **result
        }
