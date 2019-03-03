from z3 import *

# structure from binary
# M#XX#-#XX#X-XXX#X-#XBB#-XX##
# 9 integers

def m(a, b):
  # modules z3
  return If(a % b >= 0,
    a % b,
    a % b + b)

s = Solver()

# 15 checks
# resource solution-36.blogspot.com/2014/08/solving-picoctf-2013-harder-serial-with.html
# 1) every 5 characters are followed by '-'

# 2) 9 input characters must be integers

v = []
for i in range(9):
  e = Int('v'+str(i))
  v.append(e)

  # define in numbers only
  s.add(e >= 0)
  s.add(e <= 9)

# 3) Constraints on int(1, 2, 4)
s.add((v[1]-1)/(2*v[0]) == 1)
s.add(v[1] > 7)
s.add(v[1]-v[0]+2-v[3] == 0)

# 4) Constraints on int(8, 9)
# need to add ascii offset for module math for 2 integers
s.add(m(v[7] + v[8] + 48*2,13) == 8) 

# 5) Constraints on (7, 8)
s.add(m(v[7] + v[6] + 48*2,22) == 18)

# 6) Constraints on (6, 7)
s.add(m(v[5] + v[6] + 48*2, 11) == 5)

# 7) Constraints on (6, 7, 9)
s.add(m(v[6] + v[8] + v[5] + 48*3, 26) == 4) 

# 8) Constraints on (1, 2, 3)
s.add(m(v[0] + 48 +(v[1]+48)*(v[2]+48), 41) == 5)

# 9) Constraints on (5, 9)
s.add(m(v[4] + v[8] + 48*2, 4) == 1)

# 10) Constraints on (2, 7)
s.add(m(v[6] + v[1] + 48*2, 4) == 3)

# 11) Middle letters are BB
# Sturcture in final solution

# 12) Constraints on (2, 4, 7)
t4 = 9
t3 = 7
t2 = 8 
print (t2+48 + (t4+48)*(t3+48)) %  10
s.add(m(v[2]+48 + (v[4]+48)*(v[3]+48), 10) == 5)

# 13) Constraints on (2, 5, 8)
s.add(m(v[1] + v[4] + v[7] - 18 + 48*3, 16) == 8)

# 14) Constraints on (4, 9)
s.add(m(v[3] + v[8] + 48*2, 2) == 1)

# 15) First letter is M
# Structure in final solution

print(s.check())
m = s.model()
print m