# buffer_overflow Secure Coding Scenario

### Setup
Add the following to the challenge_mapper in the Akeso config.py:
'echo_overflow':('echo_overflow',['overflow'],['echo_server'],3456)

Put contents of Exploits in the Exploits directory of Akeso.
Put contents of Services in the Services directory of Akeso.

### Solution
change gets(buf) to fgets(buf,128,stdin) and commit code to gitlab
