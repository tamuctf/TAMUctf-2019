import json

class GradeRequest:
    def __init__(self, user=None, assignment=None, code=None):
        self.user = user
        self.assignment = assignment
        self.code = code

    def asdict(self):
        return {
            'user': self.user,
            'assignment': self.assignment,
            'code': self.code
        }

    def marshal(self):
        return json.dumps(self.asdict())

    @classmethod
    def unmarshal(cls, data):
        map = json.loads(data)
        if not isinstance(map, dict):
            raise ValueError(f'decoded data is not a dict: {type(map)}')

        result = cls(
            user = map.pop('user'),
            assignment = map.pop('assignment'),
            code = map.pop('code')
        )
        if map:
            raise ValueError(f"data contains extra keys: {list(map.keys())}")
        return result

class ProcessUpdate:
    def __init__(self, stdout=None, stderr=None, ret=None):
        self.stdout = stdout
        self.stderr = stderr
        self.ret = ret

    def asdict(self):
        return {
            'stdout': self.stdout,
            'stderr': self.stderr,
            'ret': self.ret
        }

    def marshal(self):
        return json.dumps(self.asdict())

    @classmethod
    def unmarshal(cls, data):
        map = json.loads(data)
        if not isinstance(map, dict):
            raise ValueError(f'decoded data is not a dict: {type(map)}')

        result = cls(
            stdout = map.pop('stdout'),
            stderr = map.pop('stderr'),
            ret = map.pop('ret')
        )
        if map:
            raise ValueError(f"data contains extra keys: {list(map.keys())}")
        return result
