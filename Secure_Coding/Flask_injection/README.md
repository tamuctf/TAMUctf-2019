# FlaskInjection

## Setup
Make sure in the file `/etc/gitlab-runner/config.toml` the line `pull_policy = "if-not-present"` is added under the `[runners.docker]` section.  
Build the docker image locally with `docker build -t messy/flaskinjection .`  
Copy `flask_exploit.py` and `FlaskServer.py` to their respective locations.  
Add `'flask_server': ('flask_server', ['flask_exploit'], ['FlaskServer'], 8000)` to the config file.

## Solution
Modify the route in `views.py` to be:
```python
def science():
    try:
        chem1 = request.form['chem1']
        chem2 = request.form['chem2']
        template = '''<html>
        <div style="text-align:center">
        <h3>The result of combining {{ chem1 }} and {{ chem2 }} is:</h3></br>
        <iframe src="https://giphy.com/embed/AQ2tIhLp4cBa" width="468" height="480" frameBorder="0" class="giphy-embed" allowFullScreen></iframe></div>
        </html>'''

        return render_template_string(template, dir=dir, help=help, locals=locals, chem1=chem1, chem2=chem2)
    except:
        return "Something went wrong"
```
