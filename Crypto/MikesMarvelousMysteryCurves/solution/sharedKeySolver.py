#!/usr/bin/env python
import sys
sys.path.append('./PohligHellman')
from ec import *
from dlog import pohlighellman
print("Enter elliptic curve parameters in the form y^2 = x^3 + ax + b on the finite field F")
a = int(input("a: "))
b = int(input("b: "))
F = int(input("F: "))
curve = EC(0,a,b,F)

print("Enter the agreed starting point on the curve as follows.")
Gx = int(input("Gx: "))
Gy = int(input("Gy: "))
G = ECPt(curve,Gx,Gy)

print("Enter the first public key point Qa")
Qax = int(input("Qax: "))
Qay = int(input("Qay: "))
Qa = ECPt(curve,Qax,Qay)

print("Enter the second public key point Qb")
Qbx = int(input("Qbx: "))
Qby = int(input("Qby: "))
Qb = ECPt(curve,Qbx,Qby)

order = Qa.computeOrder()
print("Order:",order)

print("----------------------------------------")
private_key_a = pohlighellman(G,Qa,order)
#private_key_b = pohlighellman(G,Qb,order)
print("Private Key:",private_key_a)
print("----------------------------------------")

shared_key = private_key_a * Qb
#shared_key = private_key_b * Qa
print("Shared Key:",str(shared_key))
print("----------------------------------------")
