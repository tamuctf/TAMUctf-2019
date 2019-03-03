"""
dlog module - Shanks and Pohlig-Hellman algorithms for DLOG

dlog modules provides

    * Shank's algorithm.
    * Pohlig-Hellman algorithm.

    * Fast functions for inserting/searching for elements in sorted lists

and requires ent and bigrange.
"""

import ent as ent
import math
from bigrange import bigrange


def shanks(a, b, bscount, gscount):
    """
    Performs Shank's algorithm to solve the discrete logarithm.

    Input:
        a           'Base' of the discrete logarithm
        b           'Argument' of the discrete logarithm
        bscount     Number of baby steps to precompute
        gscount     Number of giant steps to perform

    Output:
        An integer l such that a*l=b (the smallest in the range
        0...bscount*gscount-1).

    Remarks:
        With a multiplicative notation, l is l=log_a b.
        Nevertheless, the algorithm uses additive notation and therefore
        a and b must implement the operations +, -, * (multiplication
        with an int), as well as unary - and +.
    """
    # precompute the baby steps
    step=0*a
    babysteps=[(step, 0)]
    for i in bigrange(1, bscount):
        step=step+a
        # insert a tuple (step, i) in babysteps.
        # do it in such a way that the list ends up sorted
        el=(step, i)
        babysteps.insert(find_insertion_index(el, babysteps), el)

    # go on with the giant steps
    lookfor=b; ba=bscount*a

    for j in bigrange(0, gscount):
        # update lookfor such that is always equal to b-j*bscount*a
        if (j>0):
            lookfor-=ba

        # search in the baby steps
        bstep=find_tuple_by_1st_item(lookfor, babysteps)

        if bstep!=None: #found!
            return j*bscount+bstep[1]

    return None




def autoshanks(a, b, count):
    """
    Computes Shank's algorithm with giant steps & baby steps count
    equal to ceil(sqrt(count)). These are the best values in terms
    of complexity.

    Input:
        a           'Base' of the discrete logarithm
        b           'Argument' of the discrete logarithm
        count       Number of elements in the group (of a and b)

    Output:
        An integer l such that a*l=b (the smallest in the range
        0...count-1).

    Remarks:
        With a multiplicative notation, l is l=log_a b.
        Nevertheless, the algorithm uses additive notation and therefore
        a and b must implement the operations +, -, * (multiplication
        with an int), as well as unary - and +.
    """
    n=long(math.ceil(math.sqrt(float(count))));
    return shanks(a, b, n, n)






def pohlighellman(a, b, count, simpleDlog=autoshanks):
    """
    Performs Pohlig-Hellman algorithm to solve the discrete logarithm.
    Requires the factorization of count to be computed, and relies on
    ent.factor(...) to achieve that.
    The routine uses another algorithm to solve simpler DLOG problems,
    shanks as default.

    Input:
        a           'Base' of the discrete logarithm
        b           'Argument' of the discrete logarithm
        count       Number of elements in the group (of a and b)
        simpleDlog  A callable that computes DLOG in a smaller group.
                    The signature and return value must be the same
                    as autoshanks; actually this argument defaults to
                    autoshanks(...).

    Output:
        An integer l such that a*l=b (the smallest in the range
        0...count-1).

    Remarks:
        Uses chineseremainder(...) to combine the partial results.

    """
    # first of all let's factor count
    factorization=ent.factor(count)
    l=range(0,len(factorization))
    li=0

    for (p, e) in factorization:
        g=(count//p)*a
        if g.isIdentity(): # it may happen that g=O!
            # ..still to check, but a couple of tests showed that
            # this works.
            l[li]=((p**e)-1, p**e)
            li+=1
            continue
        A=0*a # 0 in the G group
        bs=range(0, e)

        for i in bigrange(0, e):
            if i>0:
                A=A+bs[i-1]*(p**(i-1))*a
            B=(b-A)*(count//(p**(i+1)))
            bs[i]=simpleDlog(g, B, p)
        
        # compute l_k
        l[li]=0
        for i in bigrange(e, 0, -1):
            l[li]*=p
            l[li]+=bs[i-1]

        # prepare l to be input for chineseremainder
        l[li]=(l[li], p**e)

        li+=1

    return chineseremainder(l)



def chineseremainder(eqs):
    """
    Solves simultaneous congruences using Gauss's algorithm.
    The routine doesn't check if all the n are pairwise coprime.

    Input:
        eqs         A list of tuples (a, n)

    Output:
        The solution x as in CRT, congruent to a mod n for each (a, n).
    """
    N=1
    x=0
    for (a, n) in eqs:
        N*=n
    for (a, n) in eqs:
        l=N/n
        m=ent.inversemod(l, n)
        x+=a*l*m

    return x%N



###########################################################
# Fast auxiliary routines for sorted lists manipulation.
###########################################################



def find_insertion_index(el, list):
    """
    Assuming list is a sorted list, looks for the right index where
    el should be inserted. The search is exaustive for lists with less
    than 6 elements, otherwise it's done with a bisection algorithm.

    Input:
        el          The element to look the insertion index for
        list        An already sorted list (eventually [])

    Output:
        An integer in the range 0...len(list)

    Remarks:
        Used by shanks, autoshanks, pohlighellman.
    """
    if len(list)==0: return 0
    if len(list)==1:
        if el<list[0]: return 0
        return 1
    if len(list)<=5:
        candidate=len(list)
        while el<list[candidate-1] and candidate>0:
            candidate-=1
        return candidate
    # binary search
    a=0; b=len(list); m=b//2
    while True:
        # compare with the middle element
        if el<list[m]:
            b=m
        else:
            a=m+1
        m=(a+b)//2

        if a==b:
            if a==len(list) or el<list[a]:
                return a
            return a+1
        elif a+1==b:
            if el<list[a]: return a
            if b==len(list) or el<list[b]:
                return b
            return b+1




def find_item(item, list):
    """
    Assuming list is a sorted list of items, looks with a bisection
    algorithm for the index of that item. The search is exaustive for
    lists with less than 6 elements. If not found, returns None.

    Input:
        item        The item to look for
        list        A sorted list

    Output:
        An integer in the range 0...len(list)-1 or None.
    """
    if len(list)==0: return None
    if len(list)<=5:
        for i in bigrange(0, len(list)):
            if list[i]==item:
                return i
        return None
    # binary search
    a=0; b=len(list); m=b//2
    while True:
        # compare with the middle element
        if list[m]==item:
            return m

        if item<list[m]:
            b=m
        else:
            a=m+1
        m=(a+b)//2

        if a==b:
            if a==len(list):
                return None

            if item==list[a]:
                return a

            return None
        elif a+1==b:
            if item==list[a]:
                return a

            if b==len(list):
                return None

            if item==list[b]:
                return b

            return None




def find_tuple_by_1st_item(stItem, list):
    """
    Assuming list is a sorted list of tuples, looks with a bisection
    algorithm for the a tuple with stItem at index 0, and
    returns the whole tuple. The search is exhaustive for lists with 
    less than 6 elements. If not found, returns None.

    Input:
        stItem      The first item of the tuple we're looking for
        list        A sorted list of tuples

    Output:
        A tuple with stItem as the first element or None.

    Remarks:
        Used by shanks, autoshanks, pohlighellman.
    """
    if len(list)==0: return None
    if len(list)<=5:
        for i in bigrange(0, len(list)):
            if list[i][0]==stItem:
                return list[i]
        return None
    # binary search
    a=0; b=len(list); m=b//2
    while True:
        # compare with the middle element
        if list[m][0]==stItem:
            return list[m]

        if stItem<list[m][0]:
            b=m
        else:
            a=m+1
        m=(a+b)//2

        if a==b:
            if a==len(list):
                return None

            if stItem==list[a][0]:
                return list[a]

            return None
        elif a+1==b:
            if stItem==list[a][0]:
                return list[a]

            if b==len(list):
                return None

            if stItem==list[b][0]:
                return list[b]

            return None
