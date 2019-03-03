from tamuctf import app

#app = create_app()
app.run(debug=True, threaded=True, host="0.0.0.0", port=8000)
