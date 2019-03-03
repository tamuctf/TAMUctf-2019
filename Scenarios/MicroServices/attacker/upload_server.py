import os
from flask import Flask, flash, request, redirect, url_for
from werkzeug.utils import secure_filename


app = Flask(__name__)

@app.route('/', methods=['GET', 'POST'])
def upload_file():
    if request.method == 'POST':
        # check if the post request has the file part
        if 'data' not in request.files:
            print(request.files)
            return 'No file part'
            return redirect(request.url)
        f = request.files['data']
        # if user does not select file, browser also
        # submit an empty part without filename
        if f.filename == '':
            return 'No selected file'
            return redirect(request.url)
        if f:
            filename = secure_filename(f.filename)
            f.save(os.path.join(app.config['UPLOAD_FOLDER'], filename))
            return ""

    return '''
    <!doctype html>
    <title>Upload new File</title>
    <h1>Upload new File</h1>
    <form method=post enctype=multipart/form-data>
      <input type=file name=file>
      <input type=submit value=Upload>
    </form>
    '''

if __name__ == '__main__':
    UPLOAD_FOLDER = '/root/Downloads/uploads'

    app.config['UPLOAD_FOLDER'] = UPLOAD_FOLDER
    app.secret_key = 'super secret key'
    app.config['SESSION_TYPE'] = 'filesystem'

    app.run(ssl_context='adhoc', host='0.0.0.0', port=443)
