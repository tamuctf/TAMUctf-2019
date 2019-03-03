import requests

headers = {
	'User-Agent': 'Googlebot-Image/1.0'
}

r = requests.get('http://<IP or Domain>/robots.txt', headers=headers)
print r.text
