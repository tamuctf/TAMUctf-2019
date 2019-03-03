from assignment_one import top_word

def top_word_solution(sentence):
    words = sentence.split()

    counts = {}
    for word in words:
        if word in counts:
            counts[word] += 1
        else:
            counts[word] = 1

    top, hi = None, 0
    for word in words:
        count = counts.pop(word)
        if count > hi:
            hi = count
            top = word

    return top

def test_empty():
    assert top_word("") is None

def test_single():
    assert top_word("word") == "word"

def test_double():
    assert top_word("foo bar foo") == "foo"

nicolas = """
Nicolas Kim Coppola (born January 7, 1964),[3] known professionally as Nicolas Cage, is an American actor, director and
producer. During his early career, Cage starred in a variety of films such as Valley Girl (1983), Racing with the Moon
(1984), Birdy (1984), Peggy Sue Got Married (1986), Raising Arizona (1987), Moonstruck (1987), Vampire's Kiss (1989),
Wild at Heart (1990), Fire Birds (1990), Honeymoon in Vegas (1992), and Red Rock West (1993).
"""

def test_nicolas():
    correct = top_word_solution(nicolas)
    assert top_word(nicolas) == correct

rfc2549 = """
Avian carriers normally bypass bridges and tunnels but will seek out worm hole tunnels.  When carrying web traffic, the
carriers may digest the spiders, leaving behind a more compact representation.  The carriers may be confused by mirrors.
"""

def test_rfc2549():
    correct = top_word_solution(nicolas)
    assert top_word(rfc2549) == correct
