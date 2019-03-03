# Anti-Debugging
This is a 2 stage problem:
1. Getting around the anti-debugging mechanisms in place in the code. in this case, that comes in the form of checking for 0xCC bytes (breakpoints) in each running function when it is executed.
2. After the attacker has managed to evade those mechanisms, then they will be able to view the stack during the checkPass function by setting breakpoints, where they will find the password: "WattoSays\n".
3. when they input the password, it will hint that they should put that password into our server, which will then read the flag from a file and print it if the password is correct.
