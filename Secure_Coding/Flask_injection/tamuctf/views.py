import requests
import json
import sys
from tamuctf import app
from flask import Flask, render_template, request, jsonify, render_template_string

@app.route('/')
@app.route('/index')
def index():
    
    return render_template('index.html')

@app.route('/science', methods=['POST'])
def science():
    try:
        chem1 = request.form['chem1']
        chem2 = request.form['chem2']
        template = '''<html>
        <div style="text-align:center">
        <h3>The result of combining {} and {} is:</h3></br>
        <iframe src="https://giphy.com/embed/AQ2tIhLp4cBa" width="468" height="480" frameBorder="0" class="giphy-embed" allowFullScreen></iframe></div>
        </html>'''.format(chem1, chem2)

        return render_template_string(template, dir=dir, help=help, locals=locals)
    except:
        return "Something went wrong"


