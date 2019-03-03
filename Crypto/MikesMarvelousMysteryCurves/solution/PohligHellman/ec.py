"""
ec module - Elliptic curves over finite fields class implementation

ec provides two base classes, EC and ECPt, that can be used to perform operations on the
group of rational points of an elliptic curve over a finite field.
The classes provide also some "introspective" methods to compute orders of points,
cardinality of the rational points group.

ec relies on ent, dlog and bigrange.
"""

import os
import struct
import math

import ent as ent
import dlog

from bigrange import bigrange
from sets import Set

class EC(object):
    """
    This class represents an elliptic curve of equation y^2==x^3+ax^2+bx+c over a
    finite field F_p.
    """

    def __init__(self, a, b, c, p):
        """
        Defines a new elliptic curve. The routine performs pseudoprimality test on
        p and checks whether the curve is singular or not.

        Input:
            a           Coefficient. Will be normalized modulo p
            b           Same as a.
            c           Same as b.
            p           Prime number. Must be !=2,3. Defines the finite field to which
                        the rational points belong.

        Remarks:
            Might raise an AssertionError.
            Check before calling EC(...) that the parameters are valid by testing
            whether EC.computeDiscriminant(a, b, c, p)!=0 .
            
            Pseudoprimality test is Fermat's, just for safety... You should be sure
            of having generated a true prime before trying to build a finite field.
            Checking primality is not the purpose of the class.

        """
        super(EC, self).__init__()
        # ensure char != 2,3
        assert p!=2 and p!=3, "char F_p must be != 2, 3."
        # ensure p is a valid prime
        assert p>3 and ent.is_pseudoprime(p), "p must be a valid (pseudo)prime."
        # reduce args mod p
        a%=p; b%=p; c%=p
        # compute discriminant
        self._discriminant=EC.computeDiscriminant(a,b,c,p);
        assert self._discriminant!=0, "EC is singular."
        #save everything
        self._a=a; self._b=b; self._c=c; self._p=p
        self._cardinality=-1






    def __repr__(self):
        return "EC({},{},{},{})".format(self._a, self._b, self._c, self._p)

    def __str__(self):
        retval="y^2==x^3"
        if self._a>0:
            retval+="+"+str(self._a)+"x^2"
        elif self._a<0:
            retval+=str(self._a)+"x^2"
        if self._b>0:
            retval+="+"+str(self._b)+"x"
        elif self._b<0:
            retval+=str(self._a)+"x"
        if self._c>0:
            retval+="+"+str(self._c)
        elif self._c<0:
            retval+=str(self._c)
        retval+=" over F_"+str(self._p)

        return retval





    def __eq__(self, other):
        """
        Two elliptic curves are equals if they're defined by the same equation over the
        same finite field. Checks whether a, b, c, p are equal.
        """
        if not isinstance(other, EC): return NotImplemented
        return self._a==other._a and self._b==other._b and self._c==other._c and self._p==other._p

    def __ne__(self, other):
        if not isinstance(other, EC): return NotImplemented
        return self._a!=other._a or self._b!=other._b or self._c!=other._c or self._p!=other._p




    def enumerateAllPoints(self):
        """
        Picks points randomly, then computes the generated group and stores it; the
        routine exits when has computed exactly .cardinality() distinct points.

        Output:
            A list of ECPt objects. The identity is included.

        Remarks:
            The routine performs a call to cardinality(), therefore might be computationally
            expensive.
            Besides, enumerateAllPoints must store all the points computed, so it's also
            expensive in terms of memory.
        """
        extractedPts=Set()
        while True:
            P=self.pickPoint()
            coords=(P._x, P._y)

            if coords in extractedPts:
                continue

            extractedPts.add(coords)

            # add all the multiples of P
            Q=P
            while True:
                Q+=P

                if Q.isIdentity(): break

                coords=(Q._x, Q._y)
                extractedPts.add(coords)

            if len(extractedPts)==self.cardinality()-1:
                retval=range(0, self.cardinality()) # prealloc
                #convert to a list of ECPts
                i=1
                for (x, y) in extractedPts:
                    retval[i]=ECPt(self, x, y)
                    i+=1
                
                retval[0]=ECPt.identity()

                return retval




    def pickGenerator(self):
        """
        Picks points over the elliptic curve randomly and computes their order, until
        finds one with order equal to self.cardinality(); that is, a generator of the 
        group of rational points.

        Output:
            An ECPt object over the elliptic curve defined by self. If no point generates
            the whole group, None is returned.

        Remarks:
            The routine performs a call to cardinality(). See cardinality for details;
            computing cardinality might require a lot of time.
            To make the routine significantly faster on some curves, for each point that
            fails the test, the whole group it generates is stored in memory.
            Because of this pickGenerator(...) can be memory expensive too.
        """
        extractedPts=Set()
        while True:
            P=self.pickPoint()
            coords=(P._x, P._y)

            if coords in extractedPts:
                continue

            extractedPts.add(coords)

            order=P.computeOrder()

            if order==self.cardinality():
                return P

            # add all the mutiples of P
            Q=P
            for i in bigrange(2,order):
                Q+=P
                coords=(Q._x, Q._y)
                extractedPts.add(coords)

            if len(extractedPts)==self.cardinality()-1:
                return None
            

    def pickPoint(self):
        """
        Randomly picks a point on the elliptic curve defined by self.

        Output:
            An ECPt object over "self".

        Remarks:
            The routine simply chooses randomly a x in 0...p-1, then  finds a suitable
            y for it using ent.sqrtmod(...).
        """
        while True:
            # choose a random x!=0
            x=1+(EC._randomLong()%(self._p-1))
            # compute fx
            fx=self.computeFx(x)
            # make sure sqrtmod won't fail
            if ent.legendre(fx, self._p)==1:
                return ECPt(self, x, ent.sqrtmod(fx, self._p))

    def cardinality(self):
        """
        This routine computes the exact cardinality of the group of rational points over
        the elliptic curve. The result is cached, the computation is done only once.

        Output:
            The cardinality.

        Remarks:
            The routine uses ECPt.minOrderWithConstraints(...), ECPt.orderInFactorGroup
            and ent.factor, therefore might be really expensive in terms of efficiency.
        """
        if self._cardinality>=0:
            return self._cardinality

        P=self.pickPoint()

        # try to get order
        b=self._p+1-int(math.floor(2*math.sqrt(self._p)))
        c=self._p+1+int(math.ceil(2*math.sqrt(self._p)))

        result=P.minOrderWithConstraints(b, c, 0, 1)
        if not isinstance(result, tuple):
            # we've finished!
            self._cardinality=result
            return result

        # we have the order of P
        lp=result[1]

        while True:
            # let's pick another point
            Q=self.pickPoint()
            while Q==P: Q=self.pickPoint() # different, please

            # another run of minOrder...
            result=Q.minOrderWithConstraints(b, c, 0, lp)

            if not isinstance(result, tuple):
                # we've finished
                self._cardinality=result
                return result

            lqp=result[1]

            # we need l_Q
            lq=lqp*lp
            factorization=ent.factor(lq)

            for (p, e) in factorization:
                while ((lq//p)*Q).isIdentity():
                    lq=lq//p

            # we have P, l_P, Q, l_Q, we can run another algorithm
            t=ECPt.orderInFactorGroup(P, lp, Q, lq)
            # let's check if it's ok
            if lp*t>2*int(math.floor(2*math.sqrt(p))):
                # #C_K is the only number in [b,c] divisible by lpt
                temp=math.ceil(b/float(lp*t))
                self._cardinality=int(temp)*lp*t
                return self._cardinality



    def computeFx(self, x):
        """
        Computes x^3+ax^2+bx+c in F_p using repeated modulo operations.

        Input:
            x           x.

        Output:
            A number in the range 0...p-1.

        Remarks:
            Considering the arbitrary length of Python's integers, maybe a unique big
            modulo operation may be faster. Benchmark needed.
        """
        x2=(x*x)  %self._p
        x3=(x2*x) %self._p
        fx=(x3+self._a*x2) %self._p
        fx+=(self._b*x)    %self._p
        fx+=self._c
        return fx %self._p

    def isPointOnEC(self, pt):
        """
        Checks if the equation defining the elliptic curve vanises at the given
        coordinates.

        Input:
            pt          An ECPt, or a tuple/list (x,y)/[x,y]

        Output:
            Boolean.
        """
        x=y=0
        if isinstance(pt, ECPt):
            x=pt._x; y=pt._y
        if isinstance(pt, tuple) or isinstance(pt, list):
            x=pt[0]; y=pt[1]

        if  x==0 and y==0: return True
        y2=y*y  %self._p
        return y2==self.computeFx(x)

    @staticmethod
    def computeDiscriminant(a,b,c,p):
        """
        Computes the discriminant of an elliptic curve using repeatedly the modulo operator.
    
        Input:
            a           Coefficient. Will be normalized modulo p
            b           Same as a.
            c           Same as b.
            p           Number.

        Output:
            An integer in the range 0...p-1.

        Remarks:
            In this routine it's not relevant whether p is prime or not; it just computes

                a^2 b^2 - 4b^3 - 4a^3 c + 18abc - 27c^2     (mod p)
        """
        a2=(a*a)  %p
        b2=(b*b)  %p
        c2=(c*c)  %p
        a3=(a2*a) %p
        b3=(b2*b) %p
        result=(a2*b2)      %p
        result+=(-4*b3)     %p
        result+=(-4*a3*c)   %p
        result+=(18*a*b*c)  %p
        result+=(-27*c2)    %p
        return result %p;

    @staticmethod
    def _randomLong():
        return struct.unpack("L", os.urandom(8))[0]




class ECPt(object):
    """
    This class represents a rational point on an elliptic curve.
    """

    def __init__(self, ec, x, y):
        """
        Initializes a new instance of a point over an elliptic curve.

        Input:
            ec          An instance of EC(...), the elliptic curve to which the point belongs
            x           The x coordinate of the point
            y           The y coordinate of the point

        Remarks:
            The routine checks if the point actually lies on the curve. The identity has coordinates
            0, 0, but you should use isIdentity() to check and ECPt.identity() to create it.
            If the point is not on the EC, it raises AssertionError; see EC.pickPoint() and
            EC.pickGenerator() which are safe methods to generate rational points.
        """
        super(ECPt, self).__init__()

        if ec!=None:
            x%=ec._p
            y%=ec._p
            # make sure the point is on the ec
            assert ec.isPointOnEC((x, y)), "The point doesn't lie on the EC."
            # ok
            self._EC=ec
            self._x=x; self._y=y
        else:
            self._EC=None
            self._x=0; self._y=0




    def __str__(self):
        if self.isIdentity(): return "O"
        return str([int(self._x), int(self._y)])

    def __repr__(self):
        if self.isIdentity(): return "ECPt.identity()"
        return "ECPt({},{},{})".format(repr(self._EC), self._x, self._y)






    def __eq__(self, other):
        """
        Two points are equals if they have the same coordinates and elliptic curve.
        """
        if not isinstance(other, ECPt): return NotImplemented
        if other._EC!=self._EC: return False
        return self._x==other._x and self._y==other._y

    def __ne__(self, other):
        if not isinstance(other, ECPt): return NotImplemented
        if other._EC!=self._EC: return True
        return self._x!=other._x or self._y!=other._y








    def __add__(self, other):
        """
        Performs addition between two points of the same EC using the group operation.

        Output:
            Another instance of an ECPt on the same elliptic curve.
        """
        # we can add only points on the same curve
        if not isinstance(other, ECPt): return NotImplemented

        # perform addition
        if self.isIdentity(): return other
        if other.isIdentity(): return self

        if self._EC!=other._EC: return NotImplemented

        if self._x==other._x and (self._y+other._y)%self._EC._p==0: return ECPt.identity()

        # define all the necessary coefficient
        if self==other:
            l=(3*self._x*self._x) %self._EC._p
            l+=(2*self._EC._a*self._x) %self._EC._p
            l+=self._EC._b
            # find 1/2y
            l*=ent.inversemod(2*self._y, self._EC._p)
        else:
            l=(other._y-self._y) %self._EC._p
            # find 1/(other._x-self._x)
            l*=ent.inversemod(other._x-self._x, self._EC._p)

        # clamp in 0...p-1 and square
        l%=self._EC._p
        l2=(l*l) %self._EC._p

        # define nu
        n=(self._y-l*self._x) %self._EC._p

        #ready to build the added point
        newX=(l2-self._EC._a-self._x-other._x) %self._EC._p
        newY=(-l*newX-n) %self._EC._p

        #finished
        return ECPt(self._EC, newX, newY)

    def __sub__(self, other):
        # we can add only points on the same curve
        if not isinstance(other, ECPt): return NotImplemented

        if self.isIdentity(): return -other
        if other.isIdentity(): return self

        if self._EC!=other._EC: return NotImplemented

        # negate other and add
        other=-other
        return self+other

    def __neg__(self):
        """
        Gives the opposite of the point (by negating the y-coordinate).
        """
        if self.isIdentity(): return self
        return ECPt(self._EC, self._x, -self._y)

    def __pos__(self):
        return self

    def __mul__(self, m):
        """
        Computes the m-th multiple of 'self' using double-and-add algorithm.
        """
        if not isinstance(m, int) and not isinstance(m, long): return NotImplemented

        if m==0: return ECPt.identity()

        # use double and add
        negateAfter=False
        if m<0:
            negateAfter=True
            m=-m

        if m==1:
            P=self
        elif m==2:
            P=self+self
        else:
            # compute first the index of MSB
            msb=0; mm=m
            while mm>0:
                msb+=1
                mm=mm>>1

            P=ECPt.identity()
            for i in bigrange(msb, 0, -1):
                # double and add
                P=P+P
                if (m & (1<<(i-1))): P=P+self

        if negateAfter: P=-P

        return P

    def __rmul__(self, m):
        if not isinstance(m, int) and not isinstance(m, long): return NotImplemented
        return self*m

    def __lt__(self, other):
        """
        The order on the set of rational points is given by the lexicographic order of the 
        couples (x, y). (x, y)<(x', y') if x<x' or if x=x' and y<y'.
        """     
        if not isinstance(other, ECPt): return NotImplemented
        if self._x<other._x: return True
        if self._x==other._x: return self._y<other._y
        return False

    def __le__(self, other):
        if not isinstance(other, ECPt): return NotImplemented
        if self._x<other._x: return True
        if self._x==other._x: return self._y<=other._y
        return False

    def __gt__(self, other):
        if not isinstance(other, ECPt): return NotImplemented
        if self._x>other._x: return True
        if self._x==other._y: return self._y>other._y
        return False

    def __ge__(self, other):
        if not isinstance(other, ECPt): return NotImplemented
        if self._x>other._x: return True
        if self._x==other._x: return self._y>=other._y
        return False



    @staticmethod
    def orderInFactorGroup(P, lp, Q, lq):
        """
        This routine computes the order of Q's equivalence class in the quotient group C_K/<P>.

        Input:
            P       A rational point
            lp      The order of P
            Q       Another rational point
            lq      The order of Q

        Output:
            Integer m such that m*[Q]=[O] in C_K/<P>.

        Remarks:
            The routine doesn't check if P and Q are either the identity, or if they belong to
            the same group (although the algorithm should fail in that case, because addition
            and equality are not defined).
            This can be a quite expensive routine, and makes use of the factorization of lq
            (achieved through ent.factor(...)).
            It is essentially intended to be called from inside EC.cardinality().
        """
        h=int(math.ceil(math.sqrt(lp)))

        # precompute baby steps
        step=ECPt.identity()
        babysteps=[step]
        for i in bigrange(1, h):
            step=step+P
            babysteps.insert(dlog.find_insertion_index(step, babysteps), step)

        # precompute giant steps
        hP=h*P
        step=ECPt.identity()
        giantsteps=[step]
        for i in bigrange(1, h):
            step=step+hP
            giantsteps.insert(dlog.find_insertion_index(step, giantsteps), step)

        # let's factor l_Q
        factorization=ent.factor(lq)
        d=lq

        # for each prime factor in the factorization
        for (p,e) in factorization:
            QQ=Q*(d//p)
            nextFactor=False

            while not nextFactor:

                # look for each QQ-R in babysteps.
                # if found, update d and try again with
                # the same factor. Otherwise, next prime
                found=False
                for (R, i) in giantsteps:
                    lookfor=QQ-R
                    if dlog.find_item(lookfor, babysteps)!=None:
                        d=d//p
                        found=True
                        break

                if not found:
                    nextFactor=True

        return d # the needed integer



    def computeOrder(self):
        """
        Computes the order of the point solving a discrete logarithm.

        Output:
            Integer m such that (m*self).isIdentity()==True.
            Outputs None if self is identity.

        Remarks:
            This routine uses Shank's algorithm (dlog.autoshanks(...)).
        """
        if self.isIdentity(): return None
        ub=self._EC._p+1+2*int(math.ceil(math.sqrt(self._EC._p)))
        return 1+dlog.autoshanks(self, -self, ub)




    def minOrderWithConstraints(self,b,c,ls,L):
        """
        This routine computes the minimum integer l such that l*self=O, with the
        following conditions:
            * l = ls   (mod L)
            * b <= l <= c
        It's essentially a modified baby-step giant-step algorithm.

        Input:
            b           The lower bound of the search interval
            c           The upper bound of the search interval
            ls          The output will be congruent to ls modulo L
            L           See ls.

        Output:
            An integer satisfying the conditions given above if found; None otherwise.

        Remarks:
            This routine is intended to be called by EC.cardinality().
        """
        bs=L*((b-ls)//L)+ls
        if bs<b: bs+=L
        if bs>c: return None

        cs=L*((c-ls)//L)+ls
        if cs>c:
            cs-=L
        elif cs+L<=c:
            cs+=L
        if cs<b: return None

        if bs==cs:
            # compute bs*P and check
            if (bs*self).isIdentity(): 
                return bs
            return None

        h=int(math.ceil(math.sqrt((cs-bs)/L+1)))

        # let's precompute small steps
        LP=L*self
        hLP=h*LP

        step=ECPt.identity()
        babysteps=[(step, 0)]

        for i in bigrange(1, h):
            step=step+LP
            # insert a tuple (step, i) in babysteps.
            # do it in such a way that the list ends up sorted
            el=(step, i)
            babysteps.insert(dlog.find_insertion_index(el, babysteps), el)

        solutions=[]
        # giant steps
        Q=-bs*self
        for i in bigrange(0, h):
            # look up in the babysteps
            found=dlog.find_tuple_by_1st_item(Q, babysteps)

            if found!=None:
                solutions.append((i,found[1]))
                if len(solutions)==2:
                    break

            # update Q value
            Q=Q-hLP

        #check if the solution has been found
        if len(solutions)==0: return None
        l=bs+L*(solutions[0][0]*h+solutions[0][1])
        if len(solutions)==2:
            m=(solutions[1][0]-solutions[0][0])*h+solutions[1][1]-solutions[0][1]
            return (l, m)

        return l
        



    def isIdentity(self):
        """
        Identity element has x=y=0 and None as EC. You should always use this function to
        check if an element is identity.

        Output:
            Boolean.

        Remarks:
            The fact that the identity element has None as EC is not mathematically precise,
            since this allows the addition of the identity to any point from any rational
            point group; in other words, all the identities in all the rational points groups
            are the same. On the other side, the identity is not an affine point, and therefore
            should be distinguished in some way; and it is the same point at infinity for all
            the elliptic curves. Besides, using a null elliptic curve as a "flag" to distinguish
            the identity has the advantage that, in case of elliptic curves with c==0, the point
            (0,0) is actually a point different from O (the identity); in this way we obtain
            the result we want without the need of subclassing ECPt.

        """
        return self._x==0 and self._y==0 and self._EC==None

    @staticmethod
    def identity():
        """
        Returns "the" identity element.

        Output:
            ECPt instance.

        Remarks:
            See ECPt.isIdentity(...) for details about the inner structure of the identity.
        """
        return ECPt(None, 0, 0)
