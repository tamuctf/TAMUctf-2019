# Cr4ckZ33C0d3

## Challenge

`nc pwn.tamuctf.com 8189`

## Setup
Build the container with `docker build -t revz3 .` and run it with `docker run -it --rm revz3`

## Solution
The challenge details several constraints which should be loaded into a SMT/SAT solver such as Z3. The logic for these is detailed in the solution below. It's kind of ugly but it works.  
```python
from z3 import *

k0 = Int('s[0]')
k1 = Int('s[1]')
k2 = Int('s[2]')
k3 = Int('s[3]')
k4 = Int('s[4]')
k5 = Int('s[5]')
k6 = Int('s[6]')
k7 = Int('s[7]')
k8 = Int('s[8]')
k9 = Int('s[9]')
k10 = Int('s[10]')
k11 = Int('s[11]')
k12 = Int('s[12]')
k13 = Int('s[13]')
k14 = Int('s[14]')
k15 = Int('s[15]')
k16 = Int('s[16]')
k17 = Int('s[17]')
k18 = Int('s[18]')
k19 = Int('s[19]')
k20 = Int('s[20]')
k21 = Int('s[21]')
k22 = Int('s[22]')
k23 = Int('s[23]')
k24 = Int('s[24]')
k25 = Int('s[25]')
k26 = Int('s[26]')
k27 = Int('s[27]')
k28 = Int('s[28]')
k29 = Int('s[29]')

s = Solver()

z = ord('0')

# Check 01
s.add(k5 == ord('-'))
s.add(k11 == ord('-'))
s.add(k17 == ord('-'))
s.add(k23 == ord('-'))

# Check 02
s.add((k1-z) >= 0); s.add((k1-z) <=9);
s.add((k4-z) >= 0); s.add((k4-z) <=9);
s.add((k6-z) >= 0); s.add((k6-z) <=9);
s.add((k9-z) >= 0); s.add((k9-z) <=9);
s.add((k15-z) >= 0); s.add((k15-z) <=9);
s.add((k15-z) >= 0); s.add((k15-z) <=9);
s.add((k18-z) >= 0); s.add((k18-z) <=9);
s.add((k22-z) >= 0); s.add((k22-z) <=9);
s.add((k27-z) >= 0); s.add((k27-z) <=9);
s.add((k28-z) >= 0); s.add((k28-z) <=9);

# check 03
s.add((k4-z) == ((k1-z)*2+1))
s.add((k4-z) > 7)
s.add(k9 == ((k4-(k1-z))+2))

# Check 04
s.add((k27+k28)%13 == 8)

# Check 05
s.add((k27+k22)%22 == 18)

# Check 06
s.add((k18 + k22)%11 == 5)

# Check 07
s.add((k28+k22+k18)%26 == 4)

# Check 08
s.add((k1 + k4*k6)%41 == 5)

# Check 09
s.add((k15-k28)%4 == 1)

# Check 0A
s.add((k4+k22)%4 == 3)

# Check 0B
s.add(k20 == ord('B'))
s.add(k21 == ord('B'))

# Check 0C
s.add((k6+k15*k9)%10 == 1)

# Check 0D
s.add((k4+k15+k27-18)%16 == 8)

# Check 0F
s.add(k0 == ord('M'))

print s.check()
m = s.model()
l = str(m).replace(' ', '').replace('\n', '').split(',')
print l
```
