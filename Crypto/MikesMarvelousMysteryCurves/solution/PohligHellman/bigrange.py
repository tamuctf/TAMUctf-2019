"""
bigrange - big ranges. Really.

Only one cool iterator in Python that supports big integers.
"""
def bigrange(a,b,step=1):
    """
    A replacement for the xrange iterator; the builtin class
    doesn't handle arbitrarely large numbers.

    Input:
        a           First numeric output
        b           Lower (or upper) bound, never yield
        step        Defaults to 1
    """
    i=a
    while cmp(i,b)==cmp(0,step):
        yield i
        i+=step